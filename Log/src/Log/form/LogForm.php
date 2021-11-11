<?php

namespace Log\form;

use pocketmine\form\Form;
use pocketmine\Player;

class LogForm implements Form
{
  use TraitForm;

  public function jsonSerialize() : Array
  {
    return [
      'type' => 'form',
      'title' => 'Log UI',
      'content' => "",
      'buttons' => $this->getUserButtonData()
    ];

  }

  public function handleResponse(Player $player,  $data) : Void
  {
    if($data === null)
      return;

    $array = $this->getUserButtonData();
    $name = $array[$data]["text"];

    $player->sendForm(new LogDateForm($name));

  }

}

?>
