<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  Command, CommandSender
};
use pocketmine\event\{
  block\BlockPlaceEvent, player\PlayerItemHeldEvent, Listener
};
use pocketmine\plugin\PluginBase;

class ItemPopupMain extends PluginBase{

    /** @var \presentkim\itempopup\ItemPopupMain */
    private static $instance = null;

    /** @var \Sqlite3 */
    private $db;

    /**
     * @return \presentkim\itempopup\ItemPopupMain
     */
    public static function getInstance(): ItemPopupMain{
        return self::$instance;
    }

    public function onLoad(): void{
        if (!extension_loaded('sqlite3')) {
            $this->getLogger()->debug('load sqlite3 extention');
            dl((PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . 'sqlite3.' . PHP_SHLIB_SUFFIX);
        }

        @mkdir($this->getDataFolder());
        $this->db = new \SQLITE3($this->getDataFolder() . "data.sqlite3");
        self::$instance = $this;
    }

    public function onEnable(): void{
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
            public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event): void{
                $item = $event->getItem();
                $player = $event->getPlayer();
                $playerName = $player->getName();
                if (!isset($this->ignore[$playerName]) || !$this->ignore[$playerName]->equals($item, true, true)) {
                    $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = {$item->getDamage()};")->fetchArray(SQLITE3_NUM)[0];
                    if (!$result) // When first query result is not exists
                        $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = -1;")->fetchArray(SQLITE3_NUM)[0];
                    if ($result) // When query result is exists
                        $player->sendPopup($result);
                }
                if (isset($this->ignore[$playerName]))
                    unset($this->ignore[$playerName]);
            }


            public function onBlockPlaceEvent(BlockPlaceEvent $event): void{
                $this->ignore[$event->getPlayer()->getName()] = $event->getItem();
            }
        }, $this);
    }

    /**
     * @param \pocketmine\command\CommandSender $sender
     * @param \pocketmine\command\Command       $command
     * @param string                            $label
     * @param array                             $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if (isset($args[0])) {
            switch ($args[0]) {
                case 'set':
                    if (isset($args[3]) && is_numeric($args[1]) && ($args[1] = (int)$args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int)$args[2]) >= -1) {
                        $popup = implode(' ', array_slice($args, 3));
                        $result = ItemPopupMain::getInstance()->query("SELECT * FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[2];
                        if (!$result)  // When first query result is not exists
                            ItemPopupMain::getInstance()->query("INSERT INTO item_popup_list VALUES ($args[1], $args[2], '$popup');");
                        else
                            ItemPopupMain::getInstance()->query("
                                UPDATE item_popup_list
                                    set item_id = $args[1],
                                        item_damage = $args[2],
                                        item_popup = '$popup'
                                    WHERE item_id = $args[1] AND item_damage = $args[2];
                            ");
                        $sender->sendMessage('You have successfully set up a popup.');
                        return true;
                    }
                    break;

                case 'remove':
                    if (isset($args[2]) && is_numeric($args[1]) && ($args[1] = (int)$args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int)$args[2]) >= -1) {
                        $result = ItemPopupMain::getInstance()->query("SELECT * FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[2];
                        if (!$result)   // When first query result is not exists
                            $sender->sendMessage('It does not exist.');
                        else {
                            ItemPopupMain::getInstance()->query("DELETE FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];");
                            $sender->sendMessage('You have successfully remove a popup.');
                        }
                        return true;
                    }
                    break;
                case 'list':
                    $page = isset($args[1]) && is_numeric($args[1]) && ($args[1] = (int)$args[1]) > 0 ? $args[1] - 1 : 0;
                    $list = [];
                    $results = ItemPopupMain::getInstance()->query("SELECT * FROM item_popup_list ORDER BY item_id ASC, item_damage ASC;");
                    while ($row = $results->fetchArray(SQLITE3_NUM))
                        $list[] = $row;
                    for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++)
                        $sender->sendMessage('[' . ($i + 1) . "][{$list[$i][0]}:{$list[$i][1]}] {$list[$i][2]}");
                    return true;
                    break;
            }
        }
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
