<?php

namespace TungstenVn\SeasonPass\menuHandle;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class loadMenu
{

    public function __construct(menuHandle $mnh, $sender, $corner, $matrix)
    {
        $this->onLoad($mnh, $sender, $corner, $matrix);
    }

    public function onLoad($mnh, Player $sender, $corner, $matrix)
    {
        for ($x = $corner[0]; $x < $corner[0] + 5; $x++) {
            for ($y = $corner[1]; $y < $corner[1] + 7; $y++) {
                $slotId = $x * 9 + $y - $corner[1] + 2;
                if (isset($matrix[$x][$y])) {
                    if (is_numeric($matrix[$x][$y])) {
                        if ($x == 0) {
                            $item = $mnh->cmds->ssp->getConfig()->getNested('normalPass')[$y];
                            //$item = unserialize(utf8_decode($item));
                            $item = Item::legacyJsonDeserialize($item);
                            $mnh->menu->getInventory()->setItem($slotId, $item);
                        } else {
                            $item = $mnh->cmds->ssp->getConfig()->getNested('royalPass')[$y];
                            //$item = unserialize(utf8_decode($item));
                            $item = Item::legacyJsonDeserialize($item);
                            $mnh->menu->getInventory()->setItem($slotId, $item);
                        }
                    } else if ($matrix[$x][$y] == "n") {
                        $mnh->menu->getInventory()->setItem($slotId, VanillaItems::AIR());
                    } else if ($matrix[$x][$y] == "taken") {
                        $mnh->menu->getInventory()->setItem($slotId, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::GREEN));
                    } else if ($matrix[$x][$y] == "none") {
                        $mnh->menu->getInventory()->setItem($slotId, VanillaBlocks::STAINED_GLASS()->setColor(DyeColor::RED));
                    } else if ($matrix[$x][$y] == "crossline") {
                        $mnh->menu->getInventory()->setItem($slotId, VanillaItems::PAPER());
                    }
                } else {
                    $mnh->menu->getInventory()->setItem($slotId, VanillaItems::AIR());
                }
            }
        }
    }
}