<?php

declare(strict_types=1);

namespace xenialdan\MagicWE2\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use xenialdan\MagicWE2\API;
use xenialdan\MagicWE2\Loader;
use xenialdan\MagicWE2\WEException;

class TogglewandCommand extends WECommand{
	public function __construct(Plugin $plugin){
		parent::__construct("/togglewand", $plugin);
		$this->setPermission("we.command.togglewand");
		$this->setDescription("Toggle the wand tool on/off");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		/** @var Player $sender */
		$return = parent::execute($sender, $commandLabel, $args);
		if(!$return) return $return;
		try{
			$sender->sendMessage(($session = API::getSession($sender))->setWandEnabled(!$session->isWandEnabled()));
		} catch (WEException $error){
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . "Looks like you are missing an argument or used the command wrong!");
			$sender->sendMessage(Loader::$prefix . TextFormat::RED . $error->getMessage());
			$return = false;
		} catch (\Error $error){
			$this->getPlugin()->getLogger()->error($error->getMessage());
			$return = false;
		} finally{
			return $return;
		}
	}
}
