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
    <link href="<?= base_url("js/unattached.css"); ?>" rel="stylesheet" type="text/css">

    <style type="text/css">
        body {
            background: url('<?=base_url("js/M110_howitzer.jpg");?>');
            background-repeat: no-repeat;
            background-size: 100%;

        }

        div {
            font-size: 22px;
            background: rgba(255, 255, 255, .9);
            border: 1px solid #333;
            border-radius: 15px;
            margin: 40px;
            padding: 20px;
            box-shadow: 10px 10px 10px rgba(20, 20, 20, .7);
        }

        input {
            margin-left: 15px;
        }

        a {
            color: #333;
        }

        ul {
            list-style-type: none;
        }

        li {
            margin: 5px 0;
        }

        .gameDesc {
            width: auto;
        }

        li.game {
            border-bottom: 1px solid #333;
        }

        .breadcrumb {
            text-decoration: none;
        }
    </style>
</head>
<body>

<div id="container" <?=$theGame?"class='wideGame'":'';?>>
    Attach to game:
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

            $href = site_url("wargame/unitInit/" . rawurlencode($theGame->game) . "/" . $sKey);
            echo "<a href='$href'>" . $scenario->description . "</a><br><br>";

        }?>
        </li>
        <li class='rightGrid'>
        <h3>Historical Context</h3>
        <?php echo $theGame->value->longDesc;
        echo "<h3>Player Notes</h3>";
        echo $theGame->value->playerNotes;
        echo "</li>";
        echo "</ul>";
    } else {
        echo '<ul id = "theGrid" >';
        if ($games && $games[0]->game) {
            $href = site_url("wargame/unattachedGame/");
            echo "<a href='$href'>back</a><br>";
        }
        foreach ($games as $game) {
            $href = site_url("wargame/unattachedGame/" . rawurlencode($game->dir) . "/" . rawurlencode($game->genre) . "/" . rawurlencode($game->game));

            if (!$game->game) {
                $nGames = "$game->value  available";
            } else {
                $nGames = "<a class='leftGrid' href='$href'>" . $game->game . "</a>";
            }
            ?>
            <li class="gridRow">
                <?php echo "<a class='leftGrid' href='$href'>" . $game->genre . " </a> <span class='rightGrid'>$nGames</span>"; ?>
            </li>
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
</body>
</html>
