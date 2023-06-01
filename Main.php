<?php

declare(strict_types=1);

namespace creeperplayer20\actions;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent, PlayerQuitEvent};
use pocketmine\utils\Config;
use function date;

class Main extends PluginBase implements Listener
{
    private $config;

    function onEnable(): void
    {
        @mkdir($this->getDataFolder() . "players/");

        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        $this->saveDefaultConfig();
    }

    function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer()->getName();

        $this->config = new Config($this->getDataFolder() . "players/" . $player . ".yml", Config::YAML);

        if (!$this->config->exists("playedBefore")) {
            $this->config->set("playedBefore", false);
            $this->config->save();

            if ($this->getConfig()->get("firstTimeEnable") && !$this->config->get("playedBefore")) {
                $firstTimeJoin = $this->getConfig()->get("firstTimeJoin");

                $event->setJoinMessage($this->firstTimeJoin($firstTimeJoin, $player, $this->config));
                return;
            }
        }

        if ($this->getConfig()->get("joinEnable") && $this->config->get("playedBefore") || !$this->getConfig()->get("firstTimeEnable")) {
            $joinMsg = $this->getConfig()->get("join");

            $joinMsg = $this->replace($joinMsg, [
                "\$name" => $player,
                "\$time" => date($this->getConfig()->get("timeFormat") . ' a', time())
            ]);

            $event->setJoinMessage($joinMsg);
            return;
        }
    }

    function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer()->getName();

        if ($this->getConfig()->get("leaveEnable")) {
            $leaveMsg = $this->getConfig()->get("leave");

            $leaveMsg = $this->replace($leaveMsg, [
                "\$name" => $player,
                "\$time" => date($this->getConfig()->get("timeFormat") . ' a', time())
            ]);

            $event->setQuitMessage($leaveMsg);
        }
    }

    function replace(string $string, array $replace): string
    {
        foreach ($replace as $key => $value) {
            $string = str_replace($key, $value, $string);
        }
        return $string;
    }

    function firstTimeJoin(string $string, string $player, Config $config)
    {
        $firstTimeJoin = $this->replace($string, [
            "\$name" => $player,
            "\$time" => date($this->getConfig()->get("timeFormat") . ' a', time())
        ]);

        $this->config->set("playedBefore", true);
        $this->config->save();

        return $firstTimeJoin;
    }
}
