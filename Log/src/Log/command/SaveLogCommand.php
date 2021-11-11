<?php

namespace Log\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;

use Log\LogMain;

class SaveLogCommand extends Command
{

  public function __construct(private LogMain $plugin)
  {
    parent::__construct("log-save-all", "Log Command");
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

    $this->plugin->sendSave();
    $sender->sendMessage("성공적으로 로그기록 저장에 완료 했습니다.");

      return false;

  }

}
?>
