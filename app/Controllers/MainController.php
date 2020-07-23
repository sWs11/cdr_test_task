<?php

namespace App\Controllers;

use App\Integrations\IpStack;
use Illuminate\Support\Collection;

class MainController {
    /**
     * @var array
     */
    private $continents_by_ip;

    /**
     * @var array
     */
    private $continents_by_phone;

    /**
     * Simple page with form for load file
     */
    public function index() {
        include VIEWS_DIR . '/main/index.php';
    }

    /**
     * Build statistic by data in CSV file and show this statistic in table on page
     */
    public function buildStatistic() {
        $data = $this->getDataFromFile();
        $data_collection = collect($data);
        $continents_by_ip = $this->getContinentsByIp($data_collection);

        /*$this->continents_by_ip = [
            '87.190.148.217' => 'EU',
            '41.77.56.40' => 'AF',
            '218.99.136.57' => 'AS',
            '112.125.168.89' => 'AS',
            '20.12.165.244' => 'NA',
            '145.81.57.99' => 'EU',
            '116.52.75.78' => 'AS',
            '216.165.48.137' => 'NA',
            '37.35.105.218' => 'EU',
            '63.81.174.152' => 'NA',
            '91.112.216.215' => 'EU'
        ];*/

        $this->continents_by_phone = $this->getContinentsByPhone();
        $data_by_customers = $data_collection->groupBy('customer_id');
        $statistic_data = $this->calculateStatistic($data_by_customers);

        include VIEWS_DIR . '/main/index.php';
    }

    /**
     * @return array
     */
    private function getDataFromFile() : array {
        $data = [];

        if (($handle = fopen($_FILES['file']['tmp_name'], "r")) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $data[] = [
                    'customer_id' => $row[0],
                    'call_date' => $row[1],
                    'duration' => $row[2],
                    'phone' => $row[3],
                    'ip' => $row[4],
                ];
            }

            fclose($handle);
        }

        return $data;
    }

    /**
     * @param Collection $data_collection
     * @return array
     */
    private function getContinentsByIp(Collection $data_collection) : array {
        $unique_ips = $data_collection->unique('ip')->pluck('ip');
        $ip_stack_integration = new IpStack();
        $continents_by_ip = [];

        foreach ($unique_ips as $ip) {
            $ip_data = $ip_stack_integration->getDataByIp($ip);

            if(array_key_exists('success', $ip_data) && $ip_data['success'] === false) {
                echo 'errorka!';
            }

            $continents_by_ip[$ip] = $ip_data['continent_code'];
        }

        return $continents_by_ip;
    }

    /**
     * @return array
     */
    private function getContinentsByPhone() :array {
        return include ROOT . '/resources/geonames.php';
    }

    /**
     * @param Collection $data_by_customers
     * @return array
     */
    private function calculateStatistic(Collection $data_by_customers) : array {
        $statistic_data = [];

        /**
         * @var Collection $customer_item
         */
        foreach ($data_by_customers as $customer_id => $customer_item) {
            $customer_item->transform(function (array $item) {
                $item['continent_by_ip'] = $this->continents_by_ip[$item['ip']];

                foreach ($this->continents_by_phone as $phone_prefix => $details) {
                    if(preg_match('~^' . quotemeta($phone_prefix) .'~', $item['phone'])) {
                        $item['continent_by_phone'] = $details['continent'];

                        break;
                    }
                }

                $item['call_within_same_continent'] = $item['continent_by_phone'] === $item['continent_by_ip'];

                return $item;
            });

            $statistic_data[$customer_id] = [
                'customer_id' => $customer_id,
                'count_calls_within_same_continent' => $customer_item->where('call_within_same_continent', true)->count(),
                'duration_calls_within_same_continent' => $customer_item->where('call_within_same_continent', true)->sum('duration'),
                'total_count_calls' => $customer_item->count(),
                'total_duration' => $customer_item->sum('duration')
            ];
        }

        return $statistic_data;
    }
}