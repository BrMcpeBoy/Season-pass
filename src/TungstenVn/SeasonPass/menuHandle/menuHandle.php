<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\player\Player;
use TungstenVn\SeasonPass\commands\commands;
use pocketmine\item\{Item, StringToItemParser, LegacyStringToItemParser};
use pocketmine\item\ItemFactory;
use pocketmine\event\Listener;
use pocketmine\inventory\PlayerCursorInventory;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\inventory\transaction\action\CreativeInventoryAction;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use muqsit\invmenu\inventory\InvMenuInventory;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use TungstenVn\SeasonPass\sounds\soundHandle;

class menuHandle implements Listener
{

    public $cmds;
    public $name; //player's name
    public $corner = [0, 0]; //slot 2 in chest is corner
    public $menu;
    public $matrix;
    public function __construct(commands $cmds,Player $sender)
    {
        $this->cmds = $cmds;

        $matrix = new createMatrix($this, $sender);
        $this->matrix = $matrix->getMatrix();

        $menu = new createDefaultMenu($this, $sender);
        $this->menu = $menu->menu;
        $this->menu->send($sender);

        $this->name = $sender->getName();

        new loadMenu($this, $sender, [0, 0], $this->matrix);

    }

    public function onTransaction(InventoryTransactionEvent $ev): void
    {
        $player = $ev->getTransaction()->getSource();
        if ($player->getName() != $this->name) {
            return;
        }
        $ev->cancel();
        $acts = array_values($ev->getTransaction()->getActions());
        if ($acts[0] instanceof CreativeInventoryAction || $acts[1] instanceof CreativeInventoryAction) {
            return;
        }
        if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10") || $this->check_instance($acts[0], "phone") && $this->check_instance($acts[1], "phone")) {
            if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
                //check mobile move
                if (!$acts[0]->getInventory() instanceof PlayerCursorInventory and !$acts[1]->getInventory() instanceof PlayerCursorInventory) {
                    $sound = new soundHandle($this);
                    $sound->illigelSound($player);
                    $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
                    $item->setLore(["§r§cDrop an item to confirm, do not move it to another place on the menu"]);
                    $this->menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                    return;
                }
            }
            $slotId = 0;
            //works both win10 and mobile player
            if ($acts[0] instanceof DropItemAction) {
                $slotId = $acts[1]->getSlot();
            } else {
                if ($acts[0]->getInventory() instanceof PlayerCursorInventory) {
                    $slotId = $acts[1]->getSlot();
                } else {
                    $slotId = $acts[0]->getSlot();
                }
            }
            $legalSlot = [2, 3, 4, 5, 6, 7, 8, 29, 30, 31, 32, 33, 34, 35, 45, 53];
            if (!in_array($slotId, $legalSlot)) {
                $sound = new soundHandle($this);
                $sound->illigelSound($player);
                $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
                $item->setLore(["§r§cThis item cannot be touched."]);
                $this->menu->getInventory()->setItem(52, $item);
                $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                return;
            }

