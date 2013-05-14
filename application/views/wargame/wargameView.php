<!doctype html>
<html>
    <?=
$this->load->view("wargame/wargameHead.php",compact($playerData));
    Battle::getView($gameName,$mapUrl,$player,$arg);


