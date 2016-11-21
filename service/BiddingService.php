<?php

namespace app\service;

use app\models\Bidding;
use app\models\BiddingRules;
use app\models\BiddingRulesType;
use app\models\BiddingRulesItem;
use app\models\AmazonService;

class BiddingService
{

    const GT = 'gt';
    const LT = 'lt';
    const EQ = 'eq';
    const NONE = 'none';
    const BOTH = 'both';
    const AFTER_LE = 'after_le';

    /**
     * 根据规则id获取规则详细信息
     * @param  integer $rulesId 规则id
     * @return array|null 数据存在则返回数据数组，不存在返回null
     */
    public static function getRulesById($rulesId)
    {
        return BiddingRules::find()
                ->with(['types', 'types.items'])
                ->where(['id' => $rulesId])
                ->asArray()->one();
    }

    public static function getItems($rulesId, $compare)
    {
        return BiddingRulesItem::find()
                ->with('type')
                ->where(['rules_id' => $rulesId, 'compare' => $compare])
                ->asArray()->all();
    }

    public static function getBidding($bidId)
    {
        return Bidding::find()
                ->with(
                    [
                        'sku' => function ($qsku) {
                            $qsku->select(['sku', 'sale_price', 'shipping_fee', 'current_price', 'price', 'fulfillment_channel']);
                        }, 
                        'goods' => function ($qgoods) {
                            $qgoods->select(['fulfilled_by', 'asin', 'title']);
                        } ,
                        'rules', 'rules.types', 'rules.types.items',
                    ]
                )
                ->where(['id' => $bidId])
                ->asArray()->one();
    }

    /**
     * 调用亚马逊接口获取最多20位竞争对手的价格相关信息
     * @param  integer $shopId    店铺id
     * @param  string  $sellerSku 商品sku
     * @return false|array 成功返回一个数组，失败或数据异常返回false
     */
    public static function getLowestOffers($shopId, $sellerSku)
    {
        $amazonService = new AmazonService();
        $result = $amazonService->getLowestPricedOffersForSKU($shopId, $sellerSku, 'new');
        if (!is_array($result)) {
            return false;
        }

        $result = $result['GetLowestPricedOffersForSKUResponse']['GetLowestPricedOffersForSKUResult'];
        // 获取最低价及黄金购物车价格概述
        $summary = $result['Summary'];
        if ($summary['TotalOfferCount'] <= 0) {
            return false;
        }

        $lists = [];
        // 竞争数量
        $lists['competitorsCount'] = $summary['TotalOfferCount'] - 1;
        // 最低价格
        $lists['lowestPrice'] = [];
        if (isset($summary['LowestPrices'])) {
            $lists['lowestPrice']['price'] = $summary['LowestPrices']['LowestPrice']['ListingPrice']['Amount'];
            $lists['lowestPrice']['fare'] = $summary['LowestPrices']['LowestPrice']['Shipping']['Amount'];
            $lists['lowestPrice']['amount'] = sprintf('%0.2f', $lists['lowestPrice']['price'] + $lists['lowestPrice']['fare']);
        }
        // 黄金购物车价格
        $lists['buyboxPrice'] = [];
        if (isset($summary['BuyBoxPrices'])) {
            $lists['buyboxPrice']['price'] = $summary['BuyBoxPrices']['BuyBoxPrice']['ListingPrice']['Amount'];
            $lists['buyboxPrice']['fare'] = $summary['BuyBoxPrices']['BuyBoxPrice']['Shipping']['Amount'];
            $lists['buyboxPrice']['amount'] = sprintf('%0.2f', $lists['buyboxPrice']['price'] + $lists['buyboxPrice']['fare']);
        }

        // 竞争对手offer列表
        $lists['competitorsOffers'] = [];
        // 自己是否有黄金购物车
        $lists['selfIsBuybox'] = false;
        
        if ($lists['competitorsCount'] > 0) {
            $offers = $result['Offers']['Offer'];
        } else {
            $offers = array($result['Offers']['Offer']);
        }
        foreach ($offers as $offer) {
            $one = [];
            $one['isBuybox'] = isset($offer['IsBuyBoxWinner']) && $offer['IsBuyBoxWinner'] == 'true' ? true : false;
            $one['isFBA'] = $offer['IsFulfilledByAmazon'] == 'true' ? true : false;
            $one['isFeaturedMerchant'] = isset($offer['IsFeaturedMerchant']) && $offer['IsFeaturedMerchant'] == 'true' ? true : false;
            $one['price'] = $offer['Shipping']['ListingPrice']['Amount'];
            $one['fare'] = $offer['Shipping']['Amount'];
            $one['amount'] = sprintf('%0.2f', $one['price'] + $one['fare']);

            // 自己
            if (isset($offer['MyOffer']) && $offer['MyOffer'] == 'true') {
                // 判断自己是否是黄金购物车
                if (isset($offer['IsBuyBoxWinner']) && $offer['IsBuyBoxWinner'] == 'true') {
                    $lists['selfIsBuybox'] = true;
                }
                $one['myOffer'] = true;
            } else {
                $one['myOffer'] = false;
            }

            $lists['competitorsOffers'][] = $one;
        }

        return $lists;
    }

    public static function modifyBiddingById($id, array $attributes = [])
    {
        $bidding = Bidding::findOne($id);
        if (!$bidding) {
            return false;
        }
        foreach ($attributes as $key => $value) {
            $bidding->$key = $value;
        }

        return $bidding->save();
    }

}