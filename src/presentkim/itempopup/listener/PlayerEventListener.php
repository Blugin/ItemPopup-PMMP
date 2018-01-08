<?php

namespace presentkim\itempopup\listener;

use pocketmine\event\{
  block\BlockPlaceEvent, player\PlayerItemHeldEvent, Listener
};
use presentkim\itempopup\ItemPopupMain;

class PlayerEventListener implements Listener{

    /**
     * Array for ignore PlayerItemHeldEvent after BlockPlaceEvent
     *
     * @var \pocketmine\item\Item[] array[string => \pocketmine\item\Item]
     */
    private $ignore = [];

    /** @var ItemPopupMain */
    private $owner = null;

    public function __construct(){
        $this->owner = ItemPopupMain::getInstance();
    }

    /** @param PlayerItemHeldEvent $event */
    public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event){
        $item = $event->getItem();
        $player = $event->getPlayer();
        $playerName = $player->getName();
        if (!$item->hasCustomName() && (!isset($this->ignore[$playerName]) || !$this->ignore[$playerName]->equals($item, true, true))) {
            $result = $this->owner->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = {$item->getDamage()};")->fetchArray(SQLITE3_NUM)[0];
            if (!$result) { // When first query result is not exists
                $result = $this->owner->query("SELECT item_popup FROM item_popup_list WHERE item_id = {$item->getId()} AND item_damage = -1;")->fetchArray(SQLITE3_NUM)[0];
            }
            if ($result) { // When query result is exists
                $player->sendPopup($result);
            }
        }
        if (isset($this->ignore[$playerName])) {
            unset($this->ignore[$playerName]);
        }
    }

    /** @param BlockPlaceEvent $event */
    public function onBlockPlaceEvent(BlockPlaceEvent $event){
        $this->ignore[$event->getPlayer()->getName()] = $event->getItem();
    }
}