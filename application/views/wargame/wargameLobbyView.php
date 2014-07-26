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
<script src="<?= base_url("js/jquery-1.11.1.min.js"); ?>"></script>
<script src="<?= base_url("js/jquery-ui-1.11.0.min.js"); ?>"></script>
<script src="<?= base_url("js/sync.js"); ?>"></script>
<link href="<?= base_url("js/lobby.css"); ?>" rel="stylesheet" type="text/css">
<style type="text/css">

    body{
        background: url("<?=base_url("js/armoredKnight.jpg")?>") #fff;
        background-repeat: no-repeat;
        background-position: center top;
    }

</style>
<script type="text/javascript">
    var fetchUrl = "<?=site_url("wargame/fetchLobby/");?>"
    $(document).ready(function(){
        var sync = new Sync(fetchUrl);
        sync.register("lobbies", function(lobbies){
            $("#myGames .lobbyRow").remove();
            $("#myGames .bold").hide();
            if(lobbies.length > 0){
                $("#myGames .bold").show();
            }else{
                $("#myGames").append("<li style='text-align:center' class='lobbyRow'>you have created no games</li>");

            }
            var changeLobbyHref;
            for(var i in lobbies){
                changeLobbyHref = "<?=site_url('wargame/changeWargame');?>/"+ lobbies[i].id;
                var line = "<li id='" + lobbies[i].id + "' class='lobbyRow " + lobbies[i].odd + "'>&nbsp;";
                line += "<a href='<?=site_url('wargame/changeWargame');?>/" + lobbies[i].id + "/'>";
                line += "<span class='colOne'>" + anchorMe(lobbies[i].name, changeLobbyHref) + "</span>";
                line += "<span class='colTwo'>" + anchorMe(lobbies[i].gameName, changeLobbyHref) + "</span>";
                line += "<span class='colThree " + lobbies[i].myTurn + "'>" + lobbies[i].turn + " </span>";
                line += "<span class='colFour'>" + anchorMe(lobbies[i].date, changeLobbyHref) + "</span>";
                var pubLink;
                if(lobbies[i].public == "public"){
                    pubLink = "<a href='<?=site_url('wargame/makePrivate');?>/" + lobbies[i].id + "'>private</a>";
                    ;
                }else{
                    pubLink = "<a href='<?=site_url('wargame/makePublic');?>/" + lobbies[i].id + "'>public</a>";
                }
                line += "<span class='colFive'>"+pubLink + " <a href='<?=site_url('wargame/playAs');?>/" + lobbies[i].id + "'>edit</a> <a href='<?=site_url("wargame/deleteGame");?>/" + lobbies[i].id + "/'>delete</a></span><div class='clear'></div></li>";
                $("#myGames").append(line);
            }
        });
        sync.register("multiLobbies", function(lobbies){
            $("#myMultiGames .lobbyRow").remove();
            $("#myMultiGames .bold").hide();
            if(lobbies.length > 0){
                $("#myMultiGames .bold").show();
            }else{
                $("#myMultiGames").append("<li style='text-align:center' class='lobbyRow'>you have created no games</li>");

            }
            var changeLobbyHref;
            for(var i in lobbies){
                changeLobbyHref = "<?=site_url('wargame/changeWargame');?>/"+ lobbies[i].id;
                var line = "<li id='" + lobbies[i].id + "' class='lobbyRow " + lobbies[i].odd + "'>&nbsp;";
                line += "<a href='<?=site_url('wargame/changeWargame');?>/" + lobbies[i].id + "/'>";
                line += "<span class='colOne'>" + anchorMe(lobbies[i].name, changeLobbyHref) + "</span>";
                line += "<span class='colTwo'>" + anchorMe(lobbies[i].gameName, changeLobbyHref) + "</span>";
                line += "<span class='colThree " + lobbies[i].myTurn + "'>" + lobbies[i].turn + " </span>";
                line += "<span class='colFour'>" + anchorMe(lobbies[i].date, changeLobbyHref) + "</span>";
                line += "<span class='colFive'> " + anchorMe(lobbies[i].players, changeLobbyHref) + "</span></a>";
                var pubLink;
                if(lobbies[i].public == "public"){
                    pubLink = "<a href='<?=site_url('wargame/makePrivate');?>/" + lobbies[i].id + "'>private</a>";
                    ;
                }else{
                    pubLink = "<a href='<?=site_url('wargame/makePublic');?>/" + lobbies[i].id + "'>public</a>";
                }
                line += "<span class='colSix'>"+pubLink + " <a href='<?=site_url('wargame/playAs');?>/" + lobbies[i].id + "'>edit</a> <a href='<?=site_url("wargame/deleteGame");?>/" + lobbies[i].id + "/'>delete</a></span><div class='clear'></div></li>";
                $("#myMultiGames").append(line);
            }
        });
        sync.register("otherGames", function(lobbies){
            $("#myOtherGames .lobbyRow").remove();
            $("#myOterGames .bold").hide();
            if(lobbies.length > 0){
                $("#myOterGames .bold").show();
            }
            for(var i in lobbies){
                var line = "<li id='" + lobbies[i].id + "' class='lobbyRow " + lobbies[i].odd + "'>";
                line += "<a href='<?=site_url('wargame/changeWargame');?>/" + lobbies[i].id + "/'>";
                line += "<span class='colOne'>" + lobbies[i].name + "</span><span class='colTwo'>" + lobbies[i].gameName;
                line += "</span>";
                line += "<span class='colThree " + lobbies[i].myTurn + "'>" + lobbies[i].turn;

                line += " turn </span><span class='colFour'>" + lobbies[i].date + "</span><span class='colFive'> " + lobbies[i].players;
                line += "</span></a>"
                line += "<div class='clear'></div></li>";
                $("#myOtherGames").append(line);
            }
        });
        sync.register("publicGames", function(lobbies){
            $("#publicGames .lobbyRow").remove();
            $("#publicGames .bold").hide();
            if(lobbies.length > 0){
                $("#publicGames .bold").show();
            }
            for(var i in lobbies){
                var line = "<li id='" + lobbies[i].id + "' class='lobbyRow " + lobbies[i].odd + "'>";
                line += "<a href='<?=site_url('wargame/changeWargame');?>/" + lobbies[i].id;
                line += "/'><span class='colOne'>" + lobbies[i].name + "</span><span class='colTwo'>" + lobbies[i].gameName;
                line += "</span></a><a href='<?=site_url('wargame/playAs');?>/" + lobbies[i].id;
                line += "/'>";
                line += "</span></a><a href='<?=site_url('wargame/changeWargame');?>/" + lobbies[i].id;
                line += "/'><span class='colThree " + lobbies[i].myTurn + "'>It's " + lobbies[i].turn;

                line += " turn </span><span class='colFour'>" + lobbies[i].date + "</span></a><span class='colFive'> " + lobbies[i].players;
                line += "</span><div class='clear'></div></li>";
                $("#publicGames").append(line);
            }
        });
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

        sync.fetch(0);
    });

    function anchorMe(text, href){
        return text;
        return '<a href="'+ href + '">'+ text+ '</a>';
    }
</script>

</head>
<body>
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
    </ul>
    <footer class="attribution">
        By Paul Mercuri [Public domain or Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AArmored_Knight_Mounted_on_Cloaked_Horse.JPG">via Wikimedia Commons</a>
    </footer>
    <a class="logout" href="<?= site_url("users/logout"); ?>">Logout</a>
</div>
</body>
</html>