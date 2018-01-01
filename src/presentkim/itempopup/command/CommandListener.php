<?php

namespace presentkim\itempopup\command;

use pocketmine\command\{
  Command, CommandExecutor, CommandSender
};
use presentkim\itempopup\{
  ItemPopupMain, util\Translation
};
use function presentkim\itempopup\util\{
  translate, in_arrayi
};

class CommandListener implements CommandExecutor{

    /**
     * @param CommandSender $sender
     * @param Command       $command
     * @param string        $label
     * @param string[]      $args
     *
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        $plugin = ItemPopupMain::getInstance();
        if (!isset($args[0])) {
            return false;
        } else {
            $message = '';
            /**
             * \Closure[] $subcommands
             */
            $subcommands = [
              'set'    => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  if (isset($args[3]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int) $args[2]) >= -1) {
                      $popup = implode(' ', array_slice($args, 3));
                      $result = $plugin->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[0];
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
                      $message = translate("$strId@success", $args[1], $args[2]);
                      return true;
                  } else {
                      return false;
                  }
              },
              'remove' => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  if (isset($args[2]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) >= 0 && is_numeric($args[2]) && ($args[2] = (int) $args[2]) >= -1) {
                      $result = $plugin->query("SELECT item_id FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];")->fetchArray(SQLITE3_NUM)[0];
                      if (!$result) { // When first query result is not exists
                          $message = translate("$strId@failure", $args[1], $args[2]);
                      } else {
                          $plugin->query("DELETE FROM item_popup_list WHERE item_id = $args[1] AND item_damage = $args[2];");
                          $message = translate("$strId@success", $args[1], $args[2]);
                      }
                      return true;
                  } else {
                      return false;
                  }
              },
              'list'   => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  $list = [];
                  $results = $plugin->query("SELECT * FROM item_popup_list ORDER BY item_id ASC, item_damage ASC;");
                  while ($row = $results->fetchArray(SQLITE3_NUM)) {
                      $list[] = $row;
                  }
                  $max = ceil(sizeof($list) / 5);
                  $page = min($max, isset($args[1]) && is_numeric($args[1]) && ($args[1] = (int) $args[1]) > 0 ? $args[1] - 1 : 0);
                  $message = translate("$strId@head", $page, $max);
                  for ($i = $page * 5; $i < ($page + 1) * 5 && $i < count($list); $i++) {
                      $message .= PHP_EOL . translate("$strId@item", ...$list[$i]);
                  }
                  return true;
              },
              'lang'   => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  if (isset($args[1]) && is_string($args[1]) && ($args[1] = strtolower(trim($args[1])))) {
                      $resource = $plugin->getResource("lang/$args[1].yml");
                      if (is_resource($resource)) {
                          @mkdir($plugin->getDataFolder());
                          $langfilename = $plugin->getDataFolder() . "lang.yml";
                          Translation::loadFromResource($resource);
                          Translation::save($langfilename);
                          $message = translate("$strId@success", $args[1]);
                      } else {
                          $message = translate("$strId@failure", $args[1]);
                      }
                      return true;
                  } else {
                      return false;
                  }
              },
              'reload' => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  $plugin->reload();
                  $message = translate("$strId@success");
                  return true;
              },
              'save'   => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{
                  $plugin->save();
                  $message = translate("$strId@success");
                  return true;
              },
              'help'   => function (string $strId) use ($sender, $args, $plugin, &$message) : bool{

                  return true;
              },
            ];
            foreach ($subcommands as $key => $value) {
                $aliases = Translation::getArray("command-itempopup-$key@aliases");
                if (strcasecmp($args[0], translate("command-itempopup-$key")) === 0 || $aliases && in_arrayi($args[0], $aliases)) {
                    if (!$sender->hasPermission("itempopup.$key.cmd")) {
                        $message = translate('command-generic-failure@permission');
                    } elseif (!$value("command-itempopup-$key")) {
                        $message = translate("command-itempopup-$key@usage");
                    }
                    $sender->sendMessage(translate('prefix') . $message);
                    return true;
                }
            }
            return false;
        }
    }
}