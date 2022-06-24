<?php

/**本机使用的协议 */
const HEAD = 'http';

/**本机域名 */
$my_dmn = explode('.', $_SERVER['HTTP_HOST']);
$my_dmn = array(array_pop($my_dmn), array_pop($my_dmn));
$my_dmn = "{$my_dmn[1]}.{$my_dmn[0]}";

/**
 * 获取替换数组
 * @param string $url 当前访问url
 * @return array 两个数组，分别为要替换的和替换成的
 */
function get_reparr($url) {
	global $my_dmn;
	$url = substr($url, 0, strpos($url, '://'));
	$arr = [[
		'http://',
		'https://',
		'"//',
		'\'//',
	], [
		HEAD . "://sona.$my_dmn/turn.php?http://",
		HEAD . "://sona.$my_dmn/turn.php?https://",
		'"' . HEAD . "://sona.$my_dmn/$url/",
		'\'' . HEAD . "://sona.$my_dmn/$url/",
	]];
	for ($i = 0; $i < 2; $i++) foreach ($arr[$i] as $value) $arr[$i][] = str_replace('/', '\/', $value);
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