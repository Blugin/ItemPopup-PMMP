<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\event\{
  block\BlockPlaceEvent, player\PlayerItemHeldEvent, Listener
};
use pocketmine\plugin\PluginBase;
use presentkim\itempopup\{
    /** @noinspection PhpUndefinedClassInspection */
  listener\CommandListener, util\Translation
};
use function presentkim\itempopup\util\translate;

class ItemPopupMain extends PluginBase{

    /** @var \presentkim\itempopup\ItemPopupMain */
    private static $instance = null;

    /** @var \Sqlite3 */
    private $db;

    /** @var \pocketmine\command\PluginCommand[] */
    private $commands = [];

    /**
     * @return \presentkim\itempopup\ItemPopupMain
     */
    public static function getInstance() : ItemPopupMain{
        return self::$instance;
    }

    public function onLoad() : void{
        // register instance
        self::$instance = $this;

        // init data.sqlite3
        if (!extension_loaded('sqlite3')) {
            $this->getLogger()->debug('load sqlite3 extention');
            /** @noinspection PhpDeprecationInspection */
            dl((PHP_SHLIB_SUFFIX === 'dll' ? 'php_' : '') . 'sqlite3.' . PHP_SHLIB_SUFFIX);
        }
        @mkdir($this->getDataFolder());
        $this->db = new \SQLITE3($this->getDataFolder() . "data.sqlite3");
    }

    public function onEnable() : void{
        $this->reload();

        // register event listeners
        $this->getServer()->getPluginManager()->registerEvents(new class() implements Listener{

            /**
             * Array for ignore PlayerItemHeldEvent after BlockPlaceEvent
             *
             * @var \pocketmine\item\Item[] array[string => \pocketmine\item\Item]
             */
            private $ignore = [];

            /**
             * @param \pocketmine\event\player\PlayerItemHeldEvent $event
             */
            public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event) : void{
                $item = $event->getItem();
                $player = $event->getPlayer();
                $playerName = $player->getName();
                if (!isset($this->ignore[$playerName]) || !$this->ignore[$playerName]->equals($item, true, true)) {
                    $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = {$item->getDamage()};")->fetchArray(SQLITE3_NUM)[0];
                    if (!$result) { // When first query result is not exists
                        $result = ItemPopupMain::getInstance()->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = -1;")->fetchArray(SQLITE3_NUM)[0];
                    }
                    if ($result) { // When query result is exists
                        $player->sendPopup($result);
                    }
                }
                if (isset($this->ignore[$playerName])) {
                    unset($this->ignore[$playerName]);
                }
            }

            public function onBlockPlaceEvent(BlockPlaceEvent $event) : void{
                $this->ignore[$event->getPlayer()->getName()] = $event->getItem();
            }
        }, $this);
    }

    /**
     * @param string $query
     *
     * @return \SQLite3Result
     */
    public function query(string $query) : \SQLite3Result{
        return $this->db->query($query);
    }

    public function reload() : void{
        // load db
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

        // load lang
        $langfilename = $this->getDataFolder() . "lang.yml";
        if (!file_exists($langfilename)) {
            Translation::loadFromResource($this->getResource('lang/eng.yml'));
            Translation::save($langfilename);
        } else {
            Translation::load($langfilename);
        }

        // unregister commands
        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->unregister($command);
        }
        $this->commands = [];

        // register commands
        /** @noinspection PhpUndefinedClassInspection */
        $this->registerCommand(new CommandListener(), translate('command-itempopup'), 'ItemPopup', 'itempopup.cmd', translate('command-itempopup@description'), translate('command-itempopup@usage'), Translation::getArray('command-itempopup@aliases'));
    }

    public function save() : void{
        // save lang
        $langfilename = $this->getDataFolder() . "lang.yml";
        if (!file_exists($langfilename)) {
            Translation::loadFromResource($this->getResource('lang/eng.yml'));
            Translation::save($langfilename);
        } else {
            Translation::load($langfilename);
        }
    }

    /**
     * @param \pocketmine\command\CommandExecutor $executor
     * @param                                     $name
     * @param                                     $fallback
     * @param                                     $permission
     * @param string                              $description
     * @param null                                $usageMessage
     * @param string[]                            $aliases
     */
    private function registerCommand(CommandExecutor $executor, $name, $fallback, $permission, $description = "", $usageMessage = null, array $aliases = null) : void{
        $command = new PluginCommand($name, $this);
        $command->setExecutor($executor);
        $command->setPermission($permission);
        $command->setDescription($description);
        $command->setUsage($usageMessage ?? ("/" . $name));
        if (is_array($aliases)) {
            $command->setAliases($aliases);
        }

        $this->getServer()->getCommandMap()->register($fallback, $command);
        $this->commands[] = $command;
    }
}
