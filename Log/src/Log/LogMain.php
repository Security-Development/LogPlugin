<?php

namespace Log;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\player\PlayerLoginEvent;

use Log\command\SaveLogCommand;
use Log\command\LogCommand;
use Log\form\TraitForm;
use Log\form\RecordForm;

date_default_timezone_set('Asia/Seoul');

class LogMain extends PluginBase implements Listener
{

  use TraitForm;

  private Array $data = [];

  private function getDate() : String
  {
    return date("Y-m-d");
  }

  private function CreateConfig(String $name) : Void
  {
    if(!$this->isConfigFile($name))
      fopen($this->getPath($name), "w");

  }

  private function getPath(String $name) : String
  {
    return $this->getDataFolder().$name."/".$this->getDate().".yml";
  }

  private function isConfigFolder(String $name) : Bool
  {
    return (file_exists($this->getDataFolder() . $name)) ? true : false;

  }


  private function isConfigFile(String $name) : Bool
  {
    return (file_exists($this->getPath($name))) ? true : false;

  }

  public function getUserData(Player $player, String $date) : ?Array
  {
    $name = strtolower($player->getName());

    if(!$this->isConfigFile($name))
    {
      return null;

    } else {
      return $this->getDataConfig($name, $date)->getAll();

    }

  }

  public function getDataConfig(String $name, String $time) : Config
  {
    return new Config($this->getDataFolder(). $name."/".$time.".yml", Config::YAML);

  }

  public function addConfig(String $type, Array $value) : Void
  {
    $config = $this->getDataConfig($type, $this->getDate());
    $this->data[$type][] = $value;

  }

  public function sendSave() : Void
  {
    foreach($this->data as $key => $value)
    {
      $config = $this->getDataConfig($key, $this->getDate());
      $data = $config->getAll();

      foreach ($this->data[$key] as $k => $v)
      {
        $data[] = $this->data[$key][$k];

      }
      $config->setAll($data);

      $config->save();

    }

  }

  public function onDisable() : Void
  {
    $this->sendSave();

  }

  public function onEnable() : Void
  {

    @mkdir($this->getDataFolder());

    if(!file_exists($this->getDataFolder()."config.yml"))
    {
			$this->saveResource("config.yml");

		}

    $this->getServer()->getPluginManager()->registerEvents($this, $this);

    $this->getServer()->getCommandMap()->register("Log", new LogCommand());
    $this->getServer()->getCommandMap()->register("Log", new SaveLogCommand($this));

  }

  public function sendArray(String $attribute, Block $block) : Array
  {
    return [
      'attribute' => $attribute,
      'time' => date("H시i분s초"),
      'x-y-z' => "[{$block->getX()}]-[{$block->getY()}]-[{$block->getZ()}]",
      'world' => $block->getLevel()->getFolderName()
    ];

  }

  public function scanData(String $xyz) : String
  {
    $str = "";
    foreach ($this->getUserName() as $key => $value)
    {
      foreach ($this->getDateName($value) as $k => $v)
      {
        $config = new Config($this->getDataFolder().$value."/".$v, Config::YAML);
        $arr = $config->getAll();

        foreach($arr as $ka => $va)
        {
          if($xyz == $va["x-y-z"])
          {
            $message = match($va["attribute"])
            {
              "BlockBreak" => "부순",
              "BlockPlace" => "설치한"
            };

            $str .= pathinfo($v, PATHINFO_FILENAME)." ".$va["time"]." ".$value."님이 ".$message."기록이 발견 되었습니다.\n";
            continue;

          }

        }

      }

    }
    return $str;

  }

  public function LoginEvent(PlayerLoginEvent $event) : Void
  {
    $player = $event->getPlayer();
    $name = strtolower($player->getName());

    if(!$this->isConfigFolder($name))
      @mkdir($this->getDataFolder().$name);

    $this->CreateConfig($name);

  }

  public function BreakEvent(BlockBreakEvent $event) : Void
  {
    $player = $event->getPlayer();
    $block = $event->getBlock();
    $item = $player->getInventory()->getItemInHand();
    $config = new Config($this->getDataFolder()."config.yml", Config::YAML);

    if($player->isOp())
    {
      if($item->getId().":".$item->getDamage() == $config->get("item"))
      {
        $data = $this->scanData("[".$block->getX()."]-[".$block->getY()."]-[".$block->getZ()."]");
        $message = !empty($data) ? $data : "스캔 결과, 해당 좌표는 아무 기록로그를 발견할 수 없었습니다.";

        $player->sendForm(new RecordForm($message));
        $event->setCancelled();
        return;

      }

    }

    if($this->isConfigFolder(($name = strtolower($player->getName()))))
      $this->addConfig($name, $this->sendArray("BlockBreak", $block));


  }

  public function PlaceEvent(BlockPlaceEvent $event) : Void
  {
    if($this->isConfigFolder(($name = strtolower($event->getPlayer()->getName()))))
      $this->addConfig($name, $this->sendArray("BlockPlace", $event->getBlock()));
  }

}
?>
