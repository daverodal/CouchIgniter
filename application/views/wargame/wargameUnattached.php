<!doctype html>
<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */
?>
<html>

<head>
    <meta charset="UTF-8">
    <link href="<?= base_url("js/unattached.css"); ?>" rel="stylesheet" type="text/css">

    <style type="text/css">
        body {
            background: url('<?=base_url("js/M110_howitzer.jpg");?>');
            background-repeat: no-repeat;
            background-size: 100%;

        }
        .wordpress-wrapper a,
        .wordpress-wrapper b{
            margin: 0 .2em;
        }
    </style>
</head>
<body>

<div id="container" <?= $theGame ? "class='wideGame'" : ''; ?>>
    <?php

    if ($theGame) {
        echo "<ul id='theGameGrid'>";
        echo "<li class='leftGrid'>";
        $href = site_url("wargame/unattachedGame/");
        echo "<a class='breadcrumb' href='$href'>top</a> ";
        $up = $theGame->dir . "/" . rawurlencode($theGame->genre);
        $href = site_url("wargame/unattachedGame/$up");
        echo "<a class='breadcrumb' href='$href'>back</a><br> ";
        echo "";
        echo "<h2>" . $theGame->value->name . "</h2><p>" . $theGame->value->description . "</p><p class='softVoice'> Click on a scenario below</p>";
        foreach ($theGame->value->scenarios as $sKey => $scenario) {

            $href = site_url("wargame/createWargame/" . rawurlencode($theGame->game) . "/" . $sKey);
            echo "<a class='scenarioWrapper' href='$href'>" . $scenario->description . "</a>";

        }?>
        <h3>Historical Context</h3>

        <div class="wordpress-wrapper">
        <?php echo $theGame->value->histEditLink;
        echo $theGame->value->longDesc;?>
        </div>
        </li>
        <li class='rightGrid'>
        <img style="width:500px" src="{mapUrl}">
        <?php
        echo "<h3>Player Notes</h3>";
        echo $theGame->value->playerEditLink;
        echo $theGame->value->playerNotes;
        echo "</li>";
        echo "</ul>";
    } else {
        echo '<ul id = "theGrid" >';
        if ($games && $games[0]->game) {
            $href = site_url("wargame/unattachedGame/");
            echo "<a class='breadcrumb' href='$href'>back</a><br>";
            ?>
            {games}
            <li class="gridRow">
                <a class="leftGrid" href="{siteUrl}/{dir}/{urlGenre}/{game}">{genre}</a>
                <a class="rightGrid" href="{siteUrl}/{dir}/{urlGenre}/{game}">{game}</a>
            </li>
            {/games}
        <?php
        }else{
            ?>
            {games}
            <li class="gridRow">
            <a class="leftGrid" href="{siteUrl}/{dir}/{urlGenre}">{genre}</a>
            <a class="rightGrid" href="{siteUrl}/{dir}/{urlGenre}">{value} Available</a>
            </li>
            {/games}
            <?php
        }

        echo "</ul>";
    }
    ?>

    <br><br><br>
    Or
    <a href="<?= site_url("users/logout"); ?>">Logout</a>
    <a href="<?= site_url("wargame/leaveGame"); ?>">back to lobby</a>
</div>

<footer class="unattached attribution">
    By Greg Goebel [Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AM110_8_inch_self_propelled_howitzer_tank_military.jpg">via Wikimedia Commons</a></footer>

</body>
</html>