            if ($slotId < 9) {
                $x = 0;
                $y = $slotId - 2 + $this->corner[1];
                $this->takeItem(0, $y, $player, $this->menu);
            } else if ($slotId < 36) {
                $x = 3;
                $y = $slotId - 27 - 2 + $this->corner[1];
                $this->takeItem(1, $y, $player, $this->menu);
            } else {
                $sound = new soundHandle($this);
                if ($slotId == 45) {
                    if ($this->corner[1] == 0) {
                        $sound = new soundHandle($this);
                        $sound->illigelSound($player);
                        $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
                        $item->setLore(["§r§cCan't move left anymore :3"]);
                        $this->menu->getInventory()->setItem(52, $item);
                        $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                        return;
                    }
                    $sound->moveRightLeft($player);
                    $this->corner[1] -= 7;
                    $this->menu->getInventory()->setItem(52, LegacyStringToItemParser::getInstance()->parse(0,0,1));
                    $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                } else {
                    $sound->moveRightLeft($player);
                    $this->corner[1] += 7;
                    $this->menu->getInventory()->setItem(52, LegacyStringToItemParser::getInstance()->parse(0,0,1));
                    $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                }
            }
        }else{
            $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cThat action is not allowed, if you intend to get\n§r§citems in the seasonpass, click on the item you want to get\n§r§cthen click on the empty space outside the seasonpass menu"]);
            $this->menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
        }
    }
    public  function takeItem(int $type,int $y,Player $player,$menu){
        //dont change $txt;
        $txt = "normalPass";
        if($type != 0){
            $txt = "royalPass";
        }

        $levelApi = $this->cmds->ssp->levelApi;
        $level = $levelApi->getLevel($player);

        //Linh động giữa việc dùng perm hoặc dùng mảng nhét tên vào
        if($type == 1 and !$player->hasPermission("seasonpass.royalpass")){
            $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cYou are not allowed to get items in legendary cards"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->dontHavePerm($player);
            $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
            return;
        }
        if((int) $level < $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y]){
            $a = $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y];
            $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cYou are level [$level] but this item requires level [$a]"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->notEnoughLevel($player);
            $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
            return;
        }
        if(isset($this->cmds->ssp->getConfig()->getNested($txt)[$y])){

                if(!isset($this->cmds->ssp->getConfig()->getNested("database")[$player->getName()][$type][$y])){
                    $item = $this->cmds->ssp->getConfig()->getNested($txt)[$y];
                    $item = Item::jsonDeserialize($item);
                    $sound = new soundHandle($this);
                    if($player->getInventory()->canAddItem($item)){
                        $player->getInventory()->addItem($item);
                        $this->cmds->ssp->getConfig()->setNested("database.".$this->name.".".$type.".".$y,"taken");
                        $this->cmds->ssp->getConfig()->save();
                        if($type == 0){
                            $sound->normalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +9 + 2, LegacyStringToItemParser::getInstance()->parse(241, 5, 1));
                            $this->matrix[0+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("§a§l【Ｓeason Ｐass】 §r§f➢ §eXin chúc mừng §c[".$player->getName()."]§e đã lấy §c[Item $y] §etrong §fTHẺ THÔNG THƯỜNG");
                            $item = LegacyStringToItemParser::getInstance()->parse(325, 4, 1)->setCustomName("§r§a§l【 §fChúc Mừng §a】");
                            $item->setLore(["§r§6You have taken an item in the §fregular tag"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                        }else{
                            $sound->royalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +36 + 2, LegacyStringToItemParser::getInstance()->parse(241, 5, 1));
                            $this->matrix[3+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("§a§l【Ｓeason Ｐass】 §r§f➢ §eXin chúc mừng §c[".$player->getName()."]§e đã lấy §c[Item $y] §etrong §6THẺ HUYỀN THOẠI");
                            $item = LegacyStringToItemParser::getInstance()->parse(325, 5, 1)->setCustomName("§r§a§l【 §fChúc Mừng §a】");
                            $item->setLore(["§r§6You have taken an item from the §elegendary tag"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                        }
                        return;
                    }else{
                        $sound->illigelSound($player);
                        $player->removeCurrentWindow();
                        $player->sendMessage("§r§cThere are no empty slots in your inventory, please clear some items and try again.");
                        $this->name = null;
                        return;
                    }
                }else{
                    $sound = new soundHandle($this);
                    $sound->alreadyTaken($player);
                    $item = LegacyStringToItemParser::getInstance()->parse(76, 0, 1)->setCustomName("§r§a§l【 §fThông báo §a】");
                    $item->setLore(["§r§cYou have received that item."]);
                    $menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, LegacyStringToItemParser::getInstance()->parse(0, 0, 0));
                    return;
                }

        }else{
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->removeCurrentWindow();
            $player->sendMessage("§r§cAn error occurred, please notify the admin. Season card ID: pass2");
            return;
        }
    }
    public function check_instance($var, $type)
    {
        if ($type == "win10") {
            if ($var instanceof DropItemAction) {
                return false;
            }
            if ($var->getInventory() instanceof PlayerCursorInventory || $var->getInventory() instanceof InvMenuInventory) {
                return true;
            }
            return false;
        } else if ($type == "phone") {
            if ($var instanceof DropItemAction) {
                return true;
            } else {
                if ($var->getInventory() instanceof InvMenuInventory) {
                    return true;
                }
            }
            return false;
        }
    }

    public function onClose(InventoryCloseEvent $ev)
    {
        $player = $ev->getPlayer();
        if ($player->getName() == $this->name) {
            $this->name = null;
            return;
        }
    }

    public function onQuit(PlayerQuitEvent $ev)
    {
        $player = $ev->getPlayer();
        if ($player->getName() == $this->name) {
            $this->name = null;
            return;
        }
    }
}
