<?php

namespace Log\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use Log\form\LogForm;

class LogCommand extends Command
{

  public function __construct()
  {
    parent::__construct("log", "Log Command");
    $this->setPermission("op");

  }

  public function execute(CommandSender $sender, string $label, array $args) : Bool
  {
    if(!$sender->hasPermission($this->getPermission())) return true;

    if($sender instanceof ConsoleCommandSender)
    {
      $sender->getServer()->getLogger()->info("[§e*§f] §c인게임에서만 실행 가능합니다.");
      return true;

    }

    $sender->sendForm(new LogForm);

      return false;

  }

}
?>
