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
            background:#ccc;
            color:#333;
            background: url("<?=base_url("js/civil-war-public-domain-front.jpg")?>") #ccc no-repeat;
            background-position: 25% 0;
        }
        .wrapper{
            background:rgba(255,255,255,.8);
            border-radius:15px;
            padding:20px;
            margin:20px;
            border:3px solid gray;
        }
        a{
            color:#000;
        }
        li{
            list-style-type: none;
        }
        div{
            text-align:center;
        }
        .center{
            float:left;
            width:8%;
            font-size:45px;
        }
        .left{
            width:45%;

            float:left;
        }
        .right{
            width:45%;

            float:right;
        }
        .clear{
            clear:both;
        }
        .rebel{
            color:red;
        }
        .loyalist{
            color:blue;
        }
        .big{
            font-size: 50px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="wrapper">
<div class="left rebel big">REBEL</div>
<div class="right loyalist big">LOYALIST</div>
<div class="clear"></div>
<div class="left big rebel">
    YOU
</div>
<div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
<div class="right">
<ul>
{users}
<li><a class="loyalist" href="{path}/{wargame}/{me}/{key}">{key}</a></li>
{/users}
</ul>
</div>
<div class="clear"></div>
<div class="big">OR</div>
<div class="left">
<ul>
{others}
<li><a class="rebel" href="{path}/{wargame}/{key}">{key}</a></li>
{/others}
</ul>
    </div>
<div class="center">&laquo;&laquo;vs&raquo;&raquo;</div>
<div class="right big loyalist">YOU</div>
<div class="clear"></div>
<div>
<a href="<?=site_url("wargame/play");?>">Back to lobby</a>
    </div>
    </div>
