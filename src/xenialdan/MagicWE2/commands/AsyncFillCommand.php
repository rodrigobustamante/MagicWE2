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

class AsyncFillCommand extends WECommand{
	public function __construct(Plugin $plugin){
		parent::__construct("/aset", $plugin);
		$this->setAliases(["/afill"]);
		$this->setPermission("we.command.aset");
		$this->setDescription("Fill an area asynchronously");
	}

	public function getUsage(): string{
		return parent::getUsage(); // TODO: Change the autogenerated stub
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		/** @var Player $sender */
		$return = parent::execute($sender, $commandLabel, $args);
		if(!$return) return $return;
		try{
			$messages = [];
			$error = false;
			$newblocks = API::blockParser(array_shift($args), $messages, $error);
			foreach ($messages as $message){
				$sender->sendMessage($message);
			}
			$return = !$error;
			if ($return){
				API::fillAsync($sender, ($session = API::getSession($sender))->getLatestSelection(), $sender->getLevel(), $newblocks, ...$args);
			} else{
				throw new \TypeError("Could not fill with the selected blocks");
			}
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
