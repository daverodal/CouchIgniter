<?php
/**
 * User: drodal
 * Date: 11/16/11
 * Time: 6:14 PM
 * To change this template use File | Settings | File Templates.
 */
 
?>
<?= $message?>
<form method="POST">
Name of new game? <input name="wargame">
</form>
<br>
<a href="<?=site_url("wargame/logout");?>">Logout</a><br>
<a href="<?=site_url("wargame/leaveGame");?>">back to lobby</a>
