<?php
/** 
 *  PHP Version 5
 *
 *  @category    Amazon
 *  @package     MarketplaceWebService
 *  @copyright   Copyright 2009 Amazon Technologies, Inc.
 *  @link        http://aws.amazon.com
 *  @license     http://aws.amazon.com/apache2.0  Apache License, Version 2.0
 *  @version     2009-01-01
 */
/******************************************************************************* 

 *  Marketplace Web Service PHP5 Library
 *  Generated: Thu May 07 13:07:36 PDT 2009
 * 
 */

/**
 * Submit Feed  Sample
 */

include_once ('.config.inc.php'); 

/************************************************************************
* Uncomment to configure the client instance. Configuration settings
* are:
*
* - MWS endpoint URL
* - Proxy host and port.
* - MaxErrorRetry.
***********************************************************************/
// IMPORTANT: Uncomment the approiate line for the country you wish to
// sell in:
// United States:
$serviceUrl = "https://mws.amazonservices.com";
// United Kingdom
//$serviceUrl = "https://mws.amazonservices.co.uk";
// Germany
//$serviceUrl = "https://mws.amazonservices.de";
// France
//$serviceUrl = "https://mws.amazonservices.fr";
// Italy
//$serviceUrl = "https://mws.amazonservices.it";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn";
// Canada
//$serviceUrl = "https://mws.amazonservices.ca";
// India
//$serviceUrl = "https://mws.amazonservices.in";

$config = array (
  'ServiceURL' => $serviceUrl,
  'ProxyHost' => null,
  'ProxyPort' => -1,
  'MaxErrorRetry' => 3,
);

/************************************************************************
 * Instantiate Implementation of MarketplaceWebService
 * 
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants 
 * are defined in the .config.inc.php located in the same 
 * directory as this sample
 ***********************************************************************/
 $service = new MarketplaceWebService_Client(
     AWS_ACCESS_KEY_ID, 
     AWS_SECRET_ACCESS_KEY, 
     $config,
     APPLICATION_NAME,
     APPLICATION_VERSION);
 
/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebService
 * responses without calling MarketplaceWebService service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebService/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebService_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out 
 * sample for Submit Feed Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebService_Model_SubmitFeedRequest
 // object or array of parameters

// Note that PHP memory streams have a default limit of 2M before switching to disk. While you
// can set the limit higher to accomidate your feed in memory, it's recommended that you store
// your feed on disk and use traditional file streams to submit your feeds. For conciseness, this
// examples uses a memory stream.

