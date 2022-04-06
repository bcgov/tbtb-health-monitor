<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getCurlRequest($url, $get_vars = false, $cid = NULL, $cacheExpire = 7200, $cookie_vals = '', $ret_cookies = false, $trace = false, $header = array()){

        $r = array();

        if(!empty($cookie_vals) && is_array($cookie_vals)){
            $cookie = implode('&', $cookie_vals);
        }else{
            if(!empty($cookie_vals)){
                $cookie = $cookie_vals;
            }else{
                $cookie = NULL;
            }
        }

        if($trace == true && headers_sent() == false){
            header('Content-type: text/plain');
        }

        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 800); //timeout in seconds
        curl_setopt($ch, CURLOPT_HEADER, "Content-Type:application/xml");

        if (!empty($header)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        }

        if($trace){
            curl_setopt($ch, CURLOPT_STDERR, fopen('php://output', 'w'));
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
        }else{
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        if($trace){
            curl_setopt($ch, CURLOPT_HEADER, 1);
        }else{
            curl_setopt($ch, CURLOPT_HEADER, 0);
        }

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);

        if(!empty($cookie)){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
        }


        //try & catch for curl request to get url
        try {
            // grab URL and pass it to the browser
            $ret = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if($httpcode == 500){

                //try again
                sleep(3);
                $ret = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if($httpcode == 500) {
                    return false;
                }
            }

            if($httpcode == 200){

                if($get_vars){
                    $info = curl_getinfo($ch);
                    if(isset($info['url'])){
                        $url = parse_url($info['url']);
                        parse_str($url['query'], $r);
                        $r['query_string'] = $r;
                    }
                }

                if($ret_cookies){
                    // get cookie
                    preg_match('/^Set-Cookie:\s*([^;]*)/mi', $ret, $m);
                    $r['cookies'] = $m;
                }

                $r['response'] = $ret;
                $r['response_code'] = $httpcode;
                return $r;
            }
        }
        catch (\Exception $e) {
        }
        return false;
    }


    public function smsUser($phone, $username, $service_name, $service_status)
    {
        $token = env('GC_NOTIFICATION_LIVE_TOKEN');
        $request = ["phone_number" => $phone, "template_id" => env('GC_NOTIFICATION_SMS_TEMPLATE'),
            "personalisation" => [
                "name" => $username, "service_name" => $service_name, "service_status" => $service_status
            ]
        ];

        $header = array('Content-Type: application/json', 'Authorization: ApiKey-v1 ' . $token);
        $curlOptions = [
            CURLOPT_URL => "https://api.notification.canada.ca/v2/notifications/sms",
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($request),
        ];

        $this->makeCurlCall($curlOptions);
        return null;
    }

    public function makeApiCall($url, $postData){

        if (!is_object(json_decode($postData))){
            $postData = json_encode($postData);
        }

        $header = array('HTTP_X_API_TOKEN: ' . env('API_TOKEN'), 'Accept: application/json, text/plain, */*', 'Content-Type: application/json;charset=utf-8');
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_POSTFIELDS => $postData,
        ];
        return $this->makeCurlCall($curlOptions);
    }
    public function makeCurlCall($curlOptions){
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $response = curl_exec($ch);

        $error = curl_error($ch);
        $result = array( 'header' => '',
            'body' => '',
            'curl_error' => '',
            'http_code' => '',
            'last_url' => '');
        if ( $error != "" ){
            $result['curl_error'] = $error;
            curl_close($ch);
            return $result;
        }

        $header_size = curl_getinfo($ch,CURLINFO_HEADER_SIZE);
        $result['header'] = substr($response, 0, $header_size);
        $result['body'] = substr( $response, $header_size );
        $result['http_code'] = curl_getinfo($ch,CURLINFO_HTTP_CODE);
        $result['last_url'] = curl_getinfo($ch,CURLINFO_EFFECTIVE_URL);

        curl_close($ch);
        return $result;
    }

}
