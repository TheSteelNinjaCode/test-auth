<?php

use Lib\Auth\Auth;

$auth = new Auth();

echo "<pre>";
print_r($auth->getPayload());
echo "</pre>";