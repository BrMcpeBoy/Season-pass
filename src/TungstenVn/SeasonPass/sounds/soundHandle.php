<?php

namespace TungstenVn\SeasonPass\sounds;

use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
use TungstenVn\SeasonPass\menuHandle\menuHandle;

class soundHandle {


    public $mnh;
    public function __construct(menuHandle $mnh) {
      $this->mnh = $mnh;
    }
    public function moveRightLeft(Player $player) {
      $this->livingRoom($player, "game.player.attack.strong");
    }
    public function normalTaken(Player $player) {
      $this->livingRoom($player, "random.levelup");
    }
    public function royalTaken(Player $player) {
      $this->livingRoom($player, "firework.twinkle");
    }
    public function illigelSound(Player $player) {
      $this->livingRoom($player, "mob.horse.angry");
    }
    public function alreadyTaken(Player $player) {
        $this->livingRoom($player, "mob.sheep.say");
    }
    public function dontHavePerm(Player $player) {
        $this->livingRoom($player, "mob.elderguardian.curse");
    }
    public function notEnoughLevel(Player $player) {
        $this->livingRoom($player, "mob.panda.bite");
    }
    public function water(Player $player) {
        $this->livingRoom($player, "random.totem");
    }
    public function livingRoom(Player $player, string $txt) {
      $packet = new PlaySoundPacket();
      $packet->soundName = $txt;
      $packet->x = $player->getPosition()->getX();
      $packet->y = $player->getPosition()->getY();
      $packet->z = $player->getPosition()->getZ();
      $packet->volume = 1;
      $packet->pitch = 1;
      $player->getNetworkSession()->sendDataPacket($packet);
    }
}
