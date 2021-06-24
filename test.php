<?php

co('ayoub');
echo 'test';

function co($name, $value = '', $expire = 0, $path = null, $domain = null, $secure = true, $httpOnly = true)
{
    setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
}