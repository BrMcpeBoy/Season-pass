<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\item\ItemFactory;
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
        $normalBook = ItemFactory::getInstance()->get(340, 0, 1);
        $normalBook->setCustomName("§r§a§l【 §7THẺ THÔNG THƯỜNG §a】");
        $normalBook->setLore(["§r§fMọi người đều có thể nhận vật phẩm trong thẻ này"]);

        $royalBook = ItemFactory::getInstance()->get(387, 0, 1);
        $royalBook->setCustomName("§r§a§l【 §6THẺ HUYỀN THOẠI §a】");
        $royalBook->setLore(["§r§6Mua Nó Tại /muassp để nhận các vật phẩm tại thẻ này"]);

        $menu->getInventory()->setItem(0, $normalBook);
        $menu->getInventory()->setItem(1, ItemFactory::getInstance()->get(160, 5, 1));
        $menu->getInventory()->setItem(10, ItemFactory::getInstance()->get(160, 5, 1));
        $menu->getInventory()->setItem(27, $royalBook);
        $menu->getInventory()->setItem(28, ItemFactory::getInstance()->get(160, 4, 1));
        $menu->getInventory()->setItem(37, ItemFactory::getInstance()->get(160, 4, 1));

        $menu->getInventory()->setItem(45, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("§r§lĐẾN TRANG BÊN TRÁI"));
        $menu->getInventory()->setItem(53, ItemFactory::getInstance()->get(339, 0, 1)->setCustomName("§r§lĐẾN TRANG BÊN PHẢI"));

        $menu->getInventory()->setItem(18, ItemFactory::getInstance()->get(399, 0, 1));
        $menu->getInventory()->setItem(19, ItemFactory::getInstance()->get(399, 0, 1));
        $this->menu = $menu;
    }
}
