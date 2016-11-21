<?php

namespace app\jobs;

use app\models\Bidding;
use app\models\Store;
use app\models\GoodsSyncSku;
use app\models\BiddingLog;
use app\service\BiddingComputeService;
use app\service\BiddingService;
use app\libraries\MyHelper;
use app\assets\Amazon;

class GoodsBidding extends BasicWorker
{

    public static function queueName()
    {
        return 'GoodsBidding';
    }

    public function perform()
    {
        $now = time();
        $id = $this->args['id'];
        try {
            // step1 获取价格
            $computeService = new BiddingComputeService($id);
            if (!$computeService->loadLowestOffers()) {
                throw new \Exception($computeService->get('errorMsg'));
            }
            $price = $computeService->getComputePrice();
            $lowestOffers = $computeService->get('lowestOffers');
            if ($price === false || $price <= 0) {
                var_dump($price);
                return;
            }

            // step2 更新价格到亚马逊
            $ret = $this->updatePrice($computeService->getShopId(), $computeService->getSku(), $price);
            if (!$ret) {
                throw new \Exception("$id update price failed.");
            }
            
            // step3 更新(sea_bidding表)数据
            $data = [
                'last_modifyprice_at' => $now,
                'my_price' => $price,
            ];
            $data['competitors_count'] = $lowestOffers['competitorsCount'];
            if ($lowestOffers['lowestPrice']) {
                $data['lower_price'] = $lowestOffers['lowestPrice']['price'];
                $data['lower_price_far'] = $lowestOffers['lowestPrice']['fare'];
            }
            if ($lowestOffers['buyboxPrice']) {
                $data['buybox_price'] = $lowestOffers['buyboxPrice']['price'];
                $data['buybox_price_fare'] = $lowestOffers['buyboxPrice']['fare'];
            }

            $transaction = \Yii::$app->db->beginTransaction();
            if (BiddingService::modifyBiddingById($id, $data)) {
                $r = GoodsSyncSku::updateAll(['price' => $price, 'current_price' => $price], ['id' => $computeService->getSkuId()]);
                if ($r) {
                    $transaction->commit();
                }
            } else {
                $adjustStatus = 1;
                $transaction->rollBack();
                $this->_errorMsg[] =  "更新失败";
                $this->_errorMsg[] = json_encode($data);
            }

            // step4 调价之后记录调价日志
            $logData = [
                'modify_at' => $now,
                'shop_id' => $computeService->getShopId(),
                'date' => date('Y-m-d', $now),
                'goods_title' => $computeService->getTitle(),
                'asin' => $computeService->getAsin(),
                'sku' => $computeService->getSku(),
                'mix_price' => $computeService->getMinPrice(),
                'max_price' => $computeService->getMaxPrice(),
                'rules_name' => $computeService->getRulesName(),
                'before_price' => $computeService->getAmountPrice(),
                'after_price' => $price,
                'change_price' => ($price - $computeService->getAmountPrice()),
                'adjust_status' => isset($adjustStatus) ? $adjustStatus : 0,
            ];
            $this->saveBiddingLog($logData);
        } catch (\Exception $e) {
            $this->_errorMsg[] = (string)$e;
        }

    }

    private function saveBiddingLog($logData)
    {
        $model = new BiddingLog();
        foreach ($logData as $key => $value) {
            $model->$key = $value;
        }

        return $model->save();
    }

    private function updatePrice($shopId, $sku, $price)
    {
        $shop = Store::find()->with('site')
                ->where(['id' => $shopId])
                ->andFilterWhere(['is_deleted' => 'N'])
                ->asArray()
                ->one();

        $subData = [
            'Message' =>[
                'MessageID' => 1,
                'Price' =>[
                    'SKU' => $sku,
                    'StandardPrice' => $price,
                ],
            ],
        ];

        $serviceUrl = 'https://' . $shop['site']['api_host'];
        $xml = MyHelper::arrayBuildXml($shop['merchant_id'], array($subData), 'Price');

        $newapi = new Amazon($shop['merchant_id'], $shop['accesskey_id'],
            $shop['secret_key'], $serviceUrl,$shop['marketplace_id']);
        $result = $newapi->changePrice($xml);

        return $result;
    }
}