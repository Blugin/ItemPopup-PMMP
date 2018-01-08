<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  ItemPopupMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\itempopup\util\toInt;

class SetSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-itempopup-set', 'itempopup.set.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if (isset($args[2])) {
            $itemId = toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            $itemDamage = toInt($args[1], null, function (int $i){
                return $i >= -1;
            });
            if ($itemId !== null && $itemDamage !== null) {
                $popup = implode(' ', array_slice($args, 2));
                $this->owner->getConfig()->set("{$itemId}:{$itemDamage}", $popup);
                $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('success'), $itemId, $itemDamage, $popup));
                return true;
            }
        }
        return false;
    }
}