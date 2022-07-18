<?php

namespace SonsaYT\InventoryAPI\block\inventory;

use pocketmine\block\VanillaBlocks;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;
use pocketmine\block\tile\Nameable;
use pocketmine\world\Position;
use SonsaYT\InventoryAPI\Main;
use SonsaYT\InventoryAPI\task\DelayTask;

class DoubleChestInventory extends ChestInventory {

    public $main;
    protected $name = "Double Chest";

    public function __construct(Main $main) {
        $this->main = $main;
        parent::__construct($main, 54);
    }

    public function onClose(Player $player) : void {
        // Real block
        $packet = UpdateBlockPacket::create(
            BlockPosition::fromVector3($this->holder->add(1, 0, 0)),
            RuntimeBlockMapping::getInstance()->toRuntimeId($player->getWorld()->getBlock($this->holder)->getFullId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        );
        $player->getNetworkSession()->sendDataPacket($packet);
        parent::onClose($player);
    }

    public function send(Player $player){
        // Set holder
        $this->holder = new Position((int)$player->getPosition()->getX(), (int)$player->getPosition()->getY() + 3, (int)$player->getPosition()->getZ(), $player->getWorld());

        // Fake block left
        $packet = UpdateBlockPacket::create(
            BlockPosition::fromVector3($this->holder),
            RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::CHEST()->getFullId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        // Fake block right
        $packet = UpdateBlockPacket::create(
            BlockPosition::fromVector3($this->holder->add(1, 0, 0)),
            RuntimeBlockMapping::getInstance()->toRuntimeId(VanillaBlocks::CHEST()->getFullId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        // Fake tile left
        $nbt = new CompoundTag();
        $nbt->setString(Nameable::TAG_CUSTOM_NAME, $this->getName());
        $nbt->setInt("pairx", $this->holder->x + 1);
        $nbt->setInt("pairz", $this->holder->z);

        $packet = BlockActorDataPacket::create(
            BlockPosition::fromVector3($this->holder),
            new CacheableNbt($nbt)
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        // Fake tile right
        $nbt = new CompoundTag();
        // $nbt->setInt("pairx", $this->holder->x); // Not needed?
        // $nbt->setInt("pairz", $this->holder->z); // Not needed?

        $packet = BlockActorDataPacket::create(
            BlockPosition::fromVector3($this->holder->add(1, 0, 0)),
            new CacheableNbt($nbt)
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        // Add window with delay
        $this->main->getScheduler()->scheduleDelayedTask(new DelayTask($player, $this), 20);
    }

}