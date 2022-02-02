<?php

declare(strict_types=1);

namespace creeperplayer20\actions;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\Server;

class Main extends PluginBase implements Listener {

public function onEnable() : void{

$this->getServer()->getPluginManager()->registerEvents($this, $this);
$this->reloadConfig();
$this->saveDefaultConfig();
$this->getConfig()->save();

}

public function onDisable() : void{

$this->getLogger()->info("[§aC20§r - §eActions§r] §4Plugin is disabled!");

}

public function onJoin(PlayerJoinEvent $event){

$player = $event->getPlayer();
$name = $player->getName();
$event->setJoinMessage(str_replace("\$name", $name, $this->getConfig()->get("join")));;

}

public function onQuit(PlayerQuitEvent $event){

$player = $event->getPlayer();
$name = $player->getName();
$event->setQuitMessage(str_replace("\$name", $name, $this->getConfig()->get("leave")));

}
}
