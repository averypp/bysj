<?php

include_once ('.config.inc.php'); 

class AmazonCommon
{

    private $_serviceUrl = 'https://mws.amazonservices.com';
    protected $_service;

    public function __construct()
    {

        include_once ('.config.inc.php'); 
        $config = array (
            'ServiceURL' => $this->_serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'MaxErrorRetry' => 3,
        );
        // var_dump($config);die;

$marketplaceIdArray = array("Id" => array('ATVPDKIKX0DER','A2EUQ1WTGCTBG2'));


        $this->_service = new MarketplaceWebService_Client(
             AWS_ACCESS_KEY_ID, 
             AWS_SECRET_ACCESS_KEY, 
             $config,
             APPLICATION_NAME,
             APPLICATION_VERSION);

        $feedHandle = @fopen('php://memory', 'rw+');

        $feed =

        fwrite($feedHandle, $feed);
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant(MERCHANT_ID);
        $request->setMarketplaceIdList($marketplaceIdArray);
        $request->setFeedType('_CHECK_FLAT_FILE_LISTINGS_DATA_');
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        $request->setMWSAuthToken('<MWS Auth Token>'); // Optional

        rewind($feedHandle);

        $this->invokeSubmitFeed($this->_service, $request);

        @fclose($feedHandle);
 
    }

    public function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
    {
      try {
              $response = $service->submitFeed($request);
              
                echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        SubmitFeedResponse\n");
                if ($response->isSetSubmitFeedResult()) { 
                    echo("            SubmitFeedResult\n");
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                        echo("                FeedSubmissionInfo\n");
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                            echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                            echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                            echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                            echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
                        }
                    } 
                } 
                if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
         } catch (MarketplaceWebService_Exception $ex) {
             echo("Caught Exception: " . $ex->getMessage() . "\n");
             echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             echo("Error Code: " . $ex->getErrorCode() . "\n");
             echo("Error Type: " . $ex->getErrorType() . "\n");
             echo("Request ID: " . $ex->getRequestId() . "\n");
             echo("XML: " . $ex->getXML() . "\n");
             echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
         }
 }




}