<?php

declare(strict_types=1);

namespace creeperplayer20\actions;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use function date;

class Main extends PluginBase implements Listener {

    public function onEnable() : void{

        @mkdir($this->getDataFolder() . "players/");
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        
        date_default_timezone_set($this->getConfig()->get("timezone"));
        
        $this->saveDefaultConfig();

    }
        
    public function onJoin(PlayerJoinEvent $event){

        $player = $event->getPlayer()->getName();
        
        //FirstTimeMsg
        $this->config = new Config($this->getDataFolder() . "players/" . strtolower($player . ".yml"), Config::YAML, array(
            "playedBefore" => false
        ));
        $this->config;
        $this->config->save();
        
        if($this->getConfig()->get("firstTimeEnable") == true && $this->config->get("playedBefore") == false){
        
            $firstTimeJoin = $this->getConfig()->get("firstTimeJoin");
            $firstTimeJoin = str_replace("\$name", $player, $firstTimeJoin);
            $firstTimeJoin = str_replace("\$time", date($this->getConfig()->get("timeFormat") . ' a', time()), $firstTimeJoin);
        
            $this->config = new Config($this->getDataFolder() . "players/" . strtolower($player . ".yml"), Config::YAML);
            $this->config->set("playedBefore" , true);
            $this->config->save();
            
            $event->setJoinMessage($firstTimeJoin); 
        //JoinMsg
        } else if($this->getConfig()->get("joinEnable") == true && $this->config->get("playedBefore") == true || $this->getConfig()->get("firstTimeEnable") == false){ 

            $joinMsg = $this->getConfig()->get("join");

            $joinMsg = str_replace("\$name", $player, $joinMsg);
            $joinMsg = str_replace("\$time", date($this->getConfig()->get("timeFormat") . ' a', time()), $joinMsg);

            $event->setJoinMessage($joinMsg);

        }
    }
        
    public function onQuit(PlayerQuitEvent $event){
        
        $player = $event->getPlayer()->getName();
        
        //LeaveMsg
        if($this->getConfig()->get("leaveEnable") == true){

            $leaveMsg = $this->getConfig()->get("leave");
            $leaveMsg = str_replace("\$name", $player, $leaveMsg);
            $leaveMsg = str_replace("\$time", date($this->getConfig()->get("timeFormat") . ' a', time()), $leaveMsg);
        
            $event->setQuitMessage($leaveMsg);

        }
    }
}
