<?php

namespace itempopup;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class ItemPopupMain extends PluginBase{

    public function onLoad(){
        if (!extension_loaded('sqlite3')) {
            $this->getLogger()->debug('load sqlite3 extention');
            dl((PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '' . 'sqlite3.' . PHP_SHLIB_SUFFIX);
        }
    }

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $db = new \SQLITE3($this->getDataFolder() . "data.sqlite3");
        $db->query("BEGIN;");
        $db->query("
          CREATE TABLE IF NOT EXISTS popup_list (
            item_id	INTEGER NOT NULL CHECK(item_id >= 0),
            item_damage INTEGER NOT NULL DEFAULT - 1 CHECK(item_damage >= -1),
            item_popup TEXT NOT NULL
          );");
        $db->query("COMMIT;");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        return false;
    }
}
