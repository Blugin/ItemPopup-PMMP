<?php

namespace presentkim\itempopup\util;

class Translation{

    /** @var string[string] */
    private static $lang = [];

    /**
     * @param string $filename
     */
    public static function load(string $filename) : void{
        self::$lang = yaml_parse_file($filename);
    }

    /**
     * @param resource $resource
     */
    public static function loadFromResource($resource) : void{
        if (is_resource($resource)) {
            self::$lang = yaml_parse(stream_get_contents($resource));
        }
    }

    /**
     * @param string $filename
     *
     * @return bool Returns TRUE on
     *              success.
     */
    public static function save(string $filename) : bool{
        @mkdir(dirname($filename), 0755, true);
        return yaml_emit_file($filename, self::$lang);
    }

    /**
     * @param string   $strId
     * @param string[] $params = []
     *
     * @return string
     */
    public static function translate(string $strId, array $params = []) : string{
        if (isset(self::$lang[$strId])) {
            $value = self::$lang[$strId];
            if (is_string($value)) {
                return strtr($value, self::listToPairs($params));
            } elseif (is_array($value)) {
                return strtr($value[array_rand($value)], self::listToPairs($params));
            } else {
                return "$strId is not string";
            }
        }
        return "Undefined \$strId : $strId";
    }

    /**
     * @param Object[] $list
     *
     * @return string[]
     */
    public static function listToPairs(array $list) : array{
        $pairs = [];
        foreach ($list as $key => $value) {
            $pairs["{%$key}"] = (string) $value;
        }
        return $pairs;
    }
}

function translate(string $strId, array $params = []) : string{
    return Translation::translate($strId, $params);
}