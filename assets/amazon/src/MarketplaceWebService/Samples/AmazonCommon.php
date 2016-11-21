<?php

//namespace app\assets\amazon\src\MarketplaceWebService\Samples;
include_once ('.config.inc.php'); 
class AmazonCommon
{

    private $_serviceUrl;
    protected $_service;
    protected $_request;
    public $seller_id;
    public $access_key;
    public $secret_access;
    public $feed;
    public $_marketplaceId;
    public function __construct($seller_id, $access_key, $secret_access, $serviceUrl='https://mws.amazonservices.com', $marketplaceId = 'ATVPDKIKX0DER')
    {

        include_once ('.config.inc.php'); 
        $this->seller_id = $seller_id;
        $this->access_key = $access_key;
        $this->secret_access = $secret_access;
        $this->_serviceUrl = $serviceUrl;
        $this->_marketplaceId = $marketplaceId;
        $config = array (
            'ServiceURL' => $this->_serviceUrl,
            'ProxyHost' => null,
            'ProxyPort' => -1,
            'MaxErrorRetry' => 3,
        );
        $this->_service = new MarketplaceWebService_Client(
             $this->access_key, 
             $this->secret_access, 
             $config,
            'cross',
             'v1.0');
    }

    function  createRequestOfSubmit($feed, $feedType = '_POST_FLAT_FILE_LISTINGS_DATA_'){
        $marketplaceIdArray = array("Id" => array($this->_marketplaceId));
        $feedHandle = @fopen('php://memory', 'rw+');
        fwrite($feedHandle, $feed);
        rewind($feedHandle);

        $request = new MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($this->seller_id);
        $request->setMarketplaceIdList($marketplaceIdArray);
        $request->setFeedType($feedType);
        $request->setContentMd5(base64_encode(md5(stream_get_contents($feedHandle), true)));
        rewind($feedHandle);
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        rewind($feedHandle);
        return $request;


    }
    function SubmitFeed($feed, $feedType = '_POST_FLAT_FILE_LISTINGS_DATA_'){
        $request = $this->createRequestOfSubmit($feed, $feedType);
        $result = $this->invokeSubmitFeed($this->_service, $request);
        return $result;
   }


   function  createRequestOfSubmissionResult($submissionId){
        $request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest();
        $request->setMerchant($this->seller_id);
        $request->setFeedSubmissionId($submissionId);
        $request->setFeedSubmissionResult(@fopen('php://memory', 'rw+'));
        return $request;


    }

  function requestReport($reportType){
        $marketplaceIdArray = array("Id" => array($this->_marketplaceId));
        $request = new MarketplaceWebService_Model_RequestReportRequest();
        $request->setMarketplaceIdList($marketplaceIdArray);
        $request->setMerchant($this->seller_id);
        $request->setReportType($reportType);
        $request->setReportOptions('ShowSalesChannel=true');
        return $this->invokeRequestReport($this->_service, $request);
   }


  function getReportRequestList(){
        $request = new MarketplaceWebService_Model_GetReportRequestListRequest();
        $request->setMerchant($this->seller_id);
        return $this->invokeGetReportRequestList($this->_service, $request);
   }


   function getReport($generatedReportId){
        $request = new MarketplaceWebService_Model_GetReportRequest();
        $request->setMerchant($this->seller_id);
        $request->setReport(@fopen('php://memory', 'rw+'));
        $request->setReportId($generatedReportId);
        return $this->invokeGetReport($this->_service, $request);
   }

