<?php

namespace app\service;

use Exception;
use yii\helpers\ArrayHelper;

class BiddingComputeService
{
    private $errorMsg;
    private $bidData;
    private $lowestOffers;

    private $lowestPrice;
    private $featuredLowestPrice;
    private $noFeaturedLowestPrice;
    private $FBALowestPrice;
    private $FBMLowestPrice;
    private $secondPlacePrice;
    private $randPrice = [];

    private $selfIsBuybox = false;
    private $hasCompetitor = true;
    private $lowestPriceIsFBA;
    private $compare;

    /**
     * 构造函数初始化调价商品数据
     * @param integer $bidId 调价商品id
     */
    public function __construct($bidId)
    {
        $bidData = BiddingService::getBidding($bidId);
        if (empty($bidData)) {
            throw new \Exception("调价商品数据不存在或已删除");
        }
        if (empty($bidData['sku'])) {
            throw new \Exception("sku不存在");
        }
        if ($bidData['rules_id'] <= 0) {
            throw new \Exception("未绑定规则");
        }
        if ($bidData['status'] <= 0) {
            throw new \Exception("未开启监控");
        }

        $this->set('bidData', $bidData);
    }

    /**
     * 获取成员属性
     * @param  string $key 成员属性名称
     * @return mix 
     */
    public function get($key)
    {
        if (!property_exists($this, $key)) {
            throw new \Exception('Getting unknown property: ' . get_class($this) . '::' . $key);
        }
        return $this->$key;
    }

    /**
     * 设置成员属性
     * @param string $key   名称
     * @param string $value 值
     */
    public function set($key, $value)
    {
        if (!property_exists($this, $key)) {
            throw new \Exception('Setting unknown property: ' . get_class($this) . '::' . $key);
        }
        $this->$key = $value;
    }

    /**
     * 获取sea_goods_sync_sku中的sku字段值
     */
    public function getSku()
    {
        return $this->bidData['sku']['sku'];
    }

    /**
     * 获取店铺id
     */
    public function getShopId()
    {
        return $this->bidData['shop_id'];
    }

    /**
     * 获取设置商品的最小价格
     */
    public function getMinPrice()
    {
        return $this->bidData['mix_price'];
    }

    /**
     * 获取设置商品的最大价格
     */
    public function getMaxPrice()
    {
        return $this->bidData['max_price'];
    }

    /**
     * 获取商品当前的邮费
     */
    public function getPriceFare()
    {
        return $this->bidData['my_price_fare'];
    }

    /**
     * 获取商品当前的asin值
     */
    public function getAsin()
    {
        return $this->bidData['goods']['asin'];
    }

    /**
     * 获取商品标题
     */
    public function getTitle()
    {
        return $this->bidData['goods']['title'];
    }

    /**
     * 获取规则名称
     */
    public function getRulesName()
    {
        return $this->bidData['rules']['name'];
    }

    /**
     * 获取商品当前的总价格（price + fare）
     */
    public function getAmountPrice()
    {
        return ($this->bidData['my_price'] + $this->getPriceFare());
    }

    /**
     * 获取规则信息
     * @return array 返回规则相关数据的数组
     */
    public function getRules()
    {
        return $this->bidData['rules'];
    }

    /**
     * 获取规则类型，过滤未开启的类型
     * @return array
     */
    public function getTypes()
    {
        $types = $this->bidData['rules']['types'];
        $tmpArr = [];
        foreach ($types as $type) {
            if (!$type['is_open']) {
                continue;
            }
            $tmp = [];
            foreach ($type['items'] as $item) {
                $tmp[$item['compare']] = $item;
                $type['items'] = $tmp;
            }
            $tmpArr[$type['type']] = $type;
        }
        return $tmpArr;
    }

    /**
     * 获取物流方式
     */
    public function getFulfiledby()
    {
        return $this->bidData['sku']['fulfillment_channel'];
    }

    public function getSkuId()
    {
        return $this->bidData['sku_id'];
    }

