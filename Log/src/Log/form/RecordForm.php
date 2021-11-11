<?php

namespace Log\form;

use pocketmine\form\Form;
use pocketmine\Player;

class RecordForm implements Form
{
  use TraitForm;

  public function __construct(private String $text = "", private Array $arr = []) {}

  public function jsonSerialize() : Array
  {
    return [
      'type' => 'form',
      'title' => 'Log UI',
      'content' => $this->text,
      'buttons' => $this->arr
    ];

  }

  public function handleResponse(Player $player,  $data) : Void
  {
    if($data === null)
      return;

    $player->sendForm(new LogForm());

  }

}

?>
