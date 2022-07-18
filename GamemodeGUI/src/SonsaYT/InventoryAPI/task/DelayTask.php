<?php

namespace SonsaYT\InventoryAPI\task;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use SonsaYT\InventoryAPI\block\inventory\ChestInventory;

class DelayTask extends Task {

    public $player;
    public $inventory;

    public function __construct(Player $player, ChestInventory $inventory) {
        $this->player = $player;
        $this->inventory = $inventory;
    }

    public function onRun() : void {
        $this->player->setCurrentWindow($this->inventory);
    }

}
