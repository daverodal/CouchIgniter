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
            background: url('<?=base_url("js/$backgroundImage");?>');
            background-repeat: no-repeat;
            background-size: 100%;
        }
        .spinner-div{
            position: absolute;
        }
        .fa-spinner{
            position:absolute;
            font-size:200px;
            margin-left:150px;
            color:#666;
        }

        .spinner-div{
            opacity:0;  /* make things invisible upon start */
            -webkit-animation:fadeIn ease-in 1;  /* call our keyframe named fadeIn, use animattion ease-in and repeat it only 1 time */
            -moz-animation:fadeIn ease-in 1;
            animation:fadeIn ease-in 1;

            -webkit-animation-fill-mode:forwards;  /* this makes sure that after animation is done we remain at the last keyframe value (opacity: 1)*/
            -moz-animation-fill-mode:forwards;
            animation-fill-mode:forwards;

            -webkit-animation-duration:3s;
            -moz-animation-duration:3s;
            animation-duration:3s;
        }
        h4{
            margin:15px 0;
        }

        .coolBox{
            margin: 25px 0;
        }

        @-webkit-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @-moz-keyframes fadeIn { from { opacity:0; } to { opacity:1; } }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        /*ol {*/
            /*counter-reset: item;*/
            /*padding-left: 10px;*/
        /*}*/
        /*li {*/
            /*list-style-type: none;*/
        /*}*/

        /*li::before{*/
            /*content: "[" counters(item,".") "] ";*/
            /*counter-increment: item;*/
            /*font-weight: bold;*/
        /*}*/
    </style>
</head>
<body ng-controller="ScenarioController">
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
        <div ng-if="options.length > 0" class="coolBox">
        <h4>Game Options</h4>
            <div ng-repeat="option in options">
                <input type="checkbox" ng-click="updateOptions()" ng-model="option.value">
                 {{option.name}}
            </div>
        </div>
        <div class="coolBox">
            <p class='softVoice'> Click on a scenario below</p>
            <div ng-repeat="(sName, scenario) in scenarios" ng-click="clickityclick(scenario)" class="clearWrapper">
                <span  class="scenarioWrapper {{scenario.selected}}">{{scenario.description}}</span>
                <a class='scenarioWrapper play' ng-href='<?= site_url("wargame/createWargame/") ?>/{{game}}/{{scenario.sName}}?{{setOptions}}'>Play &raquo;</a>
                <div class="clear"></div>
            </div>
            <p class="scenarioDescription long-description selected">&ldquo;{{scenario.longDescription}}&rdquo;</p>
        <div class="clear"></div>
        </div>

        <h3>Historical Context</h3>

        <div class="coolBox wordpress-wrapper">
        <a target='_blank' ng-href="{{histEditLink}}">Edit</a>
        <?php //echo $theGame->value->histEditLink;
        echo $theGame->value->longDesc; ?>
        </div>
        </li>
        <li class='rightGrid'>
        <div class='spinner-div' ng-if="imageUpdating" ><i class="fa fa-spinner fa-spin"></i></div>
        <img id="mapImage" imageonload ng-src="{{scenario.mapUrl}}">
        <?php
        echo "<h3>Player Notes</h3><div class='coolBox wordpress-wrapper'>";
        echo $theGame->value->playerEditLink;
        echo $theGame->value->playerNotes;
        echo "</div></li>";
        echo "</ul>";
    } else {
        if ($games && $games[0]->game) {
            echo '<ul id = "theGamesGrid" >';
            $href = site_url("wargame/unattachedGame/");
            echo "<a class='breadcrumb' href='$href'>back</a><br>";
            ?>
            {games}
            <li class="gridRow">
                <a class="leftGrid" href="{siteUrl}/{dir}/{urlGenre}/{game}">{genre}</a>
                <a class="rightGrid" href="{siteUrl}/{dir}/{urlGenre}/{game}">{game}</a>
                <a href="{siteUrl}/{dir}/{urlGenre}/{game}"><img src="{mapUrl}"></a>
                <div class="clear"></div>
            </li>
            {/games}
        <?php
        } else {
            echo '<ul id = "theGrid" >';
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

    <div ng-controller="RecursiveController">
        <recursive-rules data="[]"></recursive-rules>
    </div>
    <br><br><br>
    Or
    <a href="<?= site_url("users/logout"); ?>">Logout</a>
    <a href="<?= site_url("wargame/leaveGame"); ?>">back to lobby</a>
</div>

<footer class="unattached attribution">
    ss<?=$backgroundAttr;?>ff
</footer>
</body>
<script>
</script>
<script type="text/javascript">
    var jString = '<?php echo addslashes(json_encode($theGame->value->scenarios));?>';
    var scenarioApp = angular.module('scenarioApp', []);
    scenarioApp.controller('ScenarioController', ['$scope', function ($scope) {
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
        $scope.imageUpdating = true;
        $scope.setOptions = "";

        var oString = '<?php echo addslashes(json_encode($theGame->value->options));?>';
        $scope.options = $.parseJSON(oString);

        $scope.clickityclick = function (a) {
            if($scope.scenario.mapUrl !== a.mapUrl){
                $scope.imageUpdating = true;
            }
            if ($scope.lastScenario) {
                $scope.lastScenario.selected = '';
            }
            a.selected = 'selected';
            $scope.scenario = a;
            $scope.lastScenario = a;
        }
        $scope.updateOptions = function(){
            $scope.setOptions = "";
            for(var i in $scope.options){
                if($scope.options[i].value){
                    $scope.setOptions += $scope.options[i].keyName+"="+$scope.options[i].value+"&";
                }
            }
        }
        $scope.updateOptions();
    }]).
    directive('imageonload', function() {
        return {
            restrict: 'A',
            link: function(scope, element, attrs) {
                element.bind('load', function() {
                    scope.imageUpdating = false;
                    scope.$apply();
                });
            }
        };
    });

    scenarioApp.controller('RecursiveController', ['$scope', function($scope) {

        $scope.typeOf = function(val){
            debugger;
            return (typeof val) === 'object';
        };
        $scope.data = [
            "love",
            "peace",
            "war",
            ['gold',
                'fire',
                'water',['gax','electric']],
            'weapons'
        ];
    }])
        .directive('recursiveRules',function(){
            return {
                restrict:'E',
                scope:{
                    data: '='
                },
                template:'<ol><li recursive-rule ng-repeat="datum in data" data="datum"></li></ol>'
            }
        })
        .directive('recursiveRule', function ($compile) {
        return {
            restrict: "A",
            replace: true,
            scope: {
                data: '='
            },
            template: '',
            link: function (scope, element, attrs) {
                debugger;
                if (angular.isArray(scope.data)) {
                    element.append("Dude! <recursive-rules data='data'></recursive-rules>");
                }else{
                    element.append('{{data}}');
                }
                $compile(element.contents())(scope);
            }
        }
    });

</script>
</html>
