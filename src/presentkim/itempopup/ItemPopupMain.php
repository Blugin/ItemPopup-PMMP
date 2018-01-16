<?php

namespace presentkim\itempopup;

use pocketmine\command\{
  CommandExecutor, PluginCommand
};
use pocketmine\plugin\PluginBase;
use presentkim\itempopup\{
  listener\PlayerEventListener, command\CommandListener, util\Translation
};

class ItemPopupMain extends PluginBase{

    /** @var self */
    private static $instance = null;

    /** @var PluginCommand[] */
    private $commands = [];

    /** @return self */
    public static function getInstance(){
        return self::$instance;
    }

    public function onLoad(){
        if (self::$instance === null) {
            self::$instance = $this;
            $this->getServer()->getLoader()->loadClass('presentkim\itempopup\util\Utils');
            Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
        }
    }

    public function onEnable(){
        $this->load();
        $this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
    }

    public function onDisable(){
        $this->save();
    }

    public function load(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->reloadConfig();

        $langfilename = $dataFolder . 'lang.yml';
        if (!file_exists($langfilename)) {
            $resource = $this->getResource('lang/eng.yml');
            Translation::loadFromResource($resource);
            stream_copy_to_stream($resource, $fp = fopen("{$dataFolder}lang.yml", "wb"));
            fclose($fp);
        } else {
            Translation::load($langfilename);
        }

        foreach ($this->commands as $command) {
            $this->getServer()->getCommandMap()->unregister($command);
        }
        $this->commands = [];
        $this->registerCommand(new CommandListener($this), Translation::translate('command-itempopup'), 'ItemPopup', 'itempopup.cmd', Translation::translate('command-itempopup@description'), Translation::translate('command-itempopup@usage'), Translation::getArray('command-itempopup@aliases'));
    }

    public function save(){
        $dataFolder = $this->getDataFolder();
        if (!file_exists($dataFolder)) {
            mkdir($dataFolder, 0777, true);
        }

        $this->saveConfig();
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