    public function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
    {
      try {
              $response = $service->submitFeed($request);
               /* echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        SubmitFeedResponse\n");*/
                if ($response->isSetSubmitFeedResult()) { 
                    // echo("            SubmitFeedResult\n");
                    $submitFeedResult = $response->getSubmitFeedResult();
                    if ($submitFeedResult->isSetFeedSubmissionInfo()) { 
                        // echo("                FeedSubmissionInfo\n");
                        $feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo();
                        if ($feedSubmissionInfo->isSetFeedSubmissionId()) 
                        {
                           /* echo("                    FeedSubmissionId\n");
                            echo("                        " . $feedSubmissionInfo->getFeedSubmissionId() . "\n");*/
                        }
                        if ($feedSubmissionInfo->isSetFeedType()) 
                        {
                           /* echo("                    FeedType\n");
                            echo("                        " . $feedSubmissionInfo->getFeedType() . "\n");*/
                        }
                        if ($feedSubmissionInfo->isSetSubmittedDate()) 
                        {
                           /* echo("                    SubmittedDate\n");
                            echo("                        " . $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");*/
                        }
                        if ($feedSubmissionInfo->isSetFeedProcessingStatus()) 
                        {
                          /*  echo("                    FeedProcessingStatus\n");
                            echo("                        " . $feedSubmissionInfo->getFeedProcessingStatus() . "\n");*/
                        }
                        if ($feedSubmissionInfo->isSetStartedProcessingDate()) 
                        {
                            /*echo("                    StartedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");*/
                        }
                        if ($feedSubmissionInfo->isSetCompletedProcessingDate()) 
                        {
/*                            echo("                    CompletedProcessingDate\n");
                            echo("                        " . $feedSubmissionInfo->getCompletedProcessingDate()->format(DATE_FORMAT) . "\n");
*/                      }
                    } 
                } 
                if ($response->isSetResponseMetadata()) { 
                    // echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        // echo("                RequestId\n");
                        // echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                // echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

                return ['FeedSubmissionId' => $feedSubmissionInfo->getFeedSubmissionId(),
                        'FeedType' => $feedSubmissionInfo->getFeedType(),
                        'SubmittedDate' =>  $feedSubmissionInfo->getSubmittedDate()->format(DATE_FORMAT),
                        'FeedProcessingStatus' =>$feedSubmissionInfo->getFeedProcessingStatus()
                       ];
         } catch (MarketplaceWebService_Exception $ex) {
             /*echo("Caught Exception: " . $ex->getMessage() . "\n");
             echo("Response Status Code: " . $ex->getStatusCode() . "\n");
             echo("Error Code: " . $ex->getErrorCode() . "\n");
             echo("Error Type: " . $ex->getErrorType() . "\n");
             echo("Request ID: " . $ex->getRequestId() . "\n");
             echo("XML: " . $ex->getXML() . "\n");
             echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
             return null;
         }
     }

 function invokeRequestReport(MarketplaceWebService_Interface $service, $request) {
      try {
              $response = $service->requestReport($request);
              
              echo ("RequestReport Service Response\n");
              //  echo ("=============================================================================\n");

               // echo("        RequestReportResponse\n");
                if ($response->isSetRequestReportResult()) {
                  //  echo("            RequestReportResult\n");
                    $requestReportResult = $response->getRequestReportResult();
                    
                    if ($requestReportResult->isSetReportRequestInfo()) {
                        
                        $reportRequestInfo = $requestReportResult->getReportRequestInfo();
                          //echo("                ReportRequestInfo\n");
                          if ($reportRequestInfo->isSetReportRequestId()) 
                          {
                             // echo("                    ReportRequestId\n");
                             // echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");
                          }
                          if ($reportRequestInfo->isSetReportType()) 
                          {
                             // echo("                    ReportType\n");
                             // echo("                        " . $reportRequestInfo->getReportType() . "\n");
                          }
                          if ($reportRequestInfo->isSetStartDate()) 
                          {
                             // echo("                    StartDate\n");
                             // echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetEndDate()) 
                          {
                              //echo("                    EndDate\n");
                              //echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetSubmittedDate()) 
                          {
                             //echo("                    SubmittedDate\n");
                              //echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetReportProcessingStatus()) 
                          {
                            return true;
                             // echo("                    ReportProcessingStatus\n");
                              //echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                          }
                      }
                } 
                if ($response->isSetResponseMetadata()) { 
                    //echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        //echo("                RequestId\n");
                        //echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }
                return false;
                //echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
     } catch (MarketplaceWebService_Exception $ex) {
         echo("Caught Exception: " . $ex->getMessage() . "\n");
         /*echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
         return false;
     }
 }

 function invokeGetReportRequestList(MarketplaceWebService_Interface $service, $request) {
      try {
              $response = $service->getReportRequestList($request);
              
                /*echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetReportRequestListResponse\n");*/
                if ($response->isSetGetReportRequestListResult()) { 
                    //echo("            GetReportRequestListResult\n");
                    $getReportRequestListResult = $response->getGetReportRequestListResult();
                    if ($getReportRequestListResult->isSetNextToken()) 
                    {
                      /*  echo("                NextToken\n");
                        echo("                    " . $getReportRequestListResult->getNextToken() . "\n");*/
                    }
                    if ($getReportRequestListResult->isSetHasNext()) 
                    {
                       /* echo("                HasNext\n");
                        echo("                    " . $getReportRequestListResult->getHasNext() . "\n");*/
                    }
                    $reportRequestInfoList = $getReportRequestListResult->getReportRequestInfoList();
                    $returnArray = [];
                    foreach ($reportRequestInfoList as $key => $reportRequestInfo) {
                       // echo("                ReportRequestInfo\n");
                    if ($reportRequestInfo->isSetReportRequestId()) 
                          {
                             /* echo("                    ReportRequestId\n");
                              echo("                        " . $reportRequestInfo->getReportRequestId() . "\n");*/
                               
                          }
                          if ($reportRequestInfo->isSetReportType()) 
                          {
                             // echo("                    ReportType\n");
                             // echo("                        " . $reportRequestInfo->getReportType() . "\n");
                            $returnArray[$key]['ReportType'] =$reportRequestInfo->getReportType();
                          }
                          if ($reportRequestInfo->isSetStartDate()) 
                          {
                              //echo("                    StartDate\n");
                              //echo("                        " . $reportRequestInfo->getStartDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetEndDate()) 
                          {
                              //echo("                    EndDate\n");
                              //echo("                        " . $reportRequestInfo->getEndDate()->format(DATE_FORMAT) . "\n");
                          }
                          // add start
                          if ($reportRequestInfo->isSetScheduled()) 
                          {
                             // echo("                    Scheduled\n");
                             // echo("                        " . $reportRequestInfo->getScheduled() . "\n");
                          }
                          // add end
                          if ($reportRequestInfo->isSetSubmittedDate()) 
                          {
                              //echo("                    SubmittedDate\n");
                              //echo("                        " . $reportRequestInfo->getSubmittedDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetReportProcessingStatus()) 
                          {
                            $returnArray[$key]['ReportProcessingStatus'] =$reportRequestInfo->getReportProcessingStatus();
                              //echo("                    ReportProcessingStatus\n");
                              //echo("                        " . $reportRequestInfo->getReportProcessingStatus() . "\n");
                          }
                          // add start
                          if ($reportRequestInfo->isSetGeneratedReportId()) 
                          {
                            $returnArray[$key]['GeneratedReportId'] =$reportRequestInfo->getGeneratedReportId();
                             // echo("                    GeneratedReportId\n");
                              //echo("                        " . $reportRequestInfo->getGeneratedReportId() . "\n");
                          }
                          if ($reportRequestInfo->isSetStartedProcessingDate()) 
                          {
                              //echo("                    StartedProcessingDate\n");
                             // echo("                        " . $reportRequestInfo->getStartedProcessingDate()->format(DATE_FORMAT) . "\n");
                          }
                          if ($reportRequestInfo->isSetCompletedDate()) 
                          {
                             // echo("                    CompletedDate\n");
                              //echo("                        " . $reportRequestInfo->getCompletedDate()->format(DATE_FORMAT) . "\n");
                          }
                          // add end
                          
                    }
                    return $returnArray;
                } 
                /*if ($response->isSetResponseMetadata()) { 
                    echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        echo("                RequestId\n");
                        echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                } 

                echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");*/
     } catch (MarketplaceWebService_Exception $ex) {

         return null;
         /*echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
     }
 }

 function invokeGetReport(MarketplaceWebService_Interface $service, $request) {
      try {
              $response = $service->getReport($request);
              
               /* echo ("Service Response\n");
                echo ("=============================================================================\n");

                echo("        GetReportResponse\n");*/
                if ($response->isSetGetReportResult()) {
                  $getReportResult = $response->getGetReportResult(); 
                  //echo ("            GetReport");
                  
                  if ($getReportResult->isSetContentMd5()) {
                   // echo ("                ContentMd5");
                    //echo ("                " . $getReportResult->getContentMd5() . "\n");
                  }
                }
                if ($response->isSetResponseMetadata()) { 
                    //echo("            ResponseMetadata\n");
                    $responseMetadata = $response->getResponseMetadata();
                    if ($responseMetadata->isSetRequestId()) 
                    {
                        //echo("                RequestId\n");
                        //echo("                    " . $responseMetadata->getRequestId() . "\n");
                    }
                }
                
                //echo ("        Report Contents\n");
               // echo (stream_get_contents($request->getReport()) . "\n");
                //echo("            ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");
                return stream_get_contents($request->getReport());
     } catch (MarketplaceWebService_Exception $ex) {
         return null;
         /*echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");*/
     }
 }




}