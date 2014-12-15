<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:45 PM
 */
?>
<!doctype html>
<html ng-app="lobbyApp">
<head>
<script src="<?= base_url("js/jquery-1.11.1.min.js"); ?>"></script>
<script src="<?= base_url("js/jquery-ui-1.11.0.min.js"); ?>"></script>
<script src="<?= base_url("js/sync.js"); ?>"></script>
<script src="<?= base_url("js/angular.js"); ?>"></script>
<link href="<?= base_url("js/lobby.css"); ?>" rel="stylesheet" type="text/css">
<style type="text/css">

    body{
        background: url("<?=base_url("js/armoredKnight.jpg")?>") #fff;
        background-repeat: no-repeat;
        background-position: center top;
    }

</style>
<script type="text/javascript">
    var DR = {};

    var fetchUrl = "<?=site_url("wargame/fetchLobby/");?>"
</script>

</head>
<body ng-controller="LobbyController">
<div id="content">
    <a class="logout logoutUpper" href="<?= site_url("users/logout"); ?>">Logout</a>

    <?php if ($myName == "Markarian") { ?>
        <h1><a href="<?= site_url("admin"); ?>">Admin </a></h1>
    <?php } ?>
    <h2>Welcome {myName}</h2>

    <h3>You can <a id="create" href="<?= site_url("wargame/unattachedGame"); ?>">Browse or Start a new Wargame</a></h3>
    Or play an existing game:<br>

    <h1>Multi Player Games</h1>
    <ul id="multiPlayerGames">
        <li>
            <h2>Multi games you created:</h2>
            <ul id="myMultiGames">
                <li class="bold lobbyHeader">
                    <span class="colOne">Name</span>
                    <span class="colTwo">Game</span>
                    <span class="colThree">Turn</span>
                    <span class="colFour">Date</span>
                    <span class="colFive">Players Involved</span>
                    <span class="colSix">Actions</span>
                </li>
                <li class="bold lobbySpacer">&nbsp;</li>
                <li ng-class-odd="'odd'" ng-repeat="myMultiGame in myMultiGames" class="lobbyRow">
                <a ng-href="<?=site_url('wargame/changeWargame');?>/{{myMultiGame.id}}/">
                    <span class="colOne">{{myMultiGame.name}}</span>
                    <span class="colTwo">{{myMultiGame.gameName}}</span>
                    <span class="colThree" ng-class="myMultiGame.myTurn">{{myMultiGame.turn}}</span>
                    <span class="colFour">{{myMultiGame.date}}</span>
                    <span class='colFive'>{{myMultiGame.players}}</span>
                </a>

                    <span class="colSix"><a ng-click='publicGame(myMultiGame)' href='' ng-heref="{{myMultiGame.pubLink}}">make {{myMultiGame.pubLinkLabel}}</a> <a ng-href='<?=site_url('wargame/playAs');?>/{{myMultiGame.id}}'>edit</a> <a href='' ng-click="deleteGame(myMultiGame.id)">delete</a>
