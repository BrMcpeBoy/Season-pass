<?php

namespace TungstenVn\SeasonPass\subCommands;

use TungstenVn\SeasonPass\commands\commands;
use pocketmine\item\Item;
use pocketmine\player\Player;

class removeItem
{

    public function __construct(commands $cmds,Player $sender,array $args)
    {
        $value = $this->checkRequirement($sender, $args);
        if ($value === null) {
            return;
        }
        if($value[0] == 0){
            $normalPass = $cmds->ssp->data->getNested("normalPass");
            if(!isset($normalPass[$value[1]])){
                $sender->sendMessage("There is no item on that slot");
                return;
            }else{
                $name = Item::legacyJsonDeserialize($normalPass[$value[1]])->getName();
                unset($normalPass[$value[1]]);
                $cmds->ssp->data->setNested("normalPass",$normalPass);
                $cmds->ssp->data->save();
                $sender->sendMessage("Succeed remove a item on slot $value[1] of normalPass,which is a $name");
                return;
            }
        }else{
            $royalPass = $cmds->ssp->data->getNested("royalPass");
            if(!isset($royalPass[$value[1]])){
                $sender->sendMessage("There is no item on that slot");
                return;
            }else{
                 $name = Item::legacyJsonDeserialize($royalPass[$value[1]])->getName();
                unset($royalPass[$value[1]]);
                $cmds->ssp->data->setNested("royalPass",$royalPass);
                $cmds->ssp->data->save();
                $sender->sendMessage("Succeed remove a item on slot $value[1] of royalPass,which is a $name");
                return;
            }
        }
    }

    public function checkRequirement(Player $sender, $args)
    {
        if (!$sender->hasPermission("seasonpass.command.removeitem")) {
            $sender->sendMessage("§eSỬ DỤNG: /seasonpass help");
            return null;
        }
        if (!isset($args[1]) or !isset($args[2])) {
            $sender->sendMessage("SỬ DỤNG: /seasonpass removeitem (type) (idSlot)!");
            return null;
        }
        if ($args[1] != 0 && $args[1] != 1) {
            $sender->sendMessage("'Type' must be 0 or 1");
            return null;
        }
        return [$args[1],$args[2]];
    }

}