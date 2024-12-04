<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use TungstenVn\SeasonPass\commands\commands;
use pocketmine\item\Item;
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

        if ($this->check_instance($acts[0], "win10") && $this->check_instance($acts[1], "win10") || $this->check_instance($acts[0], "phone") && $this->check_instance($acts[1], "phone")) {
            if ($acts[0] instanceof SlotChangeAction && $acts[1] instanceof SlotChangeAction) {
                //check mobile move
                if (!$acts[0]->getInventory() instanceof PlayerCursorInventory and !$acts[1]->getInventory() instanceof PlayerCursorInventory) {
                    $sound = new soundHandle($this);
                    $sound->illigelSound($player);
                    $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
                    $item->setLore(["§r§cThả một mục để xác nhận, không di chuyển nó đến một nơi khác trên menu"]);
                    $this->menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
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
                $item = VanillaBlocks::OAK_TRAPDOOR()->setCustomName("§r§as§l【 §fThông báo §a】");
                $item->setLore(["§r§cMục này không thể được chạm vào"]);
                $this->menu->getInventory()->setItem(52, $item);
                $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
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
                        $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
                        $item->setLore(["§r§cKhông thể di chuyển sang trái nữa :3"]);
                        $this->menu->getInventory()->setItem(52, $item);
                        $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                        return;
                    }
                    $sound->moveRightLeft($player);
                    $this->corner[1] -= 7;
                    $this->menu->getInventory()->setItem(52, VanillaItems::AIR());
                    $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                } else {
                    $sound->moveRightLeft($player);
                    $this->corner[1] += 7;
                    $this->menu->getInventory()->setItem(52, VanillaItems::AIR());
                    $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                    new loadMenu($this, $player, [0, $this->corner[1]], $this->matrix);
                }
            }
        }else{
            $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cHành động đó không được phép, nếu bạn có ý định lấy\n§r§cđồ trong seasonpass, hãy nhấn vô vật phẩm bạn muốn lấy\n§r§csau đó nhấn vô khoảng trống ngoài menu seasonpass"]);
            $this->menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
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
            $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cBạn không có quyền lấy vật phẩm ở thẻ huyền thoại"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->dontHavePerm($player);
            $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
            return;
        }
        if((int) $level < $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y]){
            $a = $this->cmds->ssp->getConfig()->getNested("marker")[$txt][$y];
            $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
            $item->setLore(["§r§cBạn đang level [$level] nhưng vật phẩm này yêu cầu mức độ [$a]"]);
            $menu->getInventory()->setItem(52, $item);
            $sound = new soundHandle($this);
            $sound->notEnoughLevel($player);
            $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
            return;
        }
        if(isset($this->cmds->ssp->getConfig()->getNested($txt)[$y])){

                if(!isset($this->cmds->ssp->getConfig()->getNested("database")[$player->getName()][$type][$y])){
                    $item = $this->cmds->ssp->getConfig()->getNested($txt)[$y];
                    $item = Item::legacyJsonDeserialize($item);
                    $sound = new soundHandle($this);
                    if($player->getInventory()->canAddItem($item)){
                        $player->getInventory()->addItem($item);
                        $this->cmds->ssp->getConfig()->setNested("database.".$this->name.".".$type.".".$y,"taken");
                        $this->cmds->ssp->getConfig()->save();
                        if($type == 0){
                            $sound->normalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +9 + 2, VanillaBlocks::REDSTONE_LAMP()->asItem());
                            $this->matrix[0+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("§a§l【Ｓeason Ｐass】 §r§f➢ §eXin chúc mừng §c[".$player->getName()."]§e đã lấy §c[Item $y] §etrong §fTHẺ THÔNG THƯỜNG");
                            $item = VanillaItems::BANNER()->setColor(DyeColor::YELLOW)->setCustomName("§r§a§l【 §fChúc Mừng §a】");
                            $item->setLore(["§r§6Bạn đã lấy một mục trong thẻ §fthông thường"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                        }else{
                            $sound->royalTaken($player);
                            $sound->water($player);
                            $menu->getInventory()->setItem($y - $this->corner[1] +36 + 2, VanillaBlocks::REDSTONE_LAMP()->asItem());
                            $this->matrix[3+1][$y] = "taken";
                            $this->cmds->ssp->getServer()->broadcastMessage("§a§l【Ｓeason Ｐass】 §r§f➢ §eXin chúc mừng §c[".$player->getName()."]§e đã lấy §c[Item $y] §etrong §6THẺ HUYỀN THOẠI");
                            $item = VanillaBlocks::BANNER()->setColor(DyeColor::LIME)->setCustomName("§r§a§l【 §fChúc Mừng §a】");
                            $item->setLore(["§r§6Bạn đã lấy một mục trong thẻ §ehuyền thoại"]);
                            $menu->getInventory()->setItem(52,$item);
                            $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                        }
                        return;
                    }else{
                        $sound->illigelSound($player);
                        $player->removeCurrentWindow();
                        $player->sendMessage("§r§cKhông có vị trí trống trong kho của bạn, vui lòng dọn ít đồ rồi thử lại");
                        $this->name = null;
                        return;
                    }
                }else{
                    $sound = new soundHandle($this);
                    $sound->alreadyTaken($player);
                    $item = VanillaBlocks::OAK_TRAPDOOR()->asItem()->setCustomName("§r§a§l【 §fThông báo §a】");
                    $item->setLore(["§r§cBạn đã nhận được vật phẩm đó"]);
                    $menu->getInventory()->setItem(52, $item);
                    $player->getCursorInventory()->setItem(0, VanillaItems::AIR());
                    return;
                }

        }else{
            $sound = new soundHandle($this);
            $sound->illigelSound($player);
            $player->removeCurrentWindow();
            $player->sendMessage("§r§cĐã xảy ra lỗi, vui lòng thông báo cho admin. Thẻ mùa ID: pass2");
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