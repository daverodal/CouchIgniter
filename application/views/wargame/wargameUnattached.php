<!doctype html>
<?php
/**
 * User: David Markarian Rodal
 * Date: 5/3/12
 * Time: 10:21 PM
 */?>
<html>

<head>
    <style type="text/css">
        body{
            background: url('<?=base_url("js/M110_howitzer.jpg");?>');
            background-repeat: no-repeat;
            background-size:    100%;

        }
        div{
            font-size:22px;
            background:rgba(255,255,255,.9);
            border:1px solid #333;
            border-radius:15px;
            margin:40px;
            padding:20px;
            float:left;
            box-shadow: 10px 10px 10px rgba(20,20,20,.7);
        }
        input{
            margin-left:15px;
        }
        a{
            color:#333;
        }
        ul{
            margin-left:100px;
            list-style-type: none;
        }
        li{
            margin:5px 0;
        }
    </style>
</head>
<body>
<div>
Attach to game:
    <ul>
{games}
<li></li><a href="<?=site_url("wargame/unitInit");?>/{name}/{arg}">{name}:{arg}</a></li>
{/games}
    </ul>
<br><br><br>
    Or
<a href="<?=site_url("wargame/logout");?>">Logout</a>
<a href="<?=site_url("wargame/leaveGame");?>">back to lobby</a>
</div>
</body>
</html>
