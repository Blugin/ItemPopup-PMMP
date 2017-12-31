<?php

namespace presentkim\itempopup\listener;

use pocketmine\command\{
  Command, CommandExecutor, CommandSender
};
use presentkim\itempopup\{
    /** @noinspection PhpUndefinedClassInspection */
  ItemPopupMain, util\Translation
};
use function presentkim\itempopup\util\translate;
use function strtolower;


/**
 * @param \pocketmine\command\CommandSender $sender
 * @param \pocketmine\command\Command       $command
 * @param string                            $label
 * @param string[]                          $args
 *
 * @return bool
 */
function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
    $plugin = ItemPopupMain::getInstance();
    if (!isset($args[0])) {
        return false;
    } elseif ($args[0] == translate('command-itempopup-set')) {
        if (!$sender->hasPermission('itempopup.set.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } elseif (isset($args[3]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int) $args[2]) >= -1) {
            $popup = implode(' ', array_slice($args, 3));
            $result = $plugin->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[2];
            if (!$result) { // When first query result is not exists
                $plugin->query("INSERT INTO item_popup_list VALUES ($args[1], $args[2], '$popup');");
            } else {
                $plugin->query("
                    UPDATE item_popup_list
                        set item_id = $args[1],
                        item_damage = $args[2],
                        item_popup = '$popup'
                    WHERE item_id = $args[1] AND item_damage = $args[2];
                ");
            }
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-set@success', [$args[1], $args[2]]));
        } else {
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-set@usage'));
        }
    } elseif ($args[0] == translate('command-itempopup-remove')) {
        if (!$sender->hasPermission('itempopup.remove.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } elseif (isset($args[2]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int) $args[2]) >= -1) {
            $result = $plugin->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[2];
            if (!$result) { // When first query result is not exists
                $sender->sendMessage(translate('prefix') . translate('command-itempopup-remove@failure', [$args[1], $args[2]]));
            } else {
                $plugin->query("DELETE FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];");
                $sender->sendMessage(translate('prefix') . translate('command-itempopup-remove@success', [$args[1], $args[2]]));
            }
        } else {
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-remove@usage'));
        }
    } elseif ($args[0] == translate('command-itempopup-list')) {
        if (!$sender->hasPermission('itempopup.list.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } else {
            $page = isset($args[1]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) > 0 ? $args[1] - 1 : 0;
            $list = [];
            $results = $plugin->query("SELECT * FROM item_popup_list ORDER BY item_id ASC, item_damage ASC;");
            while ($row = $results->fetchArray(SQLITE3_NUM)) {
                $list[] = $row;
            }
            for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++) {
                $sender->sendMessage(translate('prefix') . '[' . ($i + 1) . "][{$list[$i][0]}:{$list[$i][1]}] {$list[$i][2]}");
            }
        }
    } elseif ($args[0] == translate('command-itempopup-lang')) {
        if (!$sender->hasPermission('itempopup.lang.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } elseif (isset($args[1]) && is_string($args[1]) && ($args[1] = strtolower(trim($args[1])))) {
            $resource = $plugin->getResource("lang/$args[1].yml");
            if (is_resource($resource)) {
                @mkdir($plugin->getDataFolder());
                $langfilename = $plugin->getDataFolder() . "lang.yml";
                Translation::loadFromResource($resource);
                Translation::save($langfilename);
                $sender->sendMessage(translate('prefix') . translate('command-itempopup-lang@success', [$args[1]]));
            } else {
                $sender->sendMessage(translate('prefix') . translate('command-itempopup-lang@failure', [$args[1]]));
            }
        } else {
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-lang@usage'));
        }
    } elseif ($args[0] == translate('command-itempopup-reload')) {
        if (!$sender->hasPermission('itempopup.reload.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } else {
            $plugin->reload();
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-reload@success'));
        }
    } elseif ($args[0] == translate('command-itempopup-save')) {
        if (!$sender->hasPermission('itempopup.save.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } else {
            $plugin->save();
            $sender->sendMessage(translate('prefix') . translate('command-itempopup-save@success'));
        }
    } elseif ($args[0] == translate('command-itempopup-help')) {
        if (!$sender->hasPermission('itempopup.help.cmd')) {
            $sender->sendMessage(translate('prefix') . translate('command-generic-failure@permission'));
        } else {

        }
    } else {
        return false;
    }
    return true;
}


/**
 * @desc present return type compatibility issues
 *
 * @url https://gist.github.com/PresentKim/d99ba2a7625d17223b7a6a58a72e09de
 */
if ((new \ReflectionClass("\\pocketmine\\command\\CommandExecutor"))->getMethod("onCommand")->hasReturnType()) {
    class CommandListener implements CommandExecutor{

        /** @noinspection PhpHierarchyChecksInspection */
        public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
            return onCommand($sender, $command, $label, $args);
        }
    }
} else {
    class CommandListener implements CommandExecutor{

        /** @noinspection PhpHierarchyChecksInspection */
        public function onCommand(CommandSender $sender, Command $command, $label, array $args){
            return onCommand($sender, $command, $label, $args);
        }
    }
}

