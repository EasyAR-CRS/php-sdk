<?php

class EasyARCloudApiSample{
    private $appKey = 'App key from provider;';
    private $appSecret = 'Secret from provider ';

    private $host = 'http://localhost:8888';
    private $targetAdd = 'targets';
    private $targetDelete = 'target';
    private $targetUpdate = 'target';
    private $similar = 'similar';
    private $targetsList = 'targets';
    private $targetInfo = 'target';
    private $gradeDetection = 'grade/detection';
    private $gradeTracking = 'grade/tracking';

    public function targetAdd($name, $width, $image = NULL, $active = NULL, $applicationMetadata = NULL){
        // name and size have to provide
        $dataArr = [
            'name' => $name,
            'size' => (string)$width,
        ];
        // check image marker
        // input should be image content
        if(!is_null($image)) {
            $dataArr['image'] = base64_encode($image);
        }
        // check active flag
        if(!is_null($active) || (('0' === $active) && ('1' === $active))){
            $dataArr['active'] = $active;
        }
        // check meta
        if(!is_null($applicationMetadata)) {
            $dataArr['meta'] = base64_encode($applicationMetadata);
        }
        // type should be ImageTarget
        $dataArr['type'] = 'ImageTarget';

        $responseJson = $this->curlHttpPostRequest($this->host, $this->targetAdd, $dataArr);
        $response = json_decode($responseJson, true);

        if(!isset($response['statusCode']) || !isset($response['result']['targetId']) ){
            throw new Exception('Target add response format error. responseJson = '.$responseJson);
        }
        return [
            'resultCode' => $response['statusCode'],
            'targetId' => $response['result']['targetId'],
        ];

    }

    public function targetDelete($targetId){
        $responseJson = $this->curlHttpDeleteRequest($this->host, $this->targetDelete, $targetId, array());

        $response = json_decode($responseJson, true);

        if(!isset($response['statusCode'])){
            throw new Exception('Target delete response format error. targetId = ' . $targetId . ' responseJson = '.$responseJson);
        }
        return [
            'resultCode' => $response['statusCode'],
        ];
    }

    public function targetUpdate($targetId, $name = NULL, $size = NULL, $image = NULL, $active = NULL, $applicationMetadata = NULL){
        // base array
        $dataArr = array();
        // check name
        if(!is_null($name)){
            $dataArr['name'] = $name;
        }
        // check size
        if(!is_null($size)){
            $dataArr['size'] = (string)$size;
        }
        // check image marker
        if(!is_null($image)){
            $dataArr['image'] = base64_encode($image);
        }
        // check active flag
        if(!is_null($active)){
            // check active flag
            if(('0' === $active) || ('1' === $active)){
                $dataArr['active'] = (string)$active;
            }

        }
        // check meta
        if(!is_null($applicationMetadata)){
            $dataArr['meta'] = base64_encode($applicationMetadata);
        }
        // type should be ImageTarget
        $dataArr['type'] = 'ImageTarget';
        //---
        $responseJson = $this->curlHttpPutRequest($this->host, $this->targetUpdate, $targetId, $dataArr);
        $response = json_decode($responseJson, true);
        if(!isset($response['statusCode'])){
            throw new Exception('Target update response format error. targetId = ' . $targetId . ' responseJson = '.$responseJson);
        }
        return [
            'resultCode' => $response['statusCode'],
        ];

    }

    public function targetInfo($targetId){
        $responseJson = $this->curlHttpGetRequest($this->host, $this->targetInfo, $targetId);

        $response = json_decode($responseJson, true);
        if(!isset($response['statusCode'])
        ){
            throw new Exception('Target info response format error. responseJson = '.$responseJson);
        }

        return $response;
    }

    public function targetsList(){
        $responseJson = $this->curlHttpGetRequest($this->host, $this->targetsList, '');

        $response = json_decode($responseJson, true);

        if(!isset($response['statusCode']) || !isset($response['result']) ){
            throw new Exception('Target list response format error. responseJson = '.$responseJson);
        }

        return [
            'resultCode' => $response['statusCode'],
            'result' => $response['result'],
        ];

    }