    /**
     * 加载用于比价的offers，且初始化必要的属性
     * @return boolean 加载成功返回true，失败返回false
     */
    public function loadLowestOffers()
    {
        $ret = BiddingService::getLowestOffers($this->getShopId(), $this->getSku());
        if (!$ret) {
            return $this->_setErrorMsg("获取亚马逊数据失败");
        }

        // 无竞争者
        if ($ret['competitorsCount'] <= 0) {
            $this->set('hasCompetitor', false);
        }
        $this->set('selfIsBuybox', $ret['selfIsBuybox']);
        $this->set('lowestOffers', $ret);
        $myAmount = $this->getAmountPrice();
        $competitorsOffers = $ret['competitorsOffers'];
        $competitors = explode(',', $this->bidData['rules']['competitors']);
        $count = 0; // 竞争者记数

        foreach ($competitorsOffers as $key => $offer) {
            if ($offer['myOffer']) {
                continue;
            }
            $amount = $offer['amount'];
            /*********** 这里的条件设置是针对自己是黄金购物车 *************/
            // 特色卖家( Amazon、FBA 和 FBM) & 非特色卖家
            if ($offer['isFeaturedMerchant']) {
                $this->_setLowestPrice('featuredLowestPrice', $amount);
                // 第二顺位价格
                if ($amount > $myAmount && $this->secondPlacePrice === null) {
                    $this->set('secondPlacePrice', $amount);
                }
            } else {
                $this->_setLowestPrice('noFeaturedLowestPrice', $amount);
            }
            // FBA & FBM
            if ($offer['isFBA']) {
                $this->_setLowestPrice('FBALowestPrice', $amount);
            } else {
                $this->_setLowestPrice('FBMLowestPrice', $amount);
            }
            /*********** end *************/

            /************* 智能调价竞争对手过滤 *************/ 
            // 排除非特色卖家
            if (!$offer['isFeaturedMerchant'] && !in_array('non_featured_sellers', $competitors)) {
                continue;
            }
            // 排除FBA卖家
            if ($offer['isFBA'] && !in_array('FBA', $competitors)) {
                continue;
            }
            // 排除FBM卖家
            if (!$offer['isFBA'] && !in_array('FBM', $competitors)) {
                continue;
            }
            /************* end *************/

            // 位于最大价格与最小价格之间的价格
            if ($amount >= $this->getMinPrice() && $amount <= $this->getMaxPrice()) {
                $this->randPrice[] = $amount;
            }

            // 设置最低价格
            $this->_setLowestPrice('lowestPrice', $amount, $offer['isFBA']);
            $count++;
        }
        // 条件过滤之后无竞争对手
        if (!$count) {
            $this->set('hasCompetitor', false);
        }

        $this->_setDefaultCompare();
        return true;
    }

    /**
     * 获取计算之后的商品价格
     * 价格在商品设置的最大值&最小值之间
     * @return mixed 不需要调价返回false，需要调价则返回调价的最终价格（减掉邮费）
     */
    public function getComputePrice()
    {
        $buyboxSet = $this->bidData['rules']['buybox_set'];
        // 黄金购物车
        if ($this->selfIsBuybox && $buyboxSet != 5) {
            // 无竞争者则不修改黄金购物车的价格
            if (!$this->hasCompetitor) {
                return false;
            }
            if ($buyboxSet == 4) {
                return false;
            }
            return $this->_getBuyboxPrice($buyboxSet);
        }

        // 非黄金购物车
        return $this->_getIntelligentPrice();
    }

    /**
     * 获取比较符
     * @param  float  $lowestPrice 用于比较的最低价格
     * @return strint 返回用于比较的比较符
     */
    private function _getCompare($lowestPrice)
    {
        if ($lowestPrice == $this->getMinPrice()) {
            return BiddingService::EQ;
        }
        if ($lowestPrice > $this->getMinPrice()) {
            return BiddingService::GT;
        }
    }

