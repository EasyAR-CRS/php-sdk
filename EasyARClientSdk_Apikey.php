<?php

/**
 * Class EasyARClientSdkCRS
 * EasyAR CRS 识别图片操作
 */
class EasyARClientSdkCRS {
    private $apiKey;
    private $apiSecret;
    private $appId;
    private $appHost;

    function __construct($apiKey, $apiSecret, $appId, $appHost) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->appId = $appId;
        $this->appHost = 'http://' . $appHost;
    }

    public function ping() {
        $rs = Http::get($this->appHost . '/ping', '');
        return json_decode($rs);
    }

    /**
     * 取识别图列表 V3
     * @param int $pageNum 第几页
     * @param int $pageSize 每页多少条记录
     * @return mixed
     */
    public function targetsV3($pageNum = 1, $pageSize = 10) {
        $params['pageNum'] = (string)$pageNum;
        $params['pageSize'] = (string)$pageSize;
        $params = $this->getSign($params);
        $rs = Http::get($this->appHost . '/targets/infos', $params);
        return json_decode($rs);
    }


    /**
     * @deprecated
     */
    public function targets($limit, $last) {
        $params['limit'] = (string)$limit;
        $params['last'] = (string)$last;
        $params = $this->getSign($params);
        $rs = Http::get($this->appHost . '/targets/', $params);
        return json_decode($rs);
    }

    /**
     * 取识别图详情信息
     * @param string $targetId 识别图id
     * @return mixed
     */
    public function info($targetId) {
        $params = $this->getSign();
        $rs = Http::get($this->appHost . '/target/' . $targetId, $params);
        return json_decode($rs);
    }

    /**
     * 删除识别图
     * @param string $targetId 识别图id
     * @return mixed
     */
    public function delete($targetId){
        $params = $this->getSign();
        $rs = Http::delete($this->appHost . '/target/' . $targetId, $params);
        return json_decode($rs);
    }

    /**
     * 添加识别图
     * @param array $params
     *            image: base64后识别图，必须
     *            active: 是否启用：0为否, 1为是，必须
     * @return mixed
     */
    public function targetAdd($params) {
        $params['type'] = 'ImageTarget';
        $params = $this->getSign($params);
        $data = json_encode($params);
        $headers = ['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($data)];

        $rs = Http::post($this->appHost . '/targets/', $data, $headers);
        return json_decode($rs);
    }

    /**
     * 更新识别图
     * @param string $targetId 识别图id
     * @param array $params
     * @return mixed
     */
    public function targetUpdate($targetId, $params) {
        $params = $this->getSign($params);
        $data = json_encode($params);
        $headers = ['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($data)];

        $rs = Http::put($this->appHost . '/target/' . $targetId, $data, $headers);
        return json_decode($rs);
    }

    /**
     * 取识别图数量
     * @return mixed
     */
    public function targetsCount() {
        $params = $this->getSign();
        $rs = Http::get($this->appHost . '/targets/count', $params);
        return json_decode($rs);
    }

    /**
     * 相似识别图列表
     * @param string $image base64后识别图
     * @return mixed
     */
    public function similar($image) {
        $params['image'] = $image;
        $params = $this->getSign($params);
        $data = json_encode($params);
        $headers = ['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($data)];

        $rs = Http::post($this->appHost . '/similar/', $data, $headers);
        return json_decode($rs);
    }

    /**
     * 识别图识别级别
     * @param string $image base64后识别图
     * @return mixed
     */
    public function detection($image) {
        $params['image'] = $image;
        $params = $this->getSign($params);
        $data = json_encode($params);
        $headers = ['Content-Type: application/json; charset=utf-8', 'Content-Length: ' . strlen($data)];

        $rs = Http::post($this->appHost . '/grade/detection/', $data, $headers);
        return json_decode($rs);
    }

    /**
     * 参数签名
     * @param array $params
     * @return array
     */
    private function getSign($params = []) {
        $params['apiKey'] = $this->apiKey;
        $params['appId'] = $this->appId;
        $params['timestamp'] = time() * 1000;
        ksort($params);

        $tmp = [];
        foreach ($params as $k => $v) {
            $tmp[] = $k . $v;
        }

        $str = implode('', $tmp);
        $params['signature'] = hash('sha256', $str . $this->apiSecret);
        return $params;
    }
}

class Http {
    public static function get($url, $data, $headers = null) {
        return self::sendRequest('GET', $url, $data, $headers);
    }

    public static function patch($url, $data, $headers = null) {
        return self::sendRequest('PATCH', $url, $data, $headers);
    }

    public static function put($url, $data, $headers = null) {
        return self::sendRequest('PUT', $url, $data, $headers);
    }

    public static function delete($url, $data, $headers = null) {
        return self::sendRequest('DELETE', $url, $data, $headers);
    }

    public static function post($url, $data, $headers = null) {
        return self::sendRequest('POST', $url, $data, $headers);
    }

    public static function sendRequest($method, $url, $data = null, $headers = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        switch ($method) {
            case 'GET':
            case 'DELETE':
                if ($data) {
                    $url .= (false === strpos($url, '?')) ? '?' : '&';
                    $url .= http_build_query($data);
                }
                break;
            case 'PUT':
            case 'PATCH':
            case 'POST':
                if ($method == 'POST') {
                    curl_setopt($ch, CURLOPT_POST, 1);
                }
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
                break;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        if ('HTTPS' == strtoupper(substr($url, 0, 5))) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $str = curl_exec($ch);
        curl_close($ch);

        return $str;
    }
}