$feed = <<<EOD
TemplateType=Clothing Version=2015.1208                                                                                                                                                                                                                               
item_sku  item_name external_product_id external_product_id_type  brand_name  product_description item_type model update_delete standard_price  list_price  product_tax_code  fulfillment_latency product_site_launch_date  merchant_release_date restock_date  quantity  sale_price  sale_from_date  sale_end_date max_aggregate_ship_quantity item_package_quantity number_of_items offering_can_be_gift_messaged offering_can_be_giftwrapped is_discontinued_by_manufacturer missing_keyset_reason merchant_shipping_group_name  website_shipping_weight website_shipping_weight_unit_of_measure item_weight_unit_of_measure item_weight item_length_unit_of_measure item_length item_width  item_height bullet_point1 bullet_point2 bullet_point3 bullet_point4 bullet_point5 generic_keywords  main_image_url  other_image_url1  other_image_url2  other_image_url3  swatch_image_url  fulfillment_center_id package_height  package_width package_length  package_length_unit_of_measure  package_weight  package_weight_unit_of_measure  parent_child  parent_sku  relationship_type variation_theme cpsia_cautionary_statement  cpsia_cautionary_description  closure_type  belt_style  bottom_style  subject_character chest_size  chest_size_unit_of_measure  band_size_num band_size_num_unit_of_measure collar_style  color_name  color_map control_type  cup_size  department_name fabric_wash fit_type  front_style inseam_length inseam_length_unit_of_measure rise_height rise_height_unit_of_measure leg_diameter  leg_diameter_unit_of_measure  leg_style fabric_type import_designation  country_as_labeled  fur_description opacity neck_size neck_size_unit_of_measure neck_style  pattern_type  pocket_description  rise_style  shoe_width  size_name size_map  special_size_type sleeve_length sleeve_length_unit_of_measure sleeve_type special_features  strap_type  style_name  theme toe_style top_style underwire_type  waist_size  waist_size_unit_of_measure  water_resistance_level  sport_type  wheel_type
item_sku  item_name external_product_id external_product_id_type  brand_name  product_description item_type model update_delete standard_price  list_price  product_tax_code  fulfillment_latency product_site_launch_date  merchant_release_date restock_date  quantity  sale_price  sale_from_date  sale_end_date max_aggregate_ship_quantity item_package_quantity number_of_items offering_can_be_gift_messaged offering_can_be_giftwrapped is_discontinued_by_manufacturer missing_keyset_reason merchant_shipping_group_name  website_shipping_weight website_shipping_weight_unit_of_measure item_weight_unit_of_measure item_weight item_length_unit_of_measure item_length item_width  item_height bullet_point1 bullet_point2 bullet_point3 bullet_point4 bullet_point5 generic_keywords  main_image_url  other_image_url1  other_image_url2  other_image_url3  swatch_image_url  fulfillment_center_id package_height  package_width package_length  package_length_unit_of_measure  package_weight  package_weight_unit_of_measure  parent_child  parent_sku  relationship_type variation_theme cpsia_cautionary_statement  cpsia_cautionary_description  closure_type  belt_style  bottom_style  subject_character chest_size  chest_size_unit_of_measure  band_size_num band_size_num_unit_of_measure collar_style  color_name  color_map control_type  cup_size  department_name fabric_wash fit_type  front_style inseam_length inseam_length_unit_of_measure rise_height rise_height_unit_of_measure leg_diameter  leg_diameter_unit_of_measure  leg_style fabric_type import_designation  country_as_labeled  fur_description opacity neck_size neck_size_unit_of_measure neck_style  pattern_type  pocket_description  rise_style  shoe_width  size_name size_map  special_size_type sleeve_length sleeve_length_unit_of_measure sleeve_type special_features  strap_type  style_name  theme toe_style top_style underwire_type  waist_size  waist_size_unit_of_measure  water_resistance_level  sport_type  wheel_type
FT9993drt-Large 2016 new short sleeved T-shirt solid Mens Sport tooling T-Shirt Size  732240153757  UPC dell  rewrwerwerw special-occasion-dresses      4.00  256.00            5 44.00 2016-06-09 00:00:00 2016-06-24 00:00:00                 2.2 KG              Brand: can be customized  Item: DX010 Suitable for the crowd: Youth Style: casual Amoy goods categories: Youth Popular (18-24 years old)  Six hundred and sixty-six,Sixty-six thousand ,Six hundred ,Sixty-six billion ,Six thousand  http://imgsrc.baidu.com/forum/w%3D580/sign=fcae01763b87e9504217f3642039531b/2f2eb9389b504fc29fccbeb0e4dde71191ef6df7.jpg                                                      Beige       girls                                               Large                                   
EOD;

// Constructing the MarketplaceId array which will be passed in as the the MarketplaceIdList 
// parameter to the SubmitFeedRequest object.
$marketplaceIdArray = array("Id" => array('ATVPDKIKX0DER','A2EUQ1WTGCTBG2'));
     
 // MWS request objects can be constructed two ways: either passing an array containing the 
 // required request parameters into the request constructor, or by individually setting the request
 // parameters via setter methods.
 // Uncomment one of the methods below.
 
/********* Begin Comment Block *********/

//$feedHandle = @fopen('php://temp', 'rw+');
//fwrite($feedHandle, $feed);
//rewind($feedHandle);
//$parameters = array (
//  'Merchant' => MERCHANT_ID,
//  'MarketplaceIdList' => $marketplaceIdArray,
//  'FeedType' => '_POST_ORDER_FULFILLMENT_DATA_',
//  'FeedContent' => $feedHandle,
//  'PurgeAndReplace' => false,
//  'ContentMd5' => base64_encode(md5(stream_get_contents($feedHandle), true)),
//  'MWSAuthToken' => '<MWS Auth Token>', // Optional
//);

//rewind($feedHandle);

//$request = new MarketplaceWebService_Model_SubmitFeedRequest($parameters);
/********* End Comment Block *********/

/********* Begin Comment Block *********/
$feedHandle = @fopen('php://memory', 'rw+');
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
/********* End Comment Block *********/

invokeSubmitFeed($service, $request);

@fclose($feedHandle);
                                        
/**
  * Submit Feed Action Sample
  * Uploads a file for processing together with the necessary
  * metadata to process the file, such as which type of feed it is.
  * PurgeAndReplace if true means that your existing e.g. inventory is
  * wiped out and replace with the contents of this feed - use with
  * caution (the default is false).
  *   
  * @param MarketplaceWebService_Interface $service instance of MarketplaceWebService_Interface
  * @param mixed $request MarketplaceWebService_Model_SubmitFeed or array of parameters
  */
  function invokeSubmitFeed(MarketplaceWebService_Interface $service, $request) 
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
                                                                
