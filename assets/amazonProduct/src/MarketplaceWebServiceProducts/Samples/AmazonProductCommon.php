<?php

//namespace app\assets\amazonProduct\src\MarketplaceWebServiceProducts\Samples;
include_once ('.config.inc.php'); 
class AmazonProductCommon
{

    private $_serviceUrl;
    protected $_service;
    protected $_request;
    public $seller_id;
    public $access_key;
    public $secret_access;
    public $feed;
    public function __construct($seller_id, $access_key, $secret_access, $serviceUrl="https://mws.amazonservices.com/Products/2011-10-01"){

        include_once ('.config.inc.php'); 
        $this->seller_id = $seller_id;
        $this->access_key = $access_key;
        $this->secret_access = $secret_access;
        $this->_serviceUrl = $serviceUrl;
        $config = array (
            'ServiceURL' => $this->_serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'ProxyUsername' => null,
            'ProxyPassword' => null,
            'MaxErrorRetry' => 3,
        );
        $this->_service = new MarketplaceWebServiceProducts_Client(
             $this->access_key, 
             $this->secret_access, 
            'cross',
             'v1.0',
             $config);

    }

    
    function getUpSuccessReturnValue($marketplaceId ,$idType, $idList){
        $request = new MarketplaceWebServiceProducts_Model_GetMatchingProductForIdRequest();
        $request->setSellerId($this->seller_id);
        $request->setMarketplaceId($marketplaceId);
        $request->setIdType($idType);
        $idListType = new MarketplaceWebServiceProducts_Model_IdListType();
        $idListType->setId($idList);
        $request->setIdList($idListType);
        $result = $this->invokeGetMatchingProductForId($this->_service, $request);
        return  $result;
   }


   function invokeGetMatchingProductForId(MarketplaceWebServiceProducts_Interface $service, $request){
          try {
            $response = $service->GetMatchingProductForId($request);

            echo ("GetMatchingProductForId Response\n");
            //echo ("=============================================================================\n");

            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            return $dom->saveXML();

         } catch (MarketplaceWebServiceProducts_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            /*echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
            return null;
         }
    }


    function getLowestPricedOffersForSKU($marketplaceId ,$sellerSKU, $itemCondition){
        $request = new MarketplaceWebServiceProducts_Model_GetLowestPricedOffersForSKURequest();
        $request->setSellerId($this->seller_id);
        $request->setMarketplaceId($marketplaceId);
        $request->setSellerSKU($sellerSKU);
        $request->setItemCondition($itemCondition);
        $result = $this->invokeGetLowestPricedOffersForSKU($this->_service, $request);
        return  $result;
    }

    function invokeGetLowestPricedOffersForSKU(MarketplaceWebServiceProducts_Interface $service, $request){
        try {
            $response = $service->GetLowestPricedOffersForSKU($request);

            echo ("GetLowestPricedOffersForSKU Response\n");
            //echo ("=============================================================================\n");

            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            return  $dom->saveXML();
            //echo $dom->saveXML();
            //echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

        } catch (MarketplaceWebServiceProducts_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            return null;
            /*echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
        }
    }


    function getPriceAndShippingForSKU($marketplaceId, $sku){
        $request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest();
        $request->setSellerId($this->seller_id);
        $request->setMarketplaceId($marketplaceId);
        $SellerSKUList = new MarketplaceWebServiceProducts_Model_SellerSKUListType();
        $SellerSKUList->setSellerSKU($sku);
        $request->setSellerSKUList($SellerSKUList);
        $result = $this->invokeGetMyPriceForSKU($this->_service, $request);
        return $result;
   }

   function invokeGetMyPriceForSKU(MarketplaceWebServiceProducts_Interface $service, $request){
          try {
            $response = $service->GetMyPriceForSKU($request);

            echo ("GetMyPriceForSKU Response\n");
            //echo ("=============================================================================\n");

            $dom = new DOMDocument();
            $dom->loadXML($response->toXML());
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            return $dom->saveXML();

         } catch (MarketplaceWebServiceProducts_Exception $ex) {
            echo("Caught Exception: " . $ex->getMessage() . "\n");
            return null;
            /*echo("Response Status Code: " . $ex->getStatusCode() . "\n");
            echo("Error Code: " . $ex->getErrorCode() . "\n");
            echo("Error Type: " . $ex->getErrorType() . "\n");
            echo("Request ID: " . $ex->getRequestId() . "\n");
            echo("XML: " . $ex->getXML() . "\n");
            echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
         }
    }

}