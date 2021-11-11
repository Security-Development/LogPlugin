<?php

namespace Log\form;

use pocketmine\utils\Config;

trait TraitForm
{

  public function getUserName() : Array
  {
    $dir = "./plugin_data/Log/";
    $data = [];

    if (is_dir($dir)) {
        if ($open = opendir($dir)) {
            while (($file = readdir($open)) !== false) {
              if($file == "." || $file == ".." || $file == "config.yml")
                continue;
              $data[] = $file;

            }
            closedir($open);
        }

    }

    return $data;

  }

  public function getDateName(String $name) : Array
  {
    $dir = "./plugin_data/Log/".$name."/";
    $data = [];

    if (is_dir($dir)) {
        if ($open = opendir($dir)) {
            while (($file = readdir($open)) !== false) {
              if($file == "." || $file == "..")
                continue;
              $data[] = $file;

            }
            closedir($open);
        }

    }

    return $data;

  }

  public function getUserButtonData() : Array
  {
    $buttons = [];

    foreach($this->getUserName() as $key => $value)
    {
      $buttons[] = ["text" => $value];

    }
    sort($buttons);

    return $buttons;

  }

  public function getDateButtonData(String $name) : Array
  {
    $buttons = [];

    foreach($this->getDateName($name) as $key => $value)
    {
      $buttons[] = ["text" => pathinfo($value, PATHINFO_FILENAME)];

    }
    sort($buttons);

    return $buttons;

  }

  public function getData(String $name, String $date) : String
  {
    $config = new Config("./plugin_data/Log/".$name."/".$date, Config::YAML);
    $arr = $config->getAll();
    $str = "";

    foreach($arr as $key => $value)
    {
      $str .= "[ * ] === [ * ]\n";
      $str .= "속성 : ".$value["attribute"]."\n";
      $str .= "시간 : ".$value["time"]."\n";
      $str .= "좌표[x-y-z] : ".$value["x-y-z"]."\n";
      $str .= "월드 : ".$value["world"]."\n";

    }

    return $str;

  }

}

?>
