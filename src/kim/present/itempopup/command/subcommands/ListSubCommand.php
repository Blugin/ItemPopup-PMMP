<?php

namespace kim\present\itempopup\command\subcommands;

use kim\present\itempopup\command\{
	PoolCommand, SubCommand
};
use kim\present\itempopup\ItemPopup as Plugin;
use kim\present\itempopup\util\Utils;
use pocketmine\command\CommandSender;

class ListSubCommand extends SubCommand{

	public function __construct(PoolCommand $owner){
		parent::__construct($owner, 'list');
	}

	/**
	 * @param CommandSender $sender
	 * @param String[]      $args
	 *
	 * @return bool
	 */
	public function onCommand(CommandSender $sender, array $args) : bool{
		$list = [];
		foreach($this->plugin->getConfig()->getAll() as $key => $value){
			$items = explode(':', $key);
			$items[] = $value;
			$list[] = $items;
		}

		$max = ceil(count($list) / 5);
		$page = min($max, (isset($args[0]) ? Utils::toInt($args[0], 1, function(int $i){
							return $i > 0 ? 1 : -1;
						}) : 1) - 1);
		$sender->sendMessage(Plugin::$prefix . $this->translate('head', $page + 1, $max));
		for($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++){
			$sender->sendMessage($this->translate('item', ...$list[$i]));
		}
		return true;
	}
}