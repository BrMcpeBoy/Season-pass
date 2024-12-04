<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use TungstenVn\SeasonPass\menuHandle\menuHandle;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\type\InvMenuTypeIds;

class createDefaultMenu
{
    public $menu;

    public function __construct(menuHandle $mnh, $sender)
    {
        $this->createMenu($sender);
    }

    public function createMenu($sender)
    {
        $menu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
        $menu->setName("§lSSP MÙA I §r(XEM CẤP /minelevel)");
        $normalBook = VanillaItems::BOOK();
        $normalBook->setCustomName("§r§a§l【 §7THẺ THÔNG THƯỜNG §a】");
        $normalBook->setLore(["§r§fMọi người đều có thể nhận vật phẩm trong thẻ này"]);

        $royalBook = VanillaItems::WRITTEN_BOOK();
        $royalBook->setCustomName("§r§a§l【 §6THẺ HUYỀN THOẠI §a】");
        $royalBook->setLore(["§r§6Mua Nó Tại /muassp để nhận các vật phẩm tại thẻ này"]);

        $menu->getInventory()->setItem(0, $normalBook);
        $menu->getInventory()->setItem(1, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME)->asItem());
        $menu->getInventory()->setItem(10, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME)->asItem());
        $menu->getInventory()->setItem(27, $royalBook);
        $menu->getInventory()->setItem(28, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW)->asItem());
        $menu->getInventory()->setItem(37, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::YELLOW)->asItem());

        $menu->getInventory()->setItem(45, VanillaItems::PAPER()->setCustomName("§r§lĐẾN TRANG BÊN TRÁI"));
        $menu->getInventory()->setItem(53, VanillaItems::PAPER()->setCustomName("§r§lĐẾN TRANG BÊN PHẢI"));

        $menu->getInventory()->setItem(18, VanillaItems::PAPER());
        $menu->getInventory()->setItem(19, VanillaItems::PAPER());
        $this->menu = $menu;
    }
}
