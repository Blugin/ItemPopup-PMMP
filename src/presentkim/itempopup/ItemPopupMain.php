<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\plugin\PluginBase;
use presentkim\itempopup\{
  listener\PlayerEventListener, command\CommandListener, util\Translation
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
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        if (self::$instance === null) {
            // register instance
            self::$instance = $this;

            // load utils
            $this->getServer()->getLoader()->loadClass('presentkim\itempopup\util\Utils');

            // Dispose of existing data
            $sqlite3Path = "{$this->getDataFolder()}data.sqlite3";
            if (file_exists($sqlite3Path)) {
                extensionLoad('sqlite3');

                $db = new \SQLITE3($sqlite3Path);
                $results = $db->query("SELECT * FROM item_popup_list;");
                $config = $this->getConfig();
                while ($result = $results->fetchArray(SQLITE3_NUM)) {
                    $key = mb_convert_encoding("{$result[0]}:{$result[1]}", "ASCII", "UTF-8");
                    $value = mb_convert_encoding($result[2], "ASCII", "UTF-8");
                    $config->set($key, $value);
                }
                $this->saveConfig();
                unset($db, $results, $result);
                unlink($sqlite3Path);
            }
        }
    }

    public function onEnable(){
        $this->load();

        // register event listeners
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
    }

    /**
     * @param string $query
     *
     * @return \SQLite3Result
     */
    public function query(string $query){
        return $this->db->query($query);
    }

    public function load(){
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

    public function save(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        // save lang
        Translation::save($dataFolder . 'lang.yml');
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
    private function registerCommand(CommandExecutor $executor, $name, $fallback, $permission, $description = "", $usageMessage = null, array $aliases = null){
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
