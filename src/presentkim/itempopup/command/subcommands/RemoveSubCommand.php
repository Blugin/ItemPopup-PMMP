<?php

namespace presentkim\itempopup\command\subcommands;

use pocketmine\command\CommandSender;
use presentkim\itempopup\{
  ItemPopupMain as Plugin, util\Translation, command\SubCommand
};

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
        if (isset($args[1]) && is_numeric($args[0]) && ($args[0] = (int) $args[0]) >= 0 && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= -1) {
            $result = $this->owner->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[0] AND item_damage = $args[1];")->fetchArray(SQLITE3_NUM)[0];
            if (!$result) { // When first query result is not exists
                $sender->sendMessage($this->prefix . Translation::translate("$this->strId@failure", $args[0], $args[1]));
            } else {
                $this->owner->query("DELETE FROM item_popup_list WHERE item_id = $args[0] AND item_damage = $args[1];");
                $sender->sendMessage($this->prefix . Translation::translate("$this->strId@success", $args[0], $args[1]));
            }
            return true;
        } else {
            return false;
        }
    }
}