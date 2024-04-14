<?php

require 'lib/main.php';

$url = $_SERVER['QUERY_STRING'];
if ($url[0] && $url[0] === '-') $url = substr(substr_replace($url, ':/', strpos($url, '/'), 0), 1);
header('Location: ' . myurl_encode($url));
