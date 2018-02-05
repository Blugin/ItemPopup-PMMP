<?php

namespace presentkim\itempopup\listener;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use presentkim\itempopup\ItemPopup as Plugin;

class PlayerEventListener implements Listener{

    /**
     * Array for ignore PlayerItemHeldEvent after BlockPlaceEvent
     *
     * @var \pocketmine\item\Item[] array[string => \pocketmine\item\Item]
     */
    private $ignore = [];

    /** @var Plugin */
    private $owner = null;

    public function __construct(){
        $this->owner = Plugin::getInstance();
    }

    /** @param PlayerItemHeldEvent $event */
    public function onPlayerItemHeldEvent(PlayerItemHeldEvent $event) : void{
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
    public function onBlockPlaceEvent(BlockPlaceEvent $event) : void{
        $this->ignore[$event->getPlayer()->getName()] = $event->getItem();
    }
}