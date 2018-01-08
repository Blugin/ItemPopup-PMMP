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
            $config = $this->owner->getConfig();
            $result = $config->get("{$item->getId()}:{$item->getDamage()}", null) ?? $config->get("{$item->getId()}:-1", null);
            if ($result !== null) {
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