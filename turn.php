<?php
require 'lib.php';
header('Location: ' . myurl_encode($_SERVER['QUERY_STRING']));
