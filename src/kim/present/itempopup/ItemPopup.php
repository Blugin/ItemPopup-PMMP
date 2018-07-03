<?php

namespace kim\present\itempopup;

use kim\present\itempopup\command\PoolCommand;
use kim\present\itempopup\command\subcommands\{
	LangSubCommand, ListSubCommand, ReloadSubCommand, RemoveSubCommand, SaveSubCommand, SetSubCommand
};
use kim\present\itempopup\listener\PlayerEventListener;
use kim\present\itempopup\util\Translation;
use pocketmine\plugin\PluginBase;

class ItemPopup extends PluginBase{

	/** @var ItemPopup */
	private static $instance = null;

	/** @var string */
	public static $prefix = '';

	/** @return ItemPopup */
	public static function getInstance() : ItemPopup{
		return self::$instance;
	}

	/** @var PoolCommand */
	private $command;

	public function onLoad() : void{
		if(self::$instance === null){
			self::$instance = $this;
			Translation::loadFromResource($this->getResource('lang/eng.yml'), true);
		}
	}

	public function onEnable() : void{
		$this->load();
		$this->getServer()->getPluginManager()->registerEvents(new PlayerEventListener(), $this);
	}

	public function onDisable(){
		$this->save();
	}

	public function load() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}

		$this->reloadConfig();

		$langfilename = $dataFolder . 'lang.yml';
		if(!file_exists($langfilename)){
			$resource = $this->getResource('lang/eng.yml');
			fwrite($fp = fopen("{$dataFolder}lang.yml", "wb"), $contents = stream_get_contents($resource));
			fclose($fp);
			Translation::loadFromContents($contents);
		}else{
			Translation::load($langfilename);
		}

		self::$prefix = Translation::translate('prefix');
		$this->reloadCommand();
	}

	public function save() : void{
		$dataFolder = $this->getDataFolder();
		if(!file_exists($dataFolder)){
			mkdir($dataFolder, 0777, true);
		}

		$this->saveConfig();
	}

	public function reloadCommand() : void{
		if($this->command == null){
			$this->command = new PoolCommand($this, 'itempopup');
			$this->command->createSubCommand(SetSubCommand::class);
			$this->command->createSubCommand(RemoveSubCommand::class);
			$this->command->createSubCommand(ListSubCommand::class);
			$this->command->createSubCommand(LangSubCommand::class);
			$this->command->createSubCommand(ReloadSubCommand::class);
			$this->command->createSubCommand(SaveSubCommand::class);
		}
		$this->command->updateTranslation();
		$this->command->updateSudCommandTranslation();
		if($this->command->isRegistered()){
			$this->getServer()->getCommandMap()->unregister($this->command);
		}
		$this->getServer()->getCommandMap()->register(strtolower($this->getName()), $this->command);
	}

	/**
	 * @param string $name = ''
	 *
	 * @return PoolCommand
	 */
	public function getCommand(string $name = '') : PoolCommand{
		return $this->command;
	}

	/** @param PoolCommand $command */
	public function setCommand(PoolCommand $command) : void{
		$this->command = $command;
	}
}
