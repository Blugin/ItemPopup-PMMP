<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  ItemPopupMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\itempopup\util\toInt;

class ListSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-itempopup-list', 'itempopup.list.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        $list = [];
        foreach ($this->owner->getConfig()->getAll() as $key => $value) {
            $items = explode(':', $key);
            $items[] = $value;
            $list[] = $items;
        }

        $max = ceil(sizeof($list) / 5);
        $page = min($max, (isset($args[0]) ? toInt($args[0], 1, function (int $i){
              return $i > 0 ? 1 : -1;
          }) : 1) - 1);
        $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('head'), $page + 1, $max));
        for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++) {
            $sender->sendMessage(Translation::translate($this->getFullId('item'), ...$list[$i]));
        }
        return true;
    }
}