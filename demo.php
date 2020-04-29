<?php

include 'EasyARClientSdkCRS.php';

// 获取地址 https://portal.easyar.cn/apikey/list
$apiKey = 'API Key';
$apiSecret = 'API Secret';

// 获取地址 https://portal.easyar.cn/crs/list
$crsAppId = 'CRS AppId';
$crsCloudUrl = 'Cloud URL(Server-end (Target Mangement) URL)';


$sdk = new EasyARClientSdkCRS($apiKey, $apiSecret, $crsAppId, $crsCloudUrl);

$rs = $sdk->ping();
print_r($rs);


/*
$rs = $sdk->targetsV3(1, 10);
if ($rs->statusCode == 0) {
    print_r($rs->result);
} else {
    print_r($rs);
}
*/

/*
$rs = $sdk->info('72ade957-10cf-4d17-bbda-9ab215bc35b8');
if ($rs->statusCode == 0) {
	print_r($rs->result);
} else {
	print_r($rs);
}
*/

/*
$params = [
	'name' => 'image 1',
	'active' => '1',
	'size' => '1',
	'meta' => base64_encode('hello world'),
	'image' => base64_encode(file_get_contents('1.jpg')),
];

$rs = $sdk->targetAdd($params);
if ($rs->statusCode == 0) {
	print_r($rs->result);
} else {
	print_r($rs);
}
*/

/*
$params = [
	'name' => 'update image',
	'active' => '0',
	'size' => '1',
	'meta' => base64_encode('hello world'),
	'image' => base64_encode(file_get_contents('1.jpg')),
];
$rs = $sdk->targetUpdate('104be3c2-8018-4402-98a9-d579691241d6', $params);
if ($rs->statusCode == 0) {
	print_r($rs->result);
} else {
    print_r($rs);
}
*/

/*
$rs = $sdk->delete('104be3c2-8018-4402-98a9-d579691241d6');
if ($rs->statusCode == 0) {
	print_r($rs->result);
} else {
	print_r($rs);
}
*/

/*
$rs = $sdk->targetsCount();
if ($rs->statusCode == 0) {
	print_r($rs->result->count);
} else {
	print_r($rs);
}
*/

/*
$image = base64_encode(file_get_contents('1.jpg'));
$rs = $sdk->similar($image);
if ($rs->statusCode == 0) {
	print_r($rs->result->results);
} else {
	print_r($rs);
}
*/

/*
$image = base64_encode(file_get_contents('1.jpg'));
$rs = $sdk->detection($image);
if ($rs->statusCode == 0) {
	print_r($rs->result->grade);
} else {
	print_r($rs);
}
*/