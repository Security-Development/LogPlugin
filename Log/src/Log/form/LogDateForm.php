<?php

namespace Log\form;

use pocketmine\form\Form;
use pocketmine\Player;

class LogDateForm implements Form
{
  use TraitForm;

  public function __construct(private String $name = "") {}

  public function jsonSerialize() : Array
  {
    return [
      'type' => 'form',
      'title' => 'Log UI',
      'content' => "",
      'buttons' => $this->getDateButtonData($this->name)
    ];

  }

  public function handleResponse(Player $player,  $data) : Void
  {
    if($data === null)
      return;

    $array = $this->getDateButtonData($this->name);
    $name = $array[$data]["text"];

    $player->sendForm(new RecordForm($this->getData(strtolower($player->getName()), $name), [["text" => "Go Back"]]));
  }

}

?>