    /**
     * 设置默认的比较符
     */
    private function _setDefaultCompare()
    {
        // 无竞争
        if (!$this->hasCompetitor) {
            return $this->set('compare', BiddingService::NONE);
        }
        // 当竞争对手高于最小价格
        if ($this->lowestPrice > $this->getMinPrice()) {
            return $this->set('compare', BiddingService::GT);
        }

        if ( count($this->randPrice) > 0) {
            // 当竞争者价格小于您的最小价格，且其他竞争者有介于最小&最大价格之间
            if ($this->lowestPrice < $this->getMinPrice()) {
                return $this->set('compare', BiddingService::LT);
            }
            // 当竞争者价钱等于您的最小价格，且其他竞争者有介于最小&最大价格之间
            if ($this->lowestPrice == $this->getMinPrice) {
                return $this->set('compare', BiddingService::EQ);
            }
        } else {
            // 当没有任何竞争者在您的最小价格&最大价格之间
            return $this->set('compare', BiddingService::BOTH);
        }

    }

    /**
     * 获取智能调价的价格（非进阶设置）
     * @return mix 不调价返回false，反之则返回调价之后的价格（减掉运费之后的）
     */
    private function _getIntelligentPrice()
    {
        $types = $this->getTypes();
        // 获取进阶的设置
        if ($this->_checkAdvancedSetting()) {
            $items = $types[$this->_getAdvancedType()]['items'];
        }
        // 获取智能basic设置
        else {
            $items = array_merge($types['basic']['items'], $types['protected']['items']);
        }
        $price = $this->_getPrice($items, $this->lowestPrice, $this->compare);
        // 执行保障设置
        if ($price <= $this->getMinPrice()) {
            $price = $this->_getPrice($items, $price, BiddingService::AFTER_LE);
        }

        if ($price == $this->getAmountPrice()) {
            return false;
        }
        // 保证价格在最大&最小范围之间
        $price = min(max($price, $this->getMinPrice()), $this->getMaxPrice());

        return ($price - $this->getPriceFare());
    }

    /**
     * 获取对应比较符的价格（核心代码）支持自动竞价递归比较
     * @param  array  $items       比较规则详细信息
     * @param  float  $lowestPrice 用于比较的最低价
     * @param  string $compare     比较符
     * @throws Exception 数据不完整则抛出异常
     * @return mix  不竞价返回false，反之返回比较之后的价格
     */
    private function _getPrice($items, $lowestPrice, $compare)
    {
        $randPrice = $this->_getRandPrice();
        $prices = [
            'auto' => $randPrice,
            'stop' => false,
            'min' => $this->getMinPrice(),
            'max' => $this->getMaxPrice(),
            'competitor' => $lowestPrice,
        ];
        
        if (!isset($items[$compare])) {
            throw new \Exception("error: {$this->bidData['rules']['id']} " . var_export($items, 1) . "not exists $compare");
        }

        $item = $items[$compare];
        if (!empty($item['options']) && $item['options'] != 'customize') {
            $price = $prices[$item['options']];
            // 自动竞争核心代码
            if ($item['options'] == 'auto') {
                // 自动竞争没有下一位竞争者的时候，价格使用当前竞争者的价格
                if (empty($randPrice)) {
                    $price = $lowestPrice;
                } else {
                    $price = $this->_getPrice($items, $price, $this->_getCompare($price));
                }
            }
        } else {
            if (empty($item['item'])) {
                throw new \Exception("error: {$this->bidData['rules']['id']} option & item not Also null.");
            }
            $price = $this->_operationPrice($prices[$item['item']], $item['symbol'], $item['value'], $item['math']);
        }
        
        return $price;
    }

    /**
     * 获取竞争者高于最小价格 & 低于最大价格之间的最小价格
     * @return mix 当没有下一位价格时返回null，有则返回price
     */
    private function _getRandPrice()
    {
        $this->randPrice = array_unique($this->randPrice);
        sort($this->randPrice);
        return array_shift($this->randPrice);
    }

    /**
     * 检测是否需要智能调价设定中的进阶设定
     * @return boolean
     */
    private function _checkAdvancedSetting()
    {
        $adType = $this->_getAdvancedType();
        $types = $this->getTypes();
        // 是否开启对应的进阶设置
        if (!isset($types[$adType])) {
            return false;
        }
        // 是否存在满足但前compare条件的item
        $items = $types[$adType]['items'];
        if (empty($items) || !isset($items[$this->compare])) {
            return false;
        }
        return true;
    }

