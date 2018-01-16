<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  command\PoolCommand, ItemPopupMain as Plugin, command\SubCommand
};
use function presentkim\itempopup\util\toInt;

class RemoveSubCommand extends SubCommand{

    public function __construct(PoolCommand $owner){
        parent::__construct($owner, 'remove');
    }

    /**
     * @param CommandSender $sender
     * @param String[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, array $args){
        if (isset($args[1])) {
            $itemId = toInt($args[0], null, function (int $i){
                return $i >= 0;
            });
            $itemDamage = toInt($args[1], null, function (int $i){
                return $i >= -1;
            });
            if ($itemId !== null && $itemDamage !== null) {
                $config = $this->owner->getConfig();
                $key = "{$itemId}:{$itemDamage}";
                if (!$config->exists($key)) {
                    $sender->sendMessage(Plugin::$prefix . $this->translate('failure', $itemId, $itemDamage));
                } else {
                    $config->remove($key);
                    $sender->sendMessage(Plugin::$prefix . $this->translate('success', $itemId, $itemDamage));
                }
                return true;
            }
        }
        return false;
    }
}