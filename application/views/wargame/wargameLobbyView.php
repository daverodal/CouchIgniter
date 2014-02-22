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
    <script src="<?=base_url("js/jquery-ui-1.9.2.custom.min.js");?>"></script>
    <script src="<?=base_url("js/sync.js");?>"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var sync = new Sync("<?=site_url("wargame/fetchLobby/");?>");
            sync.register("lobbies",function(lobbies){
                $("#myGames .lobbyRow").remove();
                $("#myGames .bold").hide();
                if(lobbies.length> 0){
                    $("#myGames .bold").show();
                }else{
                    $("#myGames").append("<li style='text-align:center' class='lobbyRow'>you have created no games</li>");

                }
                for(var i in lobbies){
                /*var line = "<li class='"+odd+"'><a href='<?=site_url('wargame/changeWargame');?>/"+id+"/'><span class='colOne'>"+id+"</span><span class='colOne'>"+name+"</span></a><a href='<?=site_url('wargame/playAs');?>/"+id+"/'><span title='click to change' class='colTwo'>"+gameType+"</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+id+"/'><span class='colThree "+myTurn+"'>It's "+turn+" turn.</span><span class='colFour'>"+date+"</span></a><span class='colFive'> "+players+"</span>";*/
                /*var line = "<li class='"+lobbies[i].odd+"'><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id+"/'><span class='colOne'>"+lobbies[i].id+"</span><span class='colOne'>"+name+"</span></a><a href='<?=site_url('wargame/playAs');?>/"+lobbies[i].id+"/'><span title='click to change' class='colTwo'>"+lobbies[i].gameType+"</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id+"/'><span class='colThree "+lobbies[i].mylobbies[i].turn+"'>It's "+lobbies[i].turn+" lobbies[i].turn.</span><span class='colFour'>"+lobbies[i].date+"</span></a><span class='colFive'> "+lobbies[i].players+"</span>";*/
                var line = "<li id='"+lobbies[i].id+"' class='lobbyRow "+lobbies[i].odd+"'>";
                line += "<a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id;
                line += "/'><span class='colOne'>"+lobbies[i].name+"</span><span class='colOne'>"+lobbies[i].gameName;
                line += "</span>";
                line += "<span class='colTwo'>"+lobbies[i].gameType;
                line += "</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id;
                line += "/'><span class='colThree "+lobbies[i].myTurn+"'>It's "+lobbies[i].turn;

                line += " turn </span><span class='colFour'>"+lobbies[i].date+"</span></a><span class='colFive'> "+lobbies[i].players;
                line += "</span>";
                line += "<a href='<?=site_url('wargame/playAs');?>/"+lobbies[i].id+"'>edit</a> <a href='<?=site_url("wargame/deleteGame");?>/"+lobbies[i].id+"/'>delete</a>";
                $("#myGames").append(line);
                }
            });
            sync.register("otherGames",function(lobbies){
                $("#myOtherGames .lobbyRow").remove();
                $("#myOterGames .bold").hide();
                if(lobbies.length> 0){
                    $("#myOterGames .bold").show();
                }
                for(var i in lobbies){
                    /*var line = "<li class='"+odd+"'><a href='<?=site_url('wargame/changeWargame');?>/"+id+"/'><span class='colOne'>"+id+"</span><span class='colOne'>"+name+"</span></a><a href='<?=site_url('wargame/playAs');?>/"+id+"/'><span title='click to change' class='colTwo'>"+gameType+"</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+id+"/'><span class='colThree "+myTurn+"'>It's "+turn+" turn.</span><span class='colFour'>"+date+"</span></a><span class='colFive'> "+players+"</span>";*/
                    /*var line = "<li class='"+lobbies[i].odd+"'><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id+"/'><span class='colOne'>"+lobbies[i].id+"</span><span class='colOne'>"+name+"</span></a><a href='<?=site_url('wargame/playAs');?>/"+lobbies[i].id+"/'><span title='click to change' class='colTwo'>"+lobbies[i].gameType+"</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id+"/'><span class='colThree "+lobbies[i].mylobbies[i].turn+"'>It's "+lobbies[i].turn+" lobbies[i].turn.</span><span class='colFour'>"+lobbies[i].date+"</span></a><span class='colFive'> "+lobbies[i].players+"</span>";*/
                    var line = "<li id='"+lobbies[i].id+"' class='lobbyRow "+lobbies[i].odd+"'>";
                    line += "<a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id;
                    line += "/'><span class='colOne'>"+lobbies[i].name+"</span><span class='colOne'>"+lobbies[i].gameName;
                    line += "</span></a><a href='<?=site_url('wargame/playAs');?>/"+lobbies[i].id;
                    line += "/'>";
                    line += "</span></a><a href='<?=site_url('wargame/changeWargame');?>/"+lobbies[i].id;
                    line += "/'><span class='colThree "+lobbies[i].myTurn+"'>It's "+lobbies[i].turn;

                    line += " turn </span><span class='colFour'>"+lobbies[i].date+"</span></a><span class='colFive'> "+lobbies[i].players;
                    line += "</span>";
                    $("#myOtherGames").append(line);
                }
            });
            sync.register("results",function(results){
                for(var i in results){
                    id = results[0].id;
                    if(id.match(/^_/)){
                        continue;
                    }
                    $("#"+id).animate({color:"#000",backgroundColor:"#fff"},800,function(){
                        $(this).animate({color:"#fff",backgroundColor:"rgb(112,66,20)"},800,function(){
                            $(this).animate({color:"#000",backgroundColor:"#fff"},800,function(){
                                $(this).animate({color:"#fff",backgroundColor:"rgb(112,66,20)"},800,function(){
                                    $(this).css("background-color","").css("color","");
                                });
                            });
                        });
                    });
                }
            });

        sync.fetch(0);
        });

    </script>

    <style type="text/css">
    h3{
        font-size:16px;
    }
    span{
        display:inline-block;
    }
    .bold{
        font-family:Lucida;
        font-weight:bold;
        font-size:18px;
        color:white;
        text-shadow:0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black,0px 0px 3px black;
    }
    .colOne{
        width:150px;
    }
    .colTwo{
        width:120px;
    }
    .colThree{
        width:180px;
    }
    .colFour{
        width:160px;
    }
    .colFive{
        width:170px;
    }
    h2{
        font-style:italic;
    }
    li{
        list-style: none;
    }
    .myTurn{
        color:white;
        text-shadow:0px 0px 3px green,0px 0px 3px green,0px 0px 3px green,0px 0px 3px green;
    }
    body{
        background: url("<?=base_url("js/armoredKnight.jpg")?>") #fff;
        background-repeat: no-repeat;
        color:#666;
        background-position: center top;
        height:100%;

    }
    .odd{
        background:rgba(66,66,66,.4);
    }
    a{
        color:black;
        font-size:120%;
    }
    #logout{
        color:white;
        text-shadow:0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00,
        0 0 3px #c00;
        text-decoration: none;
        }
    #create{
        color:white;
        text-shadow:0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0,
        0 0 3px #0c0;
        text-decoration: none;
        border-bottom: 3px solid #afa;
        font-family: sans-serif;
        font-style:italic;
    }
    ul{
        padding:5px 10px;
        border-radius:10px;
        background:rgba(33,33,33,.5);
        color:white;
    }
    ul a{
        color:white;
    }
    ul li:hover{
        background:rgba(0,0,0,.5);
    }
    div{
        color:black;
        min-width:900px;
        /*float:left;*/
        /*max-width:1000px;*/
        /*margin-top: 50px;*/
        border-radius:20px;
        border:5px solid gray;
        padding:    15px 30px 15px 15px;
        background: rgba(160,160,160,.15);
        width:80%;
        margin:0 auto;
    }
