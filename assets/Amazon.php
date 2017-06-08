<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */



// test
namespace app\assets;

require_once __DIR__ . "/amazon/src/MarketplaceWebService/Samples/AmazonCommon.php";
require_once __DIR__ . "/amazonProduct/src/MarketplaceWebServiceProducts/Samples/AmazonProductCommon.php";
use app\libraries\MyHelper;
/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Amazon{

	    public $seller_id;
	    public $access_key;
	    public $secret_access;
	    public $feed;
	    private $_result;
	    public $_serviceUrl;
	    public $_marketplaceId;


	 public function __construct($seller_id, $access_key, $secret_access, $serviceUrl, $marketplaceId)
    {	
    	$this->seller_id = $seller_id;
    	$this->access_key = $access_key;
    	$this->secret_access = $secret_access;
    	$this->_serviceUrl = $serviceUrl;
        $this->_marketplaceId = $marketplaceId;
    }

  	public function pubToAmazon($feed){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $this->_result = $amazon->SubmitFeed($feed);
		return  $this->_result;
  	}
  	 //上传商品成功，根据UPC获取ASIN等商品信息

    //同步商品时，根据ASIN获取商品信息
   	public function getUpSuccessReturnValue($marketplaceId = 'ATVPDKIKX0DER', $idType = 'ASIN', $idList = 'B01HROM022'){
   		$serviceUrl = $this->_serviceUrl."/Products/2011-10-01";
		$amazon = new \AmazonProductCommon($this->seller_id, $this->access_key, $this->secret_access, $serviceUrl);
		$outArray = array();
		//$num = 5;
		//for ($i=0; $i < count($idList)/$num; $i++) {
			//$idNewList = array_slice($idList,$i*$num,$num);
	    	$resultXml = $amazon->getUpSuccessReturnValue($marketplaceId, $idType, $idList);
	    	$outArray = MyHelper::xml_to_array(preg_replace('/ns2\:/', '', $resultXml));
	    	//var_dump($resultOutArray);die;
	    	//array_push($outArrays, $resultOutArray);
		//}
	    if(empty($outArray)){
	    	return null;
	    }else{
	    	if(isset($outArray['GetMatchingProductForIdResponse']['GetMatchingProductForIdResult']) && is_array($outArray['GetMatchingProductForIdResponse']['GetMatchingProductForIdResult'])){
		    	foreach ($outArray['GetMatchingProductForIdResponse']['GetMatchingProductForIdResult'] as $value) {
				    if(isset($value['Error'])){
				        $outData = null;
				    } else {
				        $outData = array(
				    		'ASIN' => isset($value['Product']['Identifiers']['MarketplaceASIN']['ASIN'])? $value['Product']['Identifiers']['MarketplaceASIN']['ASIN'] : null,
				            'MarketplaceId' => isset($value['Product']['Identifiers']['MarketplaceASIN']['MarketplaceId']) ? $value['Product']['Identifiers']['MarketplaceASIN']['MarketplaceId'] : null,
				            'BulletPoint' => isset($value['Product']['AttributeSets']['ItemAttributes']['Feature'])? $value['Product']['AttributeSets']['ItemAttributes']['Feature'] : null,
				            'ListPrice' => isset($value['Product']['AttributeSets']['ItemAttributes']['ListPrice']['Amount']) ? $value['Product']['AttributeSets']['ItemAttributes']['ListPrice']['Amount'] : null,
				            'SmallImage' => isset($value['Product']['AttributeSets']['ItemAttributes']['SmallImage']['URL'])? $value['Product']['AttributeSets']['ItemAttributes']['SmallImage']['URL'] : null,
				            'Title' => isset($value['Product']['AttributeSets']['ItemAttributes']['Title']) ? $value['Product']['AttributeSets']['ItemAttributes']['Title'] : null,
				            'ParentASIN' => null
				            );
			        

				        if(isset($value['Product']['Relationships']['VariationParent'])){
				           	$parant = $value['Product']['Relationships']['VariationParent'];
				           	if(!empty($parant)){
				            	$outData['ParentASIN'] = isset($parant['Identifiers']['MarketplaceASIN']['ASIN']) ? $parant['Identifiers']['MarketplaceASIN']['ASIN'] : null;
				           	}
				        }
				    }
				}
			}else{
				return null;
			}

		}
		// }
		//print_r($outData);die;
		return $outData;

	    // $returnArrLength = count($outData);
	    // $okarr = array();
	    // for ($i=0; $i < $returnArrLength ; $i++) { 
	    // 	$startArray = $outData[$i];
		   //  $okarr = array_merge($okarr,$startArray);
	    // }
	    // //var_dump($okarr);die;
    	// return $okarr;
   	}

   	public function getPriceAndShippingForSKU($marketplaceId = 'ATVPDKIKX0DER', $sku = 'ASIN'){
   		$serviceUrl = $this->_serviceUrl."/Products/2011-10-01";
		$amazon = new \AmazonProductCommon($this->seller_id, $this->access_key, $this->secret_access, $serviceUrl);
		$outArray = $outData = array();
	    $resultXml = $amazon->getPriceAndShippingForSKU($marketplaceId, $sku);
	    $outArray = MyHelper::xml_to_array(preg_replace('/ns2\:/', '', $resultXml));
	    if(empty($outArray)){
	    	return null;
	    }else{
	    	if(isset($outArray['GetMyPriceForSKUResponse']['GetMyPriceForSKUResult']) && is_array($outArray['GetMyPriceForSKUResponse']['GetMyPriceForSKUResult'])){
		    	foreach ($outArray['GetMyPriceForSKUResponse']['GetMyPriceForSKUResult'] as $value) {
				    if(isset($value['Error'])){
				        $outData = null;
				    } else {
				    	if(!isset($value['Offers']) || !is_array($value['Offers'])){
				    		return null;
				    	}
				    	foreach($value['Offers'] as $val){
				    		if( isset($val['SellerSKU']) ){
				    			$outData['price'] = isset($val['BuyingPrice']['ListingPrice']['Amount']) ? $val['BuyingPrice']['ListingPrice']['Amount'] : 0;
						        $outData['shipping_fee'] = isset($val['BuyingPrice']['Shipping']['Amount']) ? $val['BuyingPrice']['Shipping']['Amount'] : 0;
						        $outData['fulfillment_channel'] = isset($val['FulfillmentChannel']) ? $val['FulfillmentChannel'] : 'MERCHANT';
				    		}else{
				    			foreach($val as $one){
				    				if($one['SellerSKU'] == $sku){
						        		$outData['price'] = isset($one['BuyingPrice']['ListingPrice']['Amount']) ? $one['BuyingPrice']['ListingPrice']['Amount'] : 0;
						        		$outData['shipping_fee'] = isset($one['BuyingPrice']['Shipping']['Amount']) ? $one['BuyingPrice']['Shipping']['Amount'] : 0;
						        		$outData['fulfillment_channel'] = isset($one['FulfillmentChannel'])?$one['FulfillmentChannel'] : 'MERCHANT';
						        	}
				    			}
				    		}
				    	}
				    }
				}
			}else{
				return null;
			}
	    }
	    return $outData;
   	}

    //根据类型，发送一个获取信息的请求
	public function requestReport($reportType = '_GET_MERCHANT_LISTINGS_DATA_BACK_COMPAT_'){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $this->_result = $amazon->requestReport($reportType);
	    return $this->_result;
	}

    //获取GeneratedReportId
	public function getReportRequestList(){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $result = $amazon->getReportRequestList();
	    $generatedReportId = null ;
	    foreach ($result as $key => $value) {
            if($value['ReportType'] == '_GET_MERCHANT_LISTINGS_DATA_' && $value['ReportProcessingStatus'] == '_DONE_'){
                return $generatedReportId = $value['GeneratedReportId'];
                break;
            }
        }
        return $generatedReportId;

	}

	//根据generatedReportId 获取卖家在售商品信息
	public function getReport($generatedReportId){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $result = $amazon->getReport($generatedReportId);
	    if(!$result){
	    	return null;
	    }
	    try {
	    	$outArray = MyHelper::text2Array($result);
	    } catch (\Exception $e) {
	    	print_r($e->getMessage() . "\n");
	    	return null;
	    }
	    return $outArray;
	}

	//修改价格接口   修改促销价格接口
	public function changePrice($feed, $feedType = '_POST_PRODUCT_PRICING_DATA_'){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $result = $amazon->SubmitFeed($feed, $feedType);
	    return $result;
	}

	//修改库存接口
	public function changeInventory($feed, $feedType = '_POST_INVENTORY_AVAILABILITY_DATA_'){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $result = $amazon->SubmitFeed($feed, $feedType);
	    return $result;
	}

	//修改商品信息接口
	public function changeProductInfo($feed, $feedType = '_POST_PRODUCT_DATA_'){
		$amazon = new \AmazonCommon($this->seller_id, $this->access_key, $this->secret_access, $this->_serviceUrl, $this->_marketplaceId);
	    $result = $amazon->SubmitFeed($feed, $feedType);
	    return $result;
	}
	//getLowestPricedOffersForSKU
	public function getLowestPricedOffersForSKU($marketplaceId, $sellerSKU, $itemCondition){
   		$serviceUrl = $this->_serviceUrl."/Products/2011-10-01";
		$amazon = new \AmazonProductCommon($this->seller_id, $this->access_key, $this->secret_access, $serviceUrl);
		$transArray = array();
    	$resultXml = $amazon->getLowestPricedOffersForSKU($marketplaceId ,$sellerSKU, $itemCondition);
    	if(!$resultXml){
    		return null;
    	}
    	$transArray = MyHelper::xml_to_array($resultXml);
    	//var_dump($transArray);
	    if(empty($transArray)){
	    	echo $resultXml;
	    	return null;
	    } else if ($transArray['GetLowestPricedOffersForSKUResponse']['GetLowestPricedOffersForSKUResult']['Summary']['TotalOfferCount'] == 0){
	    	//Success with no offers  or  Missing shipping charge 有两张情况没有数据，详细情况查看文档 http://docs.developer.amazonservices.com/en_US/products/Products_GetLowestPricedOffersForSKU.html
	    	return null;
	    } else {
	    	return $transArray;
	    	/*$outArray = [];
	    	$subTransArray = $transArray['GetLowestPricedOffersForSKUResponse']['GetLowestPricedOffersForSKUResult'];
	    	$outArray['competitorsCount'] = $subTransArray['Summary']['TotalOfferCount'];
	    	$outArray['lowerPrice'] = $subTransArray['Summary']['LowestPrices']['LowestPrice']['LandedPrice']['Amount'];
	    	$outArray['buyboxPrice'] = isset($subTransArray['Summary']['BuyBoxPrices']['BuyBoxPrice']['LandedPrice']['Amount']) ? $subTransArray['Summary']['BuyBoxPrices']['BuyBoxPrice']['LandedPrice']['Amount'] : 0;
	    	$offers = $subTransArray['Offers']['Offer'];
	    	if(!isset($offers[0])){//offers只有一个offer
	    		$realOffers[] = $offers;
	    	} else {//offers有多个offer
	    		$realOffers = $offers;
	    	}
	    	foreach ($realOffers as $key => $offer) {
	    		if($offer['MyOffer']){
	    			$outArray['myPrice'] = $offer['Shipping']['ListingPrice']['Amount'] + $offer['Shipping']['Amount'];
			    	$outArray['isMyOffer'] = $offer['MyOffer'];
			    	$outArray['IsFulfilledByAmazon'] =$offer['IsFulfilledByAmazon'];
			    	$outArray['IsFeaturedMerchant'] = $offer['IsFeaturedMerchant'];
	    		}
	    	}*/
		}
   	}

  	public function getReturnErrorMsg()
  	{
  		if (is_null($this->_result)) {
  			return [];
  		}
  		if (!preg_match('/\n\n/is', $this->_result)) {
  			return [];
  		}

  		$arr = explode("\n\n", $this->_result);

  		$ret = $this->text2Array($arr[1]);
  		$errorMsg = [];
  		foreach ($ret as $v) {
  			if (in_array($v['error-type'], ['Error', 'Fatal'])) {

  				$errorMsg[] = "SKU是{$v['sku']}的产品错误信息为:{$v['error-message']}";
  			}
  		}

  		return $errorMsg;
  	}

  	public static function text2Array($text)
  	{
  		$arr = explode("\n", $text);
		$keys = explode("\t", array_shift($arr));
		return array_map(function ($val) use ($keys) {
				return array_combine($keys, explode("\t", $val));
			}, $arr);
  	}

}