    /**
     * 获取进阶设定中比较的类型
     * @return string
     */
    private function _getAdvancedType()
    {
        $isFBA = $this->getFulfiledby() == 'AMAZON' ? true : false;
        if ($isFBA && $this->lowestPriceIsFBA) {
            $type = 'fba_vs_fba';
        } elseif ($isFBA && !$this->lowestPriceIsFBA) {
            $type = 'fba_vs_fbm';
        } elseif (!$isFBA && $this->lowestPriceIsFBA) {
            $type = 'fbm_vs_fba';
        } else {
            $type = 'fbm_vs_fbm';
        }
        return $type;
    }

    /**
     * 获取购物车竞价的价格
     * @param  integer $buyboxSet 进阶设定类型值
     * @return mix  false|price
     */
    private function _getBuyboxPrice($buyboxSet)
    {
        $price = 0;
        $rules = $this->getRules();
        $amount = $this->getAmountPrice();

        // 降低或提高黄金购物车价格
        if ($buyboxSet == 1) {
            
            if ($amount < $this->featuredLowestPrice) {
                $price = $this->_operationPrice($this->featuredLowestPrice, '-', $rules['buybox_set_value2'], $rules['buybox_set_math2']);
            } elseif ($amount > $this->featuredLowestPrice && $this->featuredLowestPrice > 0) {
                $price = $this->_operationPrice($this->featuredLowestPrice, '-', $rules['buybox_set_value1'], $rules['buybox_set_math1']);
            }
        }
        // 提高我的黄金购物车价格最大化利润
        elseif ($buyboxSet == 2) {

            if ($this->secondPlacePrice > 0) {
                $price =  $this->_operationPrice($this->secondPlacePrice, '-', $rules['buybox_set_value1'], $rules['buybox_set_math1']);
            }
        }
        // 降低我的黄金购物车内价格以保持竞争力 
        elseif ($buyboxSet == 3) {

            if ($amount > $this->featuredLowestPrice) {
                $price = $this->_operationPrice($this->featuredLowestPrice, '-', $rules['buybox_set_value1'], $rules['buybox_set_math1']);
            }
        }

        // 当调整后的价格高于最大价格
        if ($price > $this->getMaxPrice()) {
            // 不调价
            if ($rules['buybox_item'] == 'stop') {
                return false;
            }
            // 使用最大值
            if ($rules['buybox_item'] == 'max') {
                $price = $this->getMaxPrice();
            }
        }

        if ($price <= 0 || $amount == $price) {
            return false;
        }
        // 保证最小值
        $price = max($price, $this->getMinPrice());

        return ($price - $this->getPriceFare());
    }

    /**
     * 设置最小价格
     * @param string  $key   名称
     * @param float   $price 价格
     * @param boolean $isFBA 是否设置最小价格是否是FBA（默认为null，不设置）
     */
    private function _setLowestPrice($key, $price, $isFBA = null)
    {
        $flag = false;
        if ($this->get($key) !== null) {
            $price = min($this->get($key), $price);
            if ($price < $this->get($key)) {
                $flag = true;
            }
        } else {
            $flag = true;
        }
        if ($flag && $isFBA !== null) {
            $this->set('lowestPriceIsFBA', $isFBA);
        }
        $this->set($key, $price);
    }

    /**
     * 设置错误消息
     */
    private function _setErrorMsg($msg)
    {
        $this->errorMsg = $msg;
        return false;
    }

    /**
     * 计算操作价格
     * @param  float   $price  被计算的价格
     * @param  string  $symbol 运算符
     * @param  float   $value  值
     * @param  string  $math   匹配
     * @return float   price
     */
    private function _operationPrice($price, $symbol, $value = 0.01, $math = '$')
    {
        if ($math == '%') {
            $vlaue = ($value/100) * $price;
        }
        switch ($symbol) {
            case '-':
                $newPrice = $price - $value;
                break;
            case '+':
                $newPrice = $price + $value;
                break;
        }
        return $newPrice;
    }

}