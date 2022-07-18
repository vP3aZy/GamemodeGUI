<?php

namespace gmgui;

//USES:

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Arrow;
use pocketmine\item\Cookie;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\Steak;
use pocketmine\item\VanillaItems;
use pocketmine\player\GameMode;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getLogger()->alert("Lade Events...");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->alert("Lade InventoriAPI");
        $this->inventoryApi = $this->getServer()->getPluginManager()->getPlugin("InventoryAPI");
        $this->getLogger()->info("§aDas Plugin wurde erfolgreich geladen!");
    }

    public function onDisable(): void {
        $this->getLogger()->alert("Das Plugin wurde deaktiviert!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        switch ($command->getName()) {

            case "gmg":
                if ($sender instanceof Player) {
                    if ($sender->hasPermission("gmg.use")) {
                        $this->openMyChest($sender);
                        break;
                    } else {
                        $sender->sendMessage("§bGamemodeGUI§7: §4Dafür hast du keine Rechte!");
                        break;
                    }
                } else {
                    $sender->sendMessage("§4Da stimmt irgendwas nicht, bist du ein Spieler?");
                    break;
                }
        }
        return true;
    }

    public function openMyChest(Player $player) {
        $inventory = $this->inventoryApi->createChestGUI(); // Single chest
        $inventory->setName("§bGamemodeGUI§7:"); // Set name
        $inventory->setViewOnly(); // Prevent user from getting the item
        $item = VanillaItems::COOKIE();
        $item2 = VanillaItems::STEAK();
        $item3 = VanillaItems::ARROW();
        $item->setCustomName("§1Creative");
        $item2->setCustomName("§2Survival");
        $item3->setCustomName("§3Spectator");
        $inventory->setItem(10, $item); // Set item
        $inventory->setItem(13, $item2);
        $inventory->setItem(16, $item3);
        $inventory->setClickCallback([$this, "clickFunction"]); // Add click callback
        $inventory->send($player); // Send inventory to user

    }

    public function clickFunction(Player $player, Inventory $inventory, Item $source, Item $target, int $slot) {

        if ($source instanceof Cookie) {
            $player->sendTitle("§1Creative", "§7Equiped!");
            $player->setGamemode(GameMode::CREATIVE());
        }
        if ($source instanceof Steak) {
            $player->sendTitle("§2Survival", "§7Equiped!");
            $player->setGamemode(GameMode::SURVIVAL());
        }
        if ($source instanceof Arrow) {
            $player->sendTitle("§3Spectator", "§7Equiped!");
            $player->setGamemode(GameMode::SPECTATOR());
        }
    }
}