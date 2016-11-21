<?php

namespace app\assets;
require_once __DIR__ . "/amazonAPI/classes/core/WPLA_AmazonAPI.php";
// require_once __DIR__ . '/../../includes/amazon/src/MarketplaceWebService/Client.php';
require_once __DIR__ . '/amazonAPI/includes/amazon/src/MarketplaceWebService/Client.php';

class AmazonAPI {
    public function submitFeed($feedType,$data) {
        $api = new \WPLA_AmazonAPI();
        return  $api->submitFeed($feedType,$data);
    }
}