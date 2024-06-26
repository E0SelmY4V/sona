<?php

require '../lib/http-head.php';
require '../lib/main.php';

include('../lib/script.html');
die();

// 初始化
$ch = curl_init($req_url = myurl_decode());

// 伪造请求头
$host = substr($req_url, strpos($req_url, '://') + 3);
if (($pos = strpos($host, '/')) !== false) $host = substr($host, 0, $pos);
$hdr = [
	"Client-Ip: {$_SERVER['REMOTE_ADDR']}",
	"X-Forwarded-For: {$_SERVER['REMOTE_ADDR']}",
	'Accept-Encoding: gzip',
	"Host: $host",
];
foreach (getallheaders() as $k => $v) if (empty(REQ_DENY_HEADER[strtolower($k)])) $hdr[] = "$k: $v";
curl_setopt($ch, CURLOPT_HTTPHEADER, $hdr);

// 获取请求数据
if ($_POST) {
	curl_setopt($ch, CURLOPT_POST, 1); // 开启传输POST表单
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST)); // 设置表单
}
curl_setopt($ch, CURLOPT_HEADER, true); // 获取header头
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60); // 超时时间60秒
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // 跟随重定向
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 直接输出字符串
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 禁止https协议验证域名
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 禁止https协议验证ssl安全认证证书
curl_setopt($ch, CURLOPT_REFERER, pathinfo($req_url)['dirname'] . '/'); // 伪造来源
$data = curl_exec($ch);

// 如果被重定向了
if ($req_url !== ($resp_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL))) die(header('Location: ' . myurl_encode($resp_url)));

out:
// 伪造响应头
$size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$hdr = substr($data, 0, $size);
$hdr = explode("\r\n", $hdr, -2);
header(array_shift($hdr)); // HTTP/xxx响应头
$jed = true;
$enc = false;
foreach ($hdr as $h) {
	$n = strtolower(substr($h, 0, strpos($h, ':')));
	if ($jed && $n === 'content-encoding' && strpos($h, 'gzip') !== false) $enc = true and $jed = false;
	if (isset(RESP_ACCEPT_HEADER[$n])) header($h);
}

$data = substr($data, $size); // 获取身体
if ($enc) $data = gzdecode($data); // 如果被gzip加密则解密
$type = strtolower(curl_getinfo($ch, CURLINFO_CONTENT_TYPE)); // 获取mime类型
header("Content-type: $type");

// 如果是html，注入脚本
if (strpos($type, 'text/html') !== false) include '../lib/script.html';

// 替换url
foreach ([
	'image' => false,
	'text' => true,
	'java' => true,
	'json' => true,
	'audio' => false,
	'xml' => true,
	'video' => false,
	'form' => true,
] as $key => $val) {
	if (strpos($type, $key) === false) continue;
	if ($val) {
		$rule = get_reparr($resp_url);
		exit(str_ireplace($rule[0], $rule[1], $data));
	} else exit($data);
}
exit($data);
