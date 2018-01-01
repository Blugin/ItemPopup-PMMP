<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\plugin\PluginBase;
use presentkim\itempopup\{
  listener\PlayerEventListener, command\CommandListener
};
use presentkim\itempopup\util\{
  Translation
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

        // load utils
        $this->getServer()->getLoader()->loadClass('presentkim\itempopup\util\Utils');
        $this->getServer()->getLoader()->loadClass('presentkim\itempopup\util\Translation');

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
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
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
        $this->query("
            CREATE TABLE IF NOT EXISTS item_popup_list (
                item_id     INTEGER NOT NULL            CHECK(item_id >= 0),
                item_damage INTEGER NOT NULL DEFAULT -1 CHECK(item_damage >= -1),
                item_popup  TEXT    NOT NULL,
                PRIMARY KEY (item_id, item_damage)
            );
            COMMIT;
        ");

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
