<?php

use smartQQ\Core\App;

if (!function_exists('app')) {
    /**
     * Get the available container instance.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    function app($abstract = null)
    {
        if (is_null($abstract)) {
            return App::getInstance();
        }

        return (App::getInstance())->$abstract;
    }
}

if (!function_exists('hash33')) {
    /**
     * 生成ptqrtoken的哈希函数.
     *
     * @param string $string
     *
     * @return int
     */
    function hash33($string)
    {
        $e = 0;
        $n = strlen($string);
        for ($i = 0; $n > $i; ++$i) {
            //64位php才进行32位转换
            if (PHP_INT_MAX > 2147483647) {
                $e = toUint32val($e);
            }
            $e += ($e << 5) + charCodeAt($string, $i);
        }

        return 2147483647 & $e;
    }
}

if (!function_exists('toUint32val')) {
    /**
     * 入转换为32位无符号整数，若溢出，则只保留低32位.
     *
     * @see http://outofmemory.cn/code-snippet/18291/out-switch-32-place-sign-integer-spill-maintain-32-place
     *
     * @param mixed $var
     *
     * @return float|int|string
     */
    function toUint32val($var)
    {
        if (is_string($var)) {
            if (PHP_INT_MAX > 2147483647) {
                $var = intval($var);
            } else {
                $var = floatval($var);
            }
        }
        if (!is_int($var)) {
            $var = intval($var);
        }
        if ((0 > $var) || ($var > 4294967295)) {
            $var &= 4294967295;
            if (0 > $var) {
                $var = sprintf('%u', $var);
            }
        }

        return $var;
    }
}

if (!function_exists('charCodeAt')) {
    /**
     * 计算字符的unicode，类似js中charCodeAt
     * [Link](http://www.phpjiayuan.com/90/225.html).
     *
     * @param string $str
     * @param int $index
     *
     * @return null|number
     */
    function charCodeAt($str, $index)
    {
        $char = mb_substr($str, $index, 1, 'UTF-8');
        if (mb_check_encoding($char, 'UTF-8')) {
            $ret = mb_convert_encoding($char, 'UTF-32BE', 'UTF-8');
            return hexdec(bin2hex($ret));
        } else {
            return null;
        }
    }
}

if (!function_exists('hashArgs')) {
    /**
     * hash.
     *
     * @param int $uin
     * @param string $ptWebQQ
     *
     * @return string
     */
    function hashArgs($uin, $ptWebQQ)
    {
        $x = array(
            0, $uin >> 24 & 0xff ^ 0x45,
            0, $uin >> 16 & 0xff ^ 0x43,
            0, $uin >> 8 & 0xff ^ 0x4f,
            0, $uin & 0xff ^ 0x4b,
        );
        for ($i = 0; $i < 64; ++$i) {
            $x[($i & 3) << 1] ^= ord(substr($ptWebQQ, $i, 1));
        }
        $hex = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F');
        $hash = '';
        for ($i = 0; $i < 8; ++$i) {
            $hash .= $hex[$x[$i] >> 4 & 0xf] . $hex[$x[$i] & 0xf];
        }

        return $hash;
    }
}


if (!function_exists('p')) {

    function p($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        die;
    }
}

