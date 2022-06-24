<?php

/**用户不应接收到的响应头 */
const RESP_DENY_HEADER = array(
	// 'Strict-Transport-Security',
	// 'Expect-CT',
	// 'CF-RAY',
	// 'Server',
	// 'CF-Cache-Status',
	// 'X-Host-Time',
	// 'Transfer-Encoding',
);

/**用户应收到的响应头 */
const RESP_ACCEPT_HEADER = array(
	// 'Date' => true,
	'Content-Type' => true,
	'Connection' => true,
	'Vary' => true,
	'X-Host-Time' => true,
	'X-XSS-Protection' => true,
	'Set-Cookie' => true,
	'Expires' => true,
	'Cache-Control' => true,
	'Pragma' => true,
	'X-Frame-Options' => true,
	// 'CF-RAY' => true,
	// 'Server' => true,
	// 'CF-Cache-Status' => true,
);

/**脚本不应发送的请求头 */
const REQ_DENY_HEADER = array(
	'Accept-Encoding' => true,
	'X-Rewrite-Url' => true,
	'Host' => true,
);

/**脚本应发送的请求头 */
const REQ_ACCEPT_HEADER = array(
// 	'Accept-Language' => true,
// 	'Accept' => true,
// 	'Cache-Control' => true,
// 	'Cookie' => true,
// 	'User-Agent' => true,
// 	'Upgrade-Insecure-Requests' => true,
// 	'Connection' => true,
// 	'Host' => true,
// 	'X-Rewrite-Url' => true,
);