</style>
</head>
<body>
<div>
    <?php if($myName == "Markarian"){?>
         <h1><a href="<?=site_url("admin");?>">Admin </a></h1>
    <?php } ?>
<h2>Welcome {myName}</h2>
<h3>You can <a id="create" href="<?=site_url("wargame/createWargame");?>">Create a new Wargame</a> </h3>
Or play an existing game:<br>
<br>    Games you created:
    <ul id="myGames">
        <li class="bold"><span class="colOne">Name</span><span class="colOne">Game</span><span class="colTwo">Single/multi</span><span class="colThree">Turn</span><span class="colFour">Date</span><span class="colFive">Players Involved</span></li>

        <li class="bold" >&nbsp;</li>
<!--    {lobbies}-->
<!--<li class="{odd}"><a href="--><?//=site_url("wargame/changeWargame");?><!--/{id}/"><span class="colOne">{id}</span><span class="colOne">{name}</span></a><a href="--><?//=site_url("wargame/playAs");?><!--/{id}/"><span title="click to change" class="colTwo">{gameType}</span></a><a href="--><?//=site_url("wargame/changeWargame");?><!--/{id}/"><span class="colThree {myTurn}">It's {turn} turn.</span><span class="colFour">{date}</span></a><span class="colFive"> {players}</span>-->
<!--    <a href="--><?//=site_url("wargame/deleteGame");?><!--/{id}/">delete</a>-->
<!--    </li>-->
<!--{/lobbies}-->
    </ul>
Games you were invited to:
<ul id="myOtherGames">
    <li class="bold"><span class="colOne">Name</span><span class="colOne">Game</span><span class="colThree">Turn</span><span class="colFour">Date</span><span class="colFive">Players Involved</span></li>
    <li>&nbsp;</li>
    <!--    {otherGames}-->
<!--    <li><a href="--><?//=site_url("wargame/changeWargame");?><!--/{id}/"><span class="colOne">{id}</span><span class="colOne">{name}</span><span class="colTwo">multi</span><span class="colThree {myTurn}">It's {turn} turn.</span><span class="colFour">{date}</span></a><span class="colFive"> {players}</span>-->
<!--    </li>-->
<!--    {/otherGames}-->
</ul>
<a id="logout" href="<?=site_url("users/logout");?>">Logout</a>
    </div>
</body>
</html>