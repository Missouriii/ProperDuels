<?php

declare(strict_types = 1);

namespace JavierLeon9966\ProperDuels\command\duel\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

use JavierLeon9966\ProperDuels\ProperDuels;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\{AssumptionFailedError, TextFormat};

class QueueSubCommand extends BaseSubCommand{

	public function onRun(CommandSender $sender, string $commandLabel, array $args): void{
		if(!$this->plugin instanceof ProperDuels){
			throw new \UnexpectedValueException('This command wasn\'t created by ' . ProperDuels::class);
		}
		$arenaManager = $this->plugin->getArenaManager();
		$queueManager = $this->plugin->getQueueManager();
		if(!$sender instanceof Player){
			throw new AssumptionFailedError(InGameRequiredConstraint::class . ' should have prevented this');
		}
		$rawUUID = $sender->getUniqueId()->getBytes();
		if(isset($args['arena'])){
			$arena = $arenaManager->get($args['arena']);
			if($arena === null){
				$sender->sendMessage(TextFormat::RED."§9§l» §r§cNo arena was found by the name:§e '$args[arena]'");
				return;
			}

			if($queueManager->has($rawUUID)){
				$sender->sendMessage(TextFormat::RED.'§9§l» §r§cYou are already in a queue!');
				return;
			}

			$queueManager->add($rawUUID, $arena);
			$sender->sendMessage('§9§l» §r§aQueued!');
			return;
		}elseif($queueManager->has($rawUUID)){
			$queueManager->remove($rawUUID);
			$sender->sendMessage('§9§l» §r§aSuccessfully removed from the queue');
			return;
		}

		if(count($arenaManager->all()) === 0){
			$sender->sendMessage(TextFormat::RED.'§9§l» §r§cThere are no existing arenas');
			return;
		}
		$queueManager->add($rawUUID);
		$sender->sendMessage('§9§l» §r§aQueued!');
	}

	public function prepare(): void{
		$this->addConstraint(new InGameRequiredConstraint($this));

		$this->setPermission('properduels.command.duel.queue');

		$this->registerArgument(0, new RawStringArgument('arena', true));
	}
}
