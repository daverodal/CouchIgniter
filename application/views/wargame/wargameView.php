<!doctype html>
<html>
    <?=
$this->load->view("wargame/wargameHead.php",compact($playerData,'arg'));
    Battle::getView($gameName,$mapUrl,$player,$arg);


