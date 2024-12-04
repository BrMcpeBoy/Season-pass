<?php

namespace TungstenVn\SeasonPass\subCommands;

use TungstenVn\SeasonPass\commands\commands;
use pocketmine\item\Item;
use pocketmine\player\Player;
use TungstenVn\SeasonPass\SeasonPass;

class addItem {

    public function __construct(commands $cmds, Player $sender, array $args)
    {
        $value = $this->checkRequirement($sender, $args,$cmds);
        if ($value === null) {
            return;
        }
        $this->addItemToDataBase($cmds, $value[0], $value[1], $value[2], $sender);
    }

    public function addItemToDataBase(Commands $cmds, Item $item, int $type, int $idSlot, Player $sender){
        if ($type == 0) {
            $array = $cmds->ssp->data->getNested('normalPass');
            //$item = utf8_encode(serialize($item));
            //$array[$idSlot] = $item;
            $cmds->ssp->data->setNested("normalPass", $array);
            $cmds->ssp->data->save();
            $sender->sendMessage("Thêm đồ vào thẻ thông thường thành công");
           } else {
            $array = $cmds->ssp->data->getNested('royalPass');
            //$item = utf8_encode(serialize($item));
            //$array[$idSlot] = $item;
            $cmds->ssp->data->setNested("royalPass", $array);
            $cmds->ssp->data->save();
            $sender->sendMessage("Thêm đồ vào thẻ huyền thoại thành công");
        }
    }

    public function checkRequirement(Player $sender, $args, commands $cmds){
        if (!$sender->hasPermission("seasonpass.command.additem")) {
            $sender->sendMessage("§l§eSỬ DỤNG: /seasonpass help");
            return null;
        }

        $item = $sender->getInventory()->getItemInHand();
        if ($item->getTypeId() == 0){
            $sender->sendMessage("Bạn phải cầm 1 vật phẩm!");
            return null;
        }

        if (!isset($args[1]) or !isset($args[2]) or !isset($args[3])) {
            $sender->sendMessage("SỬ DỤNG: /seasonpass additem (type) (idSlot) (level to get)!");
            return null;
        }

        if ($args[1] != 0 && $args[1] != 1) {
            $sender->sendMessage("'Type' phải là 0 hoặc 1");
            return null;
        }

        if (!is_numeric($args[2])) {
            $sender->sendMessage("'idSlot' phải là một con số");
            return null;
        }

        // Only save to database if all checks passed
        if ($args[1] == 0){
            $cmds->ssp->data->setNested("marker.normalPass." . $args[2], (int) $args[3]);
            $cmds->ssp->data->save();
        } else {
            $cmds->ssp->data->setNested("marker.royalPass." . $args[2], (int) $args[3]);
            $cmds->ssp->data->save();
        }

        return [$item, $args[1], $args[2]];
    }


}
