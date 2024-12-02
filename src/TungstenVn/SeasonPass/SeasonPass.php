<?php

namespace TungstenVn\SeasonPass;

use pocketmine\plugin\PluginBase;
use pocketmine\Player; 
use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\event\Event;
use TungstenVn\SeasonPass\commands\commands;
use muqsit\invmenu\InvMenuHandler;
use jojoe77777\FormAPI\SimpleForm;

class SeasonPass extends PluginBase implements Listener {

    public $levelApi;

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        if(!InvMenuHandler::isRegistered()){
            InvMenuHandler::register($this);
        }
        $this->levelApi = $this->getServer()->getPluginManager()->getPlugin("MineLevel");
        if($this->levelApi == null){
            $this->getServer()->getLogger()->info("\n\n§cSeasonPass >API cấp bị thiếu, không thể bật plugin\n");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        if(!method_exists($this->levelApi, "getLevel")){
            $this->getServer()->getLogger()->info("\n\n§cSeasonPass > Plugin cấp độ không có hàm getLevel (), hàm này trả về một số, vì vậy không thể bật plugin này\n");
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }
        $this->saveDefaultConfig();
        $cmds = new commands($this);
        $this->getServer()->getCommandMap()->register("seasonpass", $cmds);
        $this->getServer()->getPluginManager()->registerEvents($cmds,$this);
	}
}
