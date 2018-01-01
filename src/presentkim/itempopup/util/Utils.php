<?php

namespace presentkim\itempopup\util;

/**
 * @param string $str
 * @param array  $strs
 *
 * @return bool
 */
function in_arrayi(string $str, array $strs) : bool{
    foreach ($strs as $key => $value) {
        if (strcasecmp($str, $value) === 0) {
            return true;
        }
    }
    return false;
}

/**
 * @param Object[] $list
 *
 * @return string[]
 */
function listToPairs(array $list) : array{
    $pairs = [];
    $size = sizeOf($list);
    for ($i = 0; $i < $size; ++$i) {
        $pairs["{%$i}"] = $list[$i];
    }
    return $pairs;
}