<?php

namespace App\Controllers;

use App\Integrations\IpStack;
use Illuminate\Support\Collection;

class MainController {
    public function index() {
        include VIEWS_DIR . '/Main/index.php';
    }

    public function buildStatistic() {

        $data = [];
        $statistic_data = [];

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

        $data_collection = collect($data);

        $continents_by_ip = [
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
        ];

        $continents_by_phone = include ROOT . '/resources/geonames.php';

        /*$unique_ips = $data_collection->unique('ip')->pluck('ip');
        $ip_stack_integration = new IpStack();

        foreach ($unique_ips as $ip) {
            $ip_data = $ip_stack_integration->getDataByIp($ip);

            if(array_key_exists('success', $ip_data) && $ip_data['success'] === false) {
                echo 'errorka!';
            }

            $continents_by_ip[$ip] = $ip_data['continent_code'];
        }*/

//        var_export($continents_by_ip);die;

        $data_by_customer = $data_collection->groupBy('customer_id');

        /**
         * @var Collection $customer_item
         */
        foreach ($data_by_customer as $customer_id => &$customer_item) {

            $customer_item->transform(function (array $item) use ($continents_by_ip, $continents_by_phone){
                $item['continent_by_ip'] = $continents_by_ip[$item['ip']];


//                var_dump($continents_by_phone);

                foreach ($continents_by_phone as $phone_prefix => $details) {

                    /*if($phone_prefix == '27') {
                        dump($item['phone']);
                        dump('~^' . quotemeta($phone_prefix) .'~');
                        dd(preg_match('~^' . $phone_prefix .'~', $item['phone']));
                    }*/

                    if(preg_match('~^' . quotemeta($phone_prefix) .'~', $item['phone'])) {
                        $item['continent_by_phone'] = $details['continent'];

                        break;
                    }

                }

                $item['call_within_same_continent'] = $item['continent_by_phone'] === $item['continent_by_ip'];

//                dd($continents_by_ip, $item['ip'], $continents_by_ip['87.190.148.217'] );
                return $item;
            });



            dump($customer_item);

            $statistic_data[$customer_id] = [
                'customer_id' => $customer_id,
                'count_calls_within_same_continent' => $customer_item->where('call_within_same_continent', true)->count(),
                'duration_calls_within_same_continent' => $customer_item->where('call_within_same_continent', true)->sum('duration'),
                'total_count_calls' => $customer_item->count(),
                'total_duration' => $customer_item->sum('duration')
            ];
        }

/*        $data_by_customer->each(function (Collection $customer_item, $customer_id) use ($ip_stack_integration, $statistic_data) {
//            $customer_item['total_duration'] = $customer_item->sum('duration');

//            dd($customer_item->sum('duration'));

//            dd($statistic_data);


            return $customer_item;
        });*/

        dd($statistic_data);
        dd($data_by_customer->toArray());

        dd($data_by_customer);

        var_dump($_SERVER);
        var_dump($_FILES);
        var_dump($_REQUEST);

    }
}