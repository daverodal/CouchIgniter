<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!doctype html>
<html ng-app="playMulti">
<head>
    <meta charset="UTF-8">
    <script src="<?= base_url("js/angular.js"); ?>"></script>
    <style>
        footer {
            position: fixed;
            bottom: 0px;
        }
    </style>
    <link href="<?= base_url("js/enterMulti.css"); ?>" rel="stylesheet" type="text/css">

</head>
<body ng-controller="SimpleRadio">
<?php Battle::playMulti($game, $wargame, $arg);
global $force_name;
$playerOne = $force_name[1];
if ($players[1]) {
    $playerOne = $players[1];
}
$playerTwo = $force_name[2];
if ($players[2]) {
    $playerTwo = $players[2];
}
$playerThree = $force_name[3];
if ($players[3]) {
    $playerThree = $players[3];
}
?>
<div class="wrapper">
    <div ng-show="macsPlayers > 2">
        <h1>Choose Number of Players</h1>

        <form>
            <input type="radio" ng-model="numPlayers" ng-value="2"> 2
            <input type="radio" ng-model="numPlayers" ng-value="3"> 3 <br/>
        </form>

        <h1>{{numPlayers}} Player Game</h1>

    </div>

    <form name="myForm">
        Game is {{publicGame}} <input type="checkbox" ng-model="publicGame"
                                      ng-true-value="'public'" ng-false-value="'private'"> <br/>
    </form>
    <div ng-if="numPlayers == 2">
        <div class="left"><span ng-class="player.color" class="big">You are {{player.myName}}</span><br>

            <form>
                <input type="radio" ng-model="player" ng-value="playerOne">  <?= $playerOne; ?>
                <input type="radio" ng-model="player" ng-value="playerTwo"> <?= $playerTwo; ?> <br/>
            </form>
        </div>
        <div ng-class="player.otherColor" class="right big">{{player.theirName}}</div>
        <div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
        <div class="clear"></div>
        <div class="right">
            <ul ng-if="player.myName == playerOne.myName">
                <li ng-repeat="user in users | orderBy: 'key'"><a ng-class="player.otherColor" href="{path}/{wargame}/{me}/{{user.key}}/{{publicGame}}">{{user.key}}</a></li>
            </ul>
            <ul ng-if="player.myName == playerTwo.myName">
                <li ng-repeat="user in users | orderBy: 'key'"><a ng-class="player.otherColor" href="{path}/{wargame}/{{user.key}}/{me}/{{publicGame}}">{{user.key}}</a></li>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <div ng-if="numPlayers == 3" class="threeVsGrid">
        <div class="leftCol"><span ng-class="player.color" class="big">You are {{player.myName}}</span><br>

            <form>
                <input type="radio" ng-model="player" ng-value="playerOne">  <?= $playerOne; ?>
                <input type="radio" ng-model="player" ng-value="playerTwo"> <?= $playerTwo; ?>
                <input type="radio" ng-model="player" ng-value="playerThree"> <?= $playerThree; ?> <br/>
            </form>
        </div>
        <div class="leftVs">&laquo;&laquo;vs&raquo;&raquo;</div>
        <div class="centerCol">
            <div ng-class="player.otherColor" class="big">{{player.theirName}}</div>
            <ul class="choose-player" ng-if="player.myName">
                <form>
                    <li ng-repeat="user in users | orderBy: 'key'">  <input type="radio" ng-model="thirdPlayer.name" ng-value="user.key">  {{user.key}}</li>
                </form>
            </ul>

        </div>
        <div class="rightVs">&laquo;&laquo;vs&raquo;&raquo;</div>
        <div class="rightCol">
            <div ng-class="player.otherColor" class="big">{{player.theirOtherName}}</div>

            <div ng-show="thirdPlayer.name">
                <ul ng-if="player.myName == playerOne.myName">
                    <li ng-repeat="user in users | orderBy: 'key'"><a ng-class="player.otherColor" ng-href="{wargame}/{me}/{{thirdPlayer.name}}{{}}/{{publicGame}}/{{user.key}}">{{user.key}}</a></li>
                </ul>
                <ul ng-if="player.myName == playerTwo.myName">
                    <li ng-repeat="user in users | orderBy: 'key'"><a ng-class="player.otherColor" ng-href="{wargame}/{{thirdPlayer.name}}/{me}/{{publicGame}}/{{user.key}}">{{user.key}}</a></li>
                </ul>
                <ul ng-if="player.myName == playerThree.myName">
                    <li ng-repeat="user in users | orderBy: 'key'"><a ng-class="player.otherColor" ng-href="{wargame}/{{thirdPlayer.name}}/{{user.key}}/{{publicGame}}/{me}">{{user.key}}</a></li>
                </ul>
            </div>






        </div>
    </div>
    <div>
        <a href="<?= site_url("wargame/play"); ?>">Back to lobby</a>
    </div>
</div>
<script>
    angular.module('playMulti', [])
        .controller('SimpleRadio', ['$scope', function ($scope) {
            $scope.publicGame = '{visibility}';
            $scope.users = [];
            $scope.wargame = '{wargame}';
            $scope.iAm = '{me}';
            $scope.player = {};
            var users = JSON.parse('<?php echo json_encode($users);?>');
            var usersArray = [];
            for(var i in users){
                usersArray.push(users[i]);
            }
            $scope.users = usersArray;
            $scope.numPlayers = 2;
            $scope.thirdPlayer = {};
            $scope.macsPlayers = <?= $maxPlayers?>;
            $scope.playerTwo = {
                "myName": "<?=$playerTwo;?>",
                "theirName": "<?=$playerOne;?>",
                "theirOtherName": "<?=$playerThree;?>",
                "color": "<?=$playerTwo;?>",
                "otherColor": "<?=$playerOne;?>"
            };
            $scope.playerOne = {
                "myName": "<?=$playerOne;?>",
                "theirName": "<?=$playerTwo;?>",
                "theirOtherName": "<?=$playerThree;?>",
                "color": "<?=$playerOne;?>",
                "otherColor": "<?=$playerTwo;?>"
            };
            $scope.playerThree = {
                "myName": "<?=$playerThree;?>",
                "theirName": "<?=$playerOne;?>",
                "theirOtherName": "<?=$playerTwo;?>",
                "color": "<?=$playerThree;?>",
                "otherColor": "<?=$playerTwo;?>"
            };
        }]);
</script>
</body>