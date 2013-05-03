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
    <script type="text/javascript">
    </script>
<style type="text/css">
    h3{
        font-size:16px;
    }
    span{
        display:inline-block;
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
        width:150px;
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
        color:darkred;
    }
    body{
        background: url("<?=base_url("js/civil-war-public-domain.jpg")?>") #ccc;
        background-repeat: no-repeat;
        color:#666;
    }
    .odd{
        background:rgba(204,204,204,.6);
    }
    a{
        color:#333;
    }
    div{
        min-width:900px;
        float:left;
        /*max-width:1000px;*/
        margin-left:20px;
        margin-top: 50px;
        border-radius:20px;
        border:3px solid gray;
        padding:    15px 30px 15px 15px;
        background: rgba(255,255,255,.8);
    }
</style>
</head>
<body>
<div>
<h2>Welcome {myName}</h2>
<h3>You can <a href="<?=site_url("wargame/createWargame");?>">Create a new Wargame</a> </h3>
Or play a game You have created:
<ul>
<li><span class="colOne">Name</span><span class="colOne">Game</span><span class="colTwo">single/multi</span><span class="colThree">Turn</span><span class="colFour">Date</span><span class="colFive">Players Involved</span></li>
<li>&nbsp;</li>
    {lobbies}
<li class="{odd}"><a href="<?=site_url("wargame/changeWargame");?>/{id}/"><span class="colOne">{id}</span><span class="colOne">{name}</span><span class="colTwo">{gameType}</span><span class="colThree {myTurn}">It's {turn} turn.</span><span class="colFour">{date}</span></a><span class="colFive"> {players}</span>
    <a href="<?=site_url("wargame/deleteGame");?>/{id}/">delete</a>
    </li>
{/lobbies}
    </ul>
Games I'm in but didn't create:
<ul>
    {otherGames}
    <li><a href="<?=site_url("wargame/changeWargame");?>/{id}/"><span class="colOne">{id}</span><span class="colOne">{name}</span><span class="colTwo">multi</span><span class="colThree">It's {turn} turn.</span><span class="colFour">{date}</span></a><span class="colFive"> {players}</span>
    </li>
    {/otherGames}
</ul>
<a href="<?=site_url("wargame/logout");?>">Logout</a>
    </div>
</body>
</html>