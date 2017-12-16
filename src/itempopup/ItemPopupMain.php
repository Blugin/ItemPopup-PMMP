<?php

namespace itempopup;

use pocketmine\command\{
  Command, CommandSender
};
use pocketmine\event\{
  block\BlockPlaceEvent, player\PlayerItemHeldEvent, Listener
};
use pocketmine\plugin\PluginBase;

class ItemPopupMain extends PluginBase{

    /** @var \itempopup\ItemPopupMain */
    private static $instance = null;

    /** @var \Sqlite3 */
    private $db;

    /**
     * @return \itempopup\ItemPopupMain
     */
    public static function getInstance(): ItemPopupMain{
        return self::$instance;
    }

    public function onLoad(){
        if (!extension_loaded('sqlite3')) {
            $this->getLogger()->debug('load sqlite3 extention');
            dl((PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . 'sqlite3.' . PHP_SHLIB_SUFFIX);
        }

        @mkdir($this->getDataFolder());
        $this->db = new \SQLITE3($this->getDataFolder() . "data.sqlite3");
        self::$instance = $this;
    }

    public function onEnable(){
        @mkdir($this->getDataFolder());
        $this->query("BEGIN;");
        $this->query("
          CREATE TABLE IF NOT EXISTS item_popup_list (
            item_id	INTEGER NOT NULL CHECK(item_id >= 0),
            item_damage INTEGER NOT NULL DEFAULT - 1 CHECK(item_damage >= -1),
            item_popup TEXT NOT NULL,
            PRIMARY KEY (item_id, item_damage)
          );");
        $this->query("COMMIT;");

        $this->getServer()->getPluginManager()->registerEvents(new class() implements Listener{

            /**
             * Array for ignore PlayerItemHeldEvent after BlockPlaceEvent
             *
             * @var array[string]\pocketmine\item\Item
             */
            private $ignore = [];

            /**
             * @param \pocketmine\event\player\PlayerItemHeldEvent $event
             */
            public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event){
                /**
                 * @var \pocketmine\item\Item $item
                 * @var \pocketmine\Player    $player
                 * @var string                $playerName
                 * @var string                $result
                 */
                $item = $event->getItem();
                $player = $event->getPlayer();
                $playerName = $player->getName();
                if (!isset($this->ignore[$playerName]) || !$this->ignore[$playerName]->equals($item, true, true)) {
                    $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = {$item->getDamage()};")->fetchArray(SQLITE3_NUM)[0];
                    if (!$result)
                        $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = -1;")->fetchArray(SQLITE3_NUM)[0];
                    if ($result)
                        $player->sendPopup($result);
                }
                if (isset($this->ignore[$playerName]))
                    unset($this->ignore[$playerName]);
            }


            public function onBlockPlaceEvent(BlockPlaceEvent $event){
                $this->ignore[$event->getPlayer()->getName()] = $event->getItem();
            }
        }, $this);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        return false;
    }

    /**
     * @param string $query
     *
     * @return \SQLite3Result
     */
    public function query(string $query): \SQLite3Result{
        return $this->db->query($query);
    }
}
