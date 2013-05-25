<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */?>
<!doctype html>
<html>
<body>
Attach to game:
{games}
<a href="<?=site_url("wargame/unitInit");?>/{name}/{arg}">{name}:{arg}</a>
{/games}
<br>
<a href="<?=site_url("wargame/logout");?>">Logout</a><br>
<a href="<?=site_url("wargame/leaveGame");?>">back to lobby</a>
</body>
</html>
