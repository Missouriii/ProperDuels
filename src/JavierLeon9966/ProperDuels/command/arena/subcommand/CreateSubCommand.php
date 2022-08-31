<?php

declare(strict_types = 1);

namespace JavierLeon9966\ProperDuels\command\arena\subcommand;

use CortexPE\Commando\args\{RawStringArgument, Vector3Argument};
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;

use JavierLeon9966\ProperDuels\arena\Arena;
use JavierLeon9966\ProperDuels\ProperDuels;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\{AssumptionFailedError, TextFormat};

class CreateSubCommand extends BaseSubCommand{

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
		if(!$this->plugin instanceof ProperDuels){
			throw new \UnexpectedValueException('§9§l» §r§cThis command was not created by§e ' . ProperDuels::class);
		}
		$arenaManager = $this->plugin->getArenaManager();
		if($arenaManager->has($args['arena'])){
			$sender->sendMessage(TextFormat::RED."§9§l» §r§cAn arena with the name '$args[arena]' already exists");
			return;
		}

		if(!$sender instanceof Player){
			throw new AssumptionFailedError(InGameRequiredConstraint::class . ' should have prevented this');
		}
		$world = $sender->getWorld();
		foreach(['firstSpawnPos', 'secondSpawnPos'] as $spawn){
			$pos = $args[$spawn]->floor();
			if(!$world->isInWorld((int)$pos->x, (int)$pos->y, (int)$pos->z)){
				$sender->sendMessage(TextFormat::RED.'§9§l» §r§cCannot set positions outside of the world');
				return;
			}
		}

		$kitManager = $this->plugin->getKitManager();
		if(isset($args['kit']) and !$kitManager->has($args['kit'])){
			$sender->sendMessage(TextFormat::RED."§9§l» §r§cNo kit was found by the name '$args[kit]'");
			return;
		}

		$arenaManager->add(new Arena(
			$args['arena'],
			$world->getFolderName(),
			$args['firstSpawnPos'],
			$args['secondSpawnPos'],
			$args['kit'] ?? null
		));
		$sender->sendMessage("§9§l» §r§aAdded new arena '$args[arena]' successfully");
	}

	public function prepare(): void{
		$this->addConstraint(new InGameRequiredConstraint($this));

		$this->setPermission('properduels.command.arena.create');

		$this->registerArgument(0, new RawStringArgument('arena'));
		$this->registerArgument(1, new Vector3Argument('firstSpawnPos'));
		$this->registerArgument(2, new Vector3Argument('secondSpawnPos'));
		$this->registerArgument(3, new RawStringArgument('kit', true));
	}
}
