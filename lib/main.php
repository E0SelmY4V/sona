<?php

header('Access-Control-Allow-Origin: *');

/**本机使用的协议 */
const HEAD = 'http';

/**本机域名 */
$my_dmn = explode('.', $_SERVER['HTTP_HOST']);
$my_dmn = array(array_pop($my_dmn), array_pop($my_dmn));
$my_dmn = "{$my_dmn[1]}.{$my_dmn[0]}";

/**
 * 获取长url替换数组
 * @param string $url 当前访问url
 * @return array 替换数组
 */
function rep_longUrl($url)
{
	global $my_dmn;
	$rslt = [
		'http://' => HEAD . "://sona.$my_dmn/turn.php?http://",
		'https://' => HEAD . "://sona.$my_dmn/turn.php?https://",
		'"//' => '"' . HEAD . "://sona.$my_dmn/$url/",
		'\'//' => '\'' . HEAD . "://sona.$my_dmn/$url/",
	];
	foreach ($rslt as $before => $after) $rslt[str_replace('/', '\/', $before)] = str_replace('/', '\/', $after);
	return $rslt;
}

/**脚本替换数组 */
$rep_script = [
	// 'location' => 'laoction',
];

/**
 * 获取替换数组
 * @param string $url 当前访问url
 * @return array 两个数组，分别为要替换的和替换成的
 */
function get_reparr($url)
{
	global $rep_longUrl, $rep_script;
	$url = substr($url, 0, strpos($url, '://'));
	$arr = [[], []];
	foreach ([
		rep_longUrl($url),
		$rep_script,
	] as $group) foreach ($group as $before => $after) list($arr[0][], $arr[1][]) = [$before, $after];
	return $arr;
}

/**
 * 解码网址
 * @param string $http_host 主机名
 * @return string 源网址
 */
function myurl_decode()
{
	$dmn = explode('.', $_SERVER['HTTP_HOST'], -2);
	array_shift($dmn);
	if (ctype_digit(end($dmn))) $dmn[count($dmn) - 2] .= ':' . array_pop($dmn);
	$dmn = join('.', $dmn);
	$dmn = substr_replace($dmn, '://', strpos($dmn, '.'), 1);
	$uri = $_SERVER['QUERY_STRING'];
	if (($pos = strpos($uri, '&'))) $uri[$pos] = '?';
	return "$dmn/$uri";
}

/**
 * 编码网址
 * @param string $url 源网址
 * @return string 用户访问网址
 */
function myurl_encode($url)
{
	global $my_dmn;
	$url = substr_replace($url, '.', strpos($url, '://'), 3);
	list($dmn, $uri) = (($pos = strpos($url, '/')) !== false) ? array(substr($url, 0, $pos), substr($url, $pos)) : array($url, '');
	if (($pos = strpos($url, ':')) !== false) $dmn[$pos] = '.';
	return "http://sona.$dmn.$my_dmn$uri";
}
