<?php

namespace presentkim\itempopup\listener;

use pocketmine\command\{
  Command, CommandExecutor, CommandSender
};
use presentkim\itempopup\{
    /** @noinspection PhpUndefinedClassInspection */
  ItemPopupMain, util\Translation
};
use function presentkim\itempopup\util\{
  translate, in_arrayi
};

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
    $message = '';
    if (!isset($args[0])) {
        return false;
    } elseif (strcasecmp($args[0], translate('command-itempopup-set')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-set@aliases'))) {
        if (!$sender->hasPermission('itempopup.set.cmd')) {
            $message = translate('command-generic-failure@permission');
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
            $message = translate('command-itempopup-set@success', [$args[1], $args[2]]);
        } else {
            $message = translate('command-itempopup-set@usage');
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-remove')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-remove@aliases'))) {
        if (!$sender->hasPermission('itempopup.remove.cmd')) {
            $message = translate('command-generic-failure@permission');
        } elseif (isset($args[2]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int) $args[2]) >= -1) {
            $result = $plugin->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[2];
            if (!$result) { // When first query result is not exists
                $message = translate('command-itempopup-remove@failure', [$args[1], $args[2]]);
            } else {
                $plugin->query("DELETE FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];");
                $message = translate('command-itempopup-remove@success', [$args[1], $args[2]]);
            }
        } else {
            $message = translate('command-itempopup-remove@usage');
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-list')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-list@aliases'))) {
        if (!$sender->hasPermission('itempopup.list.cmd')) {
            $message = translate('command-generic-failure@permission');
        } else {
            $list = [];
            $results = $plugin->query("SELECT * FROM item_popup_list ORDER BY item_id ASC, item_damage ASC;");
            while ($row = $results->fetchArray(SQLITE3_NUM)) {
                $list[] = $row;
            }
            $max = ceil(sizeof($list) / 5);
            $page = min($max, isset($args[1]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) > 0 ? $args[1] - 1 : 0);
            $message = translate('command-itempopup-list@head', [$page, $max]);
            for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++) {
                $message .= PHP_EOL . translate('command-itempopup-list@item', $list[$i]);
            }
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-lang')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-lang@aliases'))) {
        if (!$sender->hasPermission('itempopup.lang.cmd')) {
            $message = translate('command-generic-failure@permission');
        } elseif (isset($args[1]) && is_string($args[1]) && ($args[1] = strtolower(trim($args[1])))) {
            $resource = $plugin->getResource("lang/$args[1].yml");
            if (is_resource($resource)) {
                @mkdir($plugin->getDataFolder());
                $langfilename = $plugin->getDataFolder() . "lang.yml";
                Translation::loadFromResource($resource);
                Translation::save($langfilename);
                $message = translate('command-itempopup-lang@success', [$args[1]]);
            } else {
                $message = translate('command-itempopup-lang@failure', [$args[1]]);
            }
        } else {
            $message = translate('command-itempopup-lang@usage');
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-reload')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-reload@aliases'))) {
        if (!$sender->hasPermission('itempopup.reload.cmd')) {
            $message = translate('command-generic-failure@permission');
        } else {
            $plugin->reload();
            $message = translate('command-itempopup-reload@success');
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-save')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-save@aliases'))) {
        if (!$sender->hasPermission('itempopup.save.cmd')) {
            $message = translate('command-generic-failure@permission');
        } else {
            $plugin->save();
            $message = translate('command-itempopup-save@success');
        }
    } elseif (strcasecmp($args[0], translate('command-itempopup-help')) === 0 || in_arrayi($args[0], Translation::getArray('command-itempopup-help@aliases'))) {
        if (!$sender->hasPermission('itempopup.help.cmd')) {
            $message = translate('command-generic-failure@permission');
        } else {

        }
    } else {
        return false;
    }
    $sender->sendMessage(translate('prefix') . $message);
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

