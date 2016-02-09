<?php

$re = "/(\"license\": \"MIT\",)/";
$str = file_get_contents('./laravel/composer.json');
$subst = "$1\n    \"minimum-stability\": \"dev\",\n    \"prefer-stable\": true,";

$result = preg_replace($re, $subst, $str, 1);

file_put_contents('./laravel/composer.json',$result);
