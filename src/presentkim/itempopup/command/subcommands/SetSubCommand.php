<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  command\PoolCommand, ItemPopupMain as Plugin, command\SubCommand
};
use function presentkim\itempopup\util\toInt;

class SetSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'set');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args) : bool{
        if (isset($args[2])) {
            $itemId = toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            $itemDamage = toInt($args[1], null, function (int $i){
                return $i >= -1;
            });
            if ($itemId !== null && $itemDamage !== null) {
                $popup = implode(' ', array_slice($args, 2));
                $this->plugin->getConfig()->set("{$itemId}:{$itemDamage}", $popup);
                $sender->sendMessage(Plugin::$prefix . $this->translate('success', $itemId, $itemDamage, $popup));
                return true;
            }
        }
        return false;
    }
}