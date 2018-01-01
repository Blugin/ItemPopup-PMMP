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
    public function onCommand(CommandSender $sender, array $args) : bool{
        $list = [];
        $results = $this->owner->query("SELECT * FROM item_popup_list ORDER BY item_id ASC, item_damage ASC;");
        while ($row = $results->fetchArray(SQLITE3_NUM)) {
            $list[] = $row;
        }
        $max = ceil(sizeof($list) / 5);
        $page = min($max, (isset($args[0]) ? toInt($args[0], 1, function (int $i){
              return $i > 0 ? 1 : -1;
          }) : 1) - 1);
        $message = Translation::translate("$this->strId@head", $page + 1, $max);
        for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++) {
            $message .= PHP_EOL . Translation::translate("$this->strId@item", ...$list[$i]);
        }
        $sender->sendMessage("$this->prefix$message");

        return true;
    }
}