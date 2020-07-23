<?php

namespace App\Integrations;

class IpStack {
    private $api_domain = 'http://api.ipstack.com/';
    private $access_key = '47affc0931cf6f1645ce02586d5fc575';

    private $response;

    private function sendRequest($ip) {


        $ch = curl_init($this->api_domain . $ip . '?access_key=' . $this->access_key);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        return json_decode($json, true);
    }

    public function getDataByIp($ip) {
        return $this->sendRequest($ip);
    }
}