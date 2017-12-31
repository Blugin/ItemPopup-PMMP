<?php

namespace presentkim\itempopup\util;

class Translation{

    /** @var string[string] */
    private static $lang = [];

    /**
     *  static function for load global functions
     */
    public static function loadForFunc() : void{
        \presentkim\itempopup\ItemPopupMain::getInstance()->getLogger()->debug('presentkim\itempopup\util\Utils loaded');
    }
    
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
    public static function translate(string $strId, array $params = null) : string{
        if (isset(self::$lang[$strId])) {
            $value = self::$lang[$strId];
            if (is_array($value)) {
                $value = $value[array_rand($value)];
            }
            if (is_string($value)) {
                return is_array($params) ? strtr($value, self::listToPairs($params)) : $value;
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
        $size = sizeOf($list);
        for ($i = 0; $i < $size; ++$i) {
            $pairs["{%$i}"] = $list[$i];
        }
        return $pairs;
    }

    /**
     * @param string $strId
     *
     * @return string[] | null
     */
    public static function getArray(string $strId) : array{
        if (isset(self::$lang[$strId])) {
            $value = self::$lang[$strId];
            return is_array($value) ? $value : null;
        }
        return null;
    }
}

function translate(string $strId, array $params = []) : string{
    return Translation::translate($strId, $params);
}