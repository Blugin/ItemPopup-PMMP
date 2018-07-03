<?php

namespace kim\present\itempopup\command\subcommands;

use kim\present\itempopup\command\{
	PoolCommand, SubCommand
};
use kim\present\itempopup\ItemPopup as Plugin;
use pocketmine\command\CommandSender;

class SaveSubCommand extends SubCommand{

	public function __construct(PoolCommand $owner){
		parent::__construct($owner, 'save');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		$this->plugin->save();
		$sender->sendMessage(Plugin::$prefix . $this->translate('success'));

		return true;
	}
}