    public function similar($image){
        //---
        $dataArr['image'] = base64_encode($image);
        $responseJson = $this->curlHttpPostRequest($this->host, $this->similar, $dataArr);
        $response = json_decode($responseJson, true);

        if(!isset($response['statusCode']) ){
            throw new Exception('Similar response format error. responseJson = '.$responseJson);
        }
        if(isset($response['result']['results'])){
            $similarArr = $response['result']['results'];
        } else {
            $similarArr = array();
        }

        return [
            'resultCode' => $response['statusCode'],
            'result' => $similarArr,
        ];

    }

    public function gradeDetection($image){
        // image should be image content
        $dataArr['image'] = base64_encode($image);
        $responseJson = $this->curlHttpPostRequest($this->host, $this->gradeDetection, $dataArr);
        $response = json_decode($responseJson, true);

        if(!isset($response['statusCode']) ){
            throw new Exception('Similar response format error. responseJson = '.$responseJson);
        }
        if(isset($response['result']['grade'])){
            $grade = $response['result']['grade'];
        } else {
            throw new Exception('Similar response grade error. responseJson = '.$responseJson);
        }

        return [
            'resultCode' => $response['statusCode'],
            'result' => [
                'grade' => $grade
            ],
        ];

    }

    public function curlHttpGetRequest($host, $router, $id = ''){
        if('' == $id){
            $url = $host.'/'.$router.'/';
        } else {
            $url = $host.'/'.$router.'/'.$id;
        }

        $data = array(
            'appKey' => $this->appKey,
            'date' => $this->getDateTime(),
        );

        $data['signature'] = $this->getSign($data);
        $query = http_build_query($data);
        $url .= '?'. $query;

        return $this->httpRequest($url, false, 'GET');
    }

    public function curlHttpPostRequest($host, $router, $data){
        $url = $host.'/'.$router.'/';

        $data['appKey'] = $this->appKey;
        $data['date'] = $this->getDateTime();
        $data['signature'] = $this->getSign($data);

        return $this->httpRequest($url, $data, 'POST');
    }

    public function curlHttpPutRequest($host, $router, $id, $data){
        $url = $host.'/'.$router.'/'.$id;

        $data['appKey'] = $this->appKey;
        $data['date'] = $this->getDateTime();
        $data['signature'] = $this->getSign($data);

        return $this->httpRequest($url, $data, 'PUT');
    }

    public function curlHttpDeleteRequest($host, $router, $id, $data){
        $url = $host.'/'.$router.'/'.$id;

        $data['appKey'] = $this->appKey;
        $data['date'] = $this->getDateTime();
        $data['signature'] = $this->getSign($data);

        $query = http_build_query($data);
        $url .= '?'. $query;

        return $this->httpRequest($url, false, 'DELETE');
    }

    private function httpRequest($url, $data, $method) {
        if(!(function_exists('curl_init'))){
            throw new Exception('EasyAR Cloud API PHP SDK require curl, you must install curl first.');
        }

        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url);

        switch ($method) {
            case 'POST':
                curl_setopt($ch,CURLOPT_POST, 1);
                break;
            case 'GET':
                break;
            case 'PUT':
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }


        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Date: '.date('D, j M Y H:i:s ',time()).'GMT', 'Content-Type: application/json'));

        if ($data) {
            $str = is_string($data) ? $data : json_encode($data);
            curl_setopt($ch,CURLOPT_POSTFIELDS, $str);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $str = curl_exec($ch);
        curl_close($ch);

        return $str;
    }

    private function getDateTime() {
        $timestamp = time() - 8 * 3600;
        return date('Y-m-d\TH:i:s.000\Z', $timestamp);
    }

    private function fixDateTime($time, $spanTime) {
        $timestamp = strtotime($time) + $spanTime;
        return date('Y-m-d\TH:i:s.000\Z', $timestamp);
    }

    private function getSign($params) {
        ksort($params);

        $tmp = [];
        foreach ($params as $k => $v) {
            if (is_string($v)) {
                $tmp[] = $k . $v;
            }
        }
        $str = implode('', $tmp);
        return sha1($str . $this->appSecret);
    }

}
