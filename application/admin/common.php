<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
function password($password, $encrypt='') {
    $pwd = array();
    $pwd['encrypt'] =  $encrypt ? $encrypt : create_randstr();
    $pwd['password'] = md5(md5(trim($password)).$pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
function create_randstr($type='string',$num=6){
    $config = array(
                'number'=>'1234567890',
                'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'string'=>'1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
            );
    $string = $config[$type];
    $code = '';
    $strlen = strlen($string) -1;
    for($i = 0; $i < $num; $i++){  //默认18位推广码
        $code .= $string{mt_rand(0, $strlen)};
    }
    return $code;
}