</span>
                    <div class='clear'></div>
                </li>

            </ul>
        </li>
        <li>
            <h2>Games you were invited to:</h2>
            <ul id="myOtherGames">
                <li class="lobbyHeader bold">
                    <span class="colOne">Name</span>
                    <span class="colTwo">Game</span>
                    <span class="colThree">Turn</span>
                    <span class="colFour">Date</span>
                    <span class="colFive">Players Involved</span>
                </li>
                <li class="lobbySpacer">&nbsp;</li>
                <li ng-class-odd="'odd'" ng-repeat="myOtherGame in myOtherGames" class="lobbyRow">
                    <a ng-href="<?=site_url('wargame/changeWargame');?>/{{myOtherGame.id}}/">
                        <span class="colOne">{{myOtherGame.name}}</span>
                        <span class="colTwo">{{myOtherGame.gameName}}</span>
                        <span class="colThree" ng-class="myOtherGame.myTurn">It's {{myOtherGame.turn}} Turn</span>
                        <span class="colFour">{{myOtherGame.date}}</span>
                        <span class='colFive'>{{myOtherGame.players}}</span>
                    </a>
                    <div class='clear'></div>
                </li>
            </ul>
        </li>
    </ul>
    <h2>HOTSEAT games you created:</h2>
    <ul id="myGames">
        <li class="bold lobbyHeader">
            <span class="colOne">Name</span>
            <span class="colTwo">Game</span>
            <span class="colThree">Turn</span>
            <span class="colFour">Date</span>
            <span class="colFive">Actions</span>
        </li>
        <li class="bold lobbySpacer">&nbsp;</li>
        <li ng-class-odd="'odd'" ng-repeat="myHotGame in myHotGames" class="lobbyRow">


            <a ng-href="<?=site_url('wargame/changeWargame');?>/{{myHotGame.id}}/">
            <span class="colOne">{{myHotGame.name}}</span>
            <span class="colTwo">{{myHotGame.gameName}}</span>
            <span class="colThree">{{myHotGame.turn}}</span>
            <span class="colFour">{{myHotGame.date}}</span>
                <span class='colFive'></span>
            </a>
            <a ng-click='publicGame(myHotGame)' href='' ng-heref="{{myHotGame.pubLink}}">make {{myHotGame.pubLinkLabel}}</a> <a ng-href='<?=site_url('wargame/playAs');?>/{{myHotGame.id}}'>edit</a> <a href='' ng-click="deleteGame(myHotGame.id)">delete</a>

        <div class='clear'></div>
        </li>
    </ul>
    <h2>Public Games: (you can observe but not play)</h2>
    <ul id="publicGames">
        <li class="lobbyHeader bold">
            <span class="colOne">Name</span>
            <span class="colTwo">Game</span>
            <span class="colThree">Turn</span>
            <span class="colFour">Date</span>
            <span class="colFive">Players Involved</span>
        </li>
        <li class="lobbySpacer">&nbsp;</li>

        <li ng-class-odd="'odd'" ng-repeat="myPublicGame in myPublicGames" class="lobbyRow">
            <a ng-href="<?=site_url('wargame/changeWargame');?>/{{myPublicGame.id}}/">
                <span class="colOne">{{myPublicGame.name}}</span>
                <span class="colTwo">{{myPublicGame.gameName}}</span>
                <span class="colThree" ng-class="myPublicGame.myTurn">It's {{myPublicGame.turn}} Turn</span>
                <span class="colFour">{{myPublicGame.date}}</span>
                <span class='colFive'>{{myPublicGame.players}}</span>
            </a>
            <div class='clear'></div>
        </li>


    </ul>
    <footer class="attribution">
        By Paul Mercuri [Public domain or Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AArmored_Knight_Mounted_on_Cloaked_Horse.JPG">via Wikimedia Commons</a>`
    </footer>
    <a class="logout" href="<?= site_url("users/logout"); ?>">Logout</a>
</div>
</body>
<script type="text/javascript">

    var lobbyApp = angular.module('lobbyApp', []);
    lobbyApp.controller('LobbyController', ['$scope', '$http', 'sync', function($scope, $http, sync){

        sync.register('lobbies', function(lobbies){
            var myHotLobbies = [];
            for(var i in lobbies) {
                var myHotLobby = {};
                myHotLobby = lobbies[i];
                myHotLobby.pubLinkLabel = lobbies[i].public === 'public' ? 'private' : 'public';
                myHotLobbies.push(myHotLobby);
            }
            $scope.myHotGames = myHotLobbies;
            $scope.$apply();
        });

        sync.register('multiLobbies', function(multiGames){
            var myMultiGames = [];
            for(var i in multiGames) {
                var myMultiGame = {};
                myMultiGame = multiGames[i];
                myMultiGame.pubLinkLabel = multiGames[i].public === 'public' ? 'private' : 'public';
                myMultiGames.push(myMultiGame);
            }
            $scope.myMultiGames = myMultiGames;
            $scope.$apply();
        });


        sync.register('otherGames', function(otherGames){
            var myOtherGames = [];
            for(var i in otherGames) {
                var myOtherGame = {};
                myOtherGame = otherGames[i];
                myOtherGame.pubLinkLabel = otherGames[i].public === 'public' ? 'private' : 'public';
                myOtherGames.push(myOtherGame);
            }
            $scope.myOtherGames = myOtherGames;
            $scope.$apply();
        });

        sync.register('publicGames', function(publicGames){
            var myPublicGames = [];
            for(var i in publicGames) {
                var myPublicGame = {};
                myPublicGame = publicGames[i];
                myPublicGame.pubLinkLabel = publicGames[i].public === 'public' ? 'private' : 'public';
                myPublicGames.push(myPublicGame);
            }
            $scope.myPublicGames = myPublicGames;
            $scope.$apply();
        });

        $scope.myOtherGames = $scope.myPublicGames = $scope.myMultiGames = $scope.myHotGames = [];

        $scope.deleteGame = function(id){
            $http.get('deleteGame/'+id);
        };

        $scope.publized = false;

        $scope.publicGame = function(game){
            /* don't think this flow control is needed. Seems to be on the getting side that fails */
            if($scope.publized){
                return;
            }
            $scope.publized = true;
            if(game.public === "public"){
                $http.get('makePrivate/'+game.id).success(function(){console.log("MakePrivate ");$scope.publized = false;});
            }else{
                $http.get('makePublic/'+game.id).success(function(){console.log("MakePrivate ");$scope.publized = false;});
            }
        };

        sync.register("results", function(results){
            for(var i in results){
                id = results[0].id;
                if(id.match(/^_/)){
                    continue;
                }
                $("#" + id).animate({color: "#000", backgroundColor: "#fff"}, 800, function(){
                    $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function(){
                        $(this).animate({color: "#000", backgroundColor: "#fff"}, 800, function(){
                            $(this).animate({color: "#fff", backgroundColor: "rgb(170,0,0)"}, 800, function(){
                                $(this).css("background-color", "").css("color", "");
                            });
                        });
                    });
                });
            }
        });


        DR.scope = $scope;

        sync.fetch(0);
    }]);

    lobbyApp.factory('sync',function(){
        var sync = new Sync(fetchUrl);
        return sync;
    });

</script>
</html>