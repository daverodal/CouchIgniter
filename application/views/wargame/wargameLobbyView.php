<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:45 PM
 */
?>
<!doctype html>
<html>
Join game:
<ul>
{lobbies}
<li>
<a href="<?=site_url("wargame/changeWargame");?>/{id}/">{id} -> {name}</a>
    </li>
{/lobbies}
    </ul>