<?php

namespace TungstenVn\SeasonPass\subCommands;

use pocketmine\player\Player;
use pocketmine\item\Item;

class setItemInfo
{

    public function __construct(int $type,Player $sender,array $args)
    {
        $value = $this->checkRequirement($sender, $args,$type);
        if ($value === null) {
            return;
        }
        if($type == 0){
            $this->setItemName($sender,$value);
        }else{
            $this->setItemLore($sender,$value);
        }
    }

    public function setItemName(Player $sender,string $txt)
    {
        $item = $sender->getInventory()->getItemInHand();
        $item->setCustomName("§r".$txt);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage("Change custom name succeed");
    }
    public function setItemLore(Player $sender,string $txt)
    {
        $item = $sender->getInventory()->getItemInHand();
        $item->setLore(["§r".$txt]);
        $sender->getInventory()->setItemInHand($item);
        $sender->sendMessage("Change lore succeed");
    }
    public function checkRequirement(Player $sender,array $args,int $type)
    {
        if (!$sender->hasPermission("seasonpass.command.setitem")) {
            $sender->sendMessage("§eSỬ DỤNG: /seasonpass help");
            return null;
        }
        $item = $sender->getInventory()->getItemInHand();
        if ($item->getTypeId() === 0){
            $sender->sendMessage("You must be holding an item!");
            return null;
        }
        unset($args[0]);
        $txt = implode(" ",$args);
        $txt = str_replace("/n","\n",$txt);
        return $txt;
    }

}