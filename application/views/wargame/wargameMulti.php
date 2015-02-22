<!doctype html>
<html ng-app="playMulti" >
<head>
    <meta charset="UTF-8">
    <script src="<?= base_url("js/angular.js"); ?>"></script>
    <style>
        footer{
            position:fixed;
            bottom:0px;
        }
    </style>
</head>
<body ng-controller="SimpleRadio" >
<?php       Battle::playMulti($game,$wargame,$arg);global $force_name;$playerOne = $force_name[1];
$playerTwo = $force_name[2];?>
<div class="wrapper">
    <form name="myForm" >
        Game is {{publicGame}} <input type="checkbox" ng-model="publicGame"
                                  ng-true-value="'public'" ng-false-value="'private'"> <br/>
    </form>
    <div class="left"><span ng-class="player.color" class="big">You are {{player.myName}}</span><br>
        <form>
        <input type="radio" ng-model="player" ng-value="playerOne">  <?=$playerOne;?>
        <input type="radio" ng-model="player" ng-value="playerTwo"> <?=$playerTwo;?> <br/>
            </form>
    </div>
    <div ng-class="player.otherColor" class="right big">{{player.theirName}}</div>
    <div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
    <div class="clear"></div>
    <div class="right">
        <ul ng-if="player.myName">
            {users}
            <li><a ng-class="player.otherColor" href="{path}/{wargame}/{me}/{key}/{{publicGame}}">{key}</a></li>
            {/users}
        </ul>
    </div>
    <div class="clear"></div>
    <div>
        <a href="<?=site_url("wargame/play");?>">Back to lobby</a>
    </div>
</div>
<script>
    angular.module('playMulti', [])
        .controller('SimpleRadio', ['$scope', function($scope){
            $scope.publicGame = '{visibility}';
            $scope.player = {};
            $scope.playerTwo = {
                "myName": "<?=$playerTwo;?>",
                "theirName":"<?=$playerOne;?>",
                "color": "<?=$playerTwo;?>",
                "otherColor":"<?=$playerOne;?>"
            };
            $scope.playerOne = {
                "myName": "<?=$playerOne;?>",
                "theirName":"<?=$playerTwo;?>",
                "color": "<?=$playerOne;?>",
                "otherColor":"<?=$playerTwo;?>"
            };

        }]);
</script>
</body>