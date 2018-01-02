<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\plugin\PluginBase;
use presentkim\itempopup\{
  listener\PlayerEventListener, command\CommandListener,util\Translation
};
use function presentkim\itempopup\util\extensionLoad;

class ItemPopupMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var \Sqlite3 */
    private $db;

    /** @var PluginCommand[] */
    private $commands = [];

    /** @return self */
    public static function getInstance() : self{
        return self::$instance;
    }

    public function onLoad() : void{
        // register instance
        self::$instance = $this;

        // load utils
        $this->getServer()->getLoader()->loadClass('presentkim\itempopup\util\Utils');

        // init data.sqlite3
        extensionLoad('sqlite3');
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }
        $this->db = new \SQLITE3($dataFolder . 'data.sqlite3');
    }

    public function onEnable() : void{
        $this->load();

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

    public function load() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // load db
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
        $langfilename = $dataFolder . 'lang.yml';
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
        $this->registerCommand(new CommandListener($this), Translation::translate('command-itempopup'), 'ItemPopup', 'itempopup.cmd', Translation::translate('command-itempopup@description'), Translation::translate('command-itempopup@usage'), Translation::getArray('command-itempopup@aliases'));
    }

    public function save() : void{
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // save lang
        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            Translation::loadFromResource($this->getResource('lang/eng.yml'));
            Translation::save($langfilename);
        } else {
            Translation::load($langfilename);
        }
    }

    /**
     * @param CommandExecutor $executor
     * @param                 $name
     * @param                 $fallback
     * @param                 $permission
     * @param string          $description
     * @param null            $usageMessage
     * @param array|null      $aliases
     */
    private function registerCommand(CommandExecutor $executor, $name, $fallback, $permission, $description = "", $usageMessage = null, array $aliases = null) : void{
        $command = new PluginCommand($name, $this);
        $command->setExecutor($executor);
        $command->setPermission($permission);
        $command->setDescription($description);
        $command->setUsage($usageMessage ?? ('/' . $name));
        if (is_array($aliases)) {
            $command->setAliases($aliases);
        }

        $this->getServer()->getCommandMap()->register($fallback, $command);
        $this->commands[] = $command;
    }
}
