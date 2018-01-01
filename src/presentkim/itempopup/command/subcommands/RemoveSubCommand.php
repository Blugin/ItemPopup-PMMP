<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  ItemPopupMain as Plugin, util\Translation, command\SubCommand
};
use function presentkim\itempopup\util\toInt;

class RemoveSubCommand extends SubCommand{

    public function __construct(Plugin $owner){
        parent::__construct($owner, Translation::translate('prefix'), 'command-itempopup-remove', 'itempopup.remove.cmd');
    }

    /**
     * @param CommandSender $sender
     * @param array         $args
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
                $result = $this->owner->query("SELECT item_id FROM item_popup_list WHERE item_id = $itemId AND item_damage = $itemDamage;")->fetchArray(SQLITE3_NUM)[0];
                if (!$result) { // When first query result is not exists
                    $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('failure'), $itemId, $itemDamage));
                } else {
                    $this->owner->query("DELETE FROM item_popup_list WHERE item_id = $itemId AND item_damage = $itemDamage;");
                    $sender->sendMessage($this->prefix . Translation::translate($this->getFullId('success'), $itemId, $itemDamage));
                }
                return true;
            }
        }
        return false;
    }
}