<?php

namespace JobUINa;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use jojoe77777\FormAPI\SimpleForm;
use EconomyHb\Main;

class Main extends PluginBase {

    private $economyPlugin;

    public function onEnable(): void {
        $this->getLogger()->info("JobUINa has been enabled!");

        // Initialize EconomyHb
        $this->economyPlugin = $this->getServer()->getPluginManager()->getPlugin("EconomyHb");

        if ($this->economyPlugin === null) {
            $this->getLogger()->warning("EconomyHb not found. Make sure it's installed and enabled.");
        }
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (strtolower($command->getName()) === "checkeconomy") {
                $this->checkPlayerEconomy($sender);
            } else {
                $this->openJobUI($sender);
            }
        } else {
            $sender->sendMessage("This command can only be used in-game.");
        }
        return true;
    }

    public function openJobUI(Player $player) {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return;
            }

            switch ($data) {
                case 0:
                    $this->joinMiningJob($player);
                    break;

                case 1:
                    $this->joinWoodcuttingJob($player);
                    break;

                case 2:
                    $this->joinHunterJob($player);
                    break;
            }
        });

        $form->setTitle("Job Selection");
        $form->setContent("Choose your job:");
        $form->addButton("Mining");  // Job 1
        $form->addButton("Woodcutting");  // Job 2
        $form->addButton("Hunter");  // Job 3

        $player->sendForm($form);
    }

    private function joinMiningJob(Player $player) {
        $this->giveSalary($player, "Mining", 20);
        $player->sendMessage("You joined the Mining job. Salary: $20");
    }

    private function joinWoodcuttingJob(Player $player) {
        $this->giveSalary($player, "Woodcutting", 15);
        $player->sendMessage("You joined the Woodcutting job. Salary: $15");
    }

    private function joinHunterJob(Player $player) {
        $this->giveSalary($player, "Hunter", 190);
        $player->sendMessage("You joined the Hunter job. Salary: $190");
    }

    private function giveSalary(Player $player, string $job, int $amount) {
        if ($this->economyPlugin !== null) {
            $this->economyPlugin->giveSalary($player, $job, $amount);
        }
    }

    private function checkPlayerEconomy(Player $player) {
        if ($this->economyPlugin !== null) {
            $economy = $this->economyPlugin->getPlayerEconomy($player);
            $player->sendMessage("Your economy: $".$economy);
        }
    }
}
