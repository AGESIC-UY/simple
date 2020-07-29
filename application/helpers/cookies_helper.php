<?php

function set_cookie($name, $value, $expire, $path, $domain, $secure=0, $httponly=1) {
  (!empty($_SERVER['HTTPS']) ? $secure = 1 : $secure = 0);

  ob_start();
  setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
  ob_end_flush();
}
