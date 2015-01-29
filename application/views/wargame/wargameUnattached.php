<!doctype html>
<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */
?>
<html ng-app="scenarioApp">

<head>
    <script src="<?= base_url("js/jquery-1.11.1.min.js"); ?>"></script>
    <script src="<?= base_url("js/jquery-ui-1.11.0.min.js"); ?>"></script>
    <script src="<?= base_url("js/sync.js"); ?>"></script>
    <script src="<?= base_url("js/angular.js"); ?>"></script>
    <meta charset="UTF-8">
    <link href="<?= base_url("js/unattached.css"); ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= base_url("js/font-awesome-4.2.0/css/font-awesome.min.css"); ?>">

    <style type="text/css">
        body {
            background: url('<?=base_url("js/M110_howitzer.jpg");?>');
            background-repeat: no-repeat;
            background-size: 100%;

        }

        .wordpress-wrapper a,
        .wordpress-wrapper b {
            margin: 0 .2em;
        }

        .selected {
            background-color: yellow;
        }
    </style>
</head>
<body ng-controller="scenarioController">
<div id="container" <?= $theGame ? "class='wideGame coolBox'" : 'class="coolBox"'; ?>>

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
        ?>
        <h2>{{name}}</h2>
        <p>{{description}}</p>
        <p class='softVoice'> Click on a scenario below</p>
        <div ng-repeat="(sName, scenario) in scenarios" ng-click="clickityclick(scenario)" class="clearWrapper">
            <span  class="scenarioWrapper {{scenario.selected}}">{{scenario.description}}</span>
            <a class='scenarioWrapper play' ng-href='<?= site_url("wargame/createWargame/") ?>/{{game}}/{{scenario.sName}}'>Play &raquo;</a>
            <div class="clear"></div>
        </div>
        <p class="">{{scenario.longDescription}}</p>
        <h3>Historical Context</h3>

        <div class="coolBox wordpress-wrapper">
        <a target='_blank' ng-href="{{histEditLink}}">Edit</a>
        <?php //echo $theGame->value->histEditLink;
        echo $theGame->value->longDesc; ?>
        </div>
        </li>
        <li class='rightGrid'>
        <img id="mapImage" ng-src="{{scenario.mapUrl}}">
        <?php
        echo "<h3>Player Notes</h3><div class='coolBox wordpress-wrapper'>";
        echo $theGame->value->playerEditLink;
        echo $theGame->value->playerNotes;
        echo "</div></li>";
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
        } else {
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
    By Greg Goebel [Public domain], <a target='blank'
                                       href="http://commons.wikimedia.org/wiki/File%3AM110_8_inch_self_propelled_howitzer_tank_military.jpg">via
        Wikimedia Commons</a></footer>
</body>
<script>
</script>
<script type="text/javascript">
    var jString = '<?php echo addslashes(json_encode($theGame->value->scenarios));?>';
    var scenarioApp = angular.module('scenarioApp', []);
    scenarioApp.controller('scenarioController', ['$scope', function ($scope) {
        $scope.predicate = '';
        $scope.scenarios = $.parseJSON(jString);
        for (var i in $scope.scenarios) {
            $scope.scenario = $scope.scenarios[i];
            break;
        }
        $scope.game = '<?=$theGame->game;?>';
        $scope.name = '<?=addslashes($theGame->value->name);?>';
        $scope.description = '<?=$theGame->value->description;?>';
        $scope.histEditLink = '<?=$theGame->value->histEditLink;?>';
        $scope.lastScenario = $scope.scenario;
        $scope.scenario.selected = 'selected';

        $scope.clickityclick = function (a) {
            if ($scope.lastScenario) {
                $scope.lastScenario.selected = '';
            }
            a.selected = 'selected';
            $scope.scenario = a;
            $scope.lastScenario = a;
        }
    }]);
</script>
</html>
