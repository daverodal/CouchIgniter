<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:45 PM
 */
?>
<!doctype html>
<html>
<head>
    <script src="<?=base_url("js/jquery-1.9.0.min.js");?>"></script>
    <script type="text/javascript">
    </script>
</head>
<body>
<h3>You can <a href="<?=site_url("wargame/createWargame");?>">Create a new Wargame</a>  Or </h3>
Join game:
<ul>
{lobbies}
<li>
<a href="<?=site_url("wargame/changeWargame");?>/{id}/">{id} -> {name}</a>
    </li>
{/lobbies}
    </ul>
<a href="<?=site_url("wargame/logout");?>">Logout</a>
</body>
</html>