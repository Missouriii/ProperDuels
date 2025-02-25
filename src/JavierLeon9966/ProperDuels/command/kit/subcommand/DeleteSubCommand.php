<?php

declare(strict_types = 1);

namespace JavierLeon9966\ProperDuels\command\kit\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;

use JavierLeon9966\ProperDuels\ProperDuels;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DeleteSubCommand extends BaseSubCommand{

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void{
		if(!$this->plugin instanceof ProperDuels){
			throw new \UnexpectedValueException('This command wasn\'t created by ' . ProperDuels::class);
		}
		$kitManager = $this->plugin->getKitManager();
		if(!$kitManager->has($args['kit'])){
			$sender->sendMessage(TextFormat::RED."§9§l» §r§cNo kit was found by the name '$args[kit]'");
			return;
		}

		$kitManager->remove($args['kit']);
		$sender->sendMessage("§9§l» §r§aRemoved kit '$args[kit]' successfully");
	}

	public function prepare(): void{
		$this->setPermission('properduels.command.kit.delete');
		$this->registerArgument(0, new RawStringArgument('kit'));
	}
}
