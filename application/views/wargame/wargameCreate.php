<html>
<head>
    <script src="<?=base_url("js/jquery-1.11.1.min.js");?>"></script>
    <link href="<?= base_url("js/create.css"); ?>" rel="stylesheet" type="text/css">
    <style type="text/css">
    body{
        background:url("<?=base_url("js/The_British_Army_in_the_Normandy_Campaign_1944_B9045.jpg")?>") #333 no-repeat;
        background-size: 100%;
        background-color: #F5F1DE
    }
        fieldset{
            width:400px;
            background:rgba(255,255,255,.3);
        }
        legend{
            font-size:20px
        }
        a{
            color:black;
        }
    </style>
</head>
<?php
/**
 * User: drodal
 * Date: 11/16/11
 * Time: 6:14 PM
 * To change this template use File | Settings | File Templates.
 */
 
?>
<body>
<?= $message?>
<div class="messageBox">
    What would you like to name your new game? <br>May use spaces or any character you like, need not be unique
    <form method="POST">
        <input id="wargame" name="wargame">
        <input type="submit" value="GO GO GO!">
    </form>
    <a href="<?=site_url("users/logout");?>">Logout</a><br>
    <a href="<?=site_url("wargame/leaveGame");?>">back to lobby</a></div>
<br>

<footer class="unattached attribution">
    By Gee (Sgt), No 5 Army Film & Photographic Unit [Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AThe_British_Army_in_the_Normandy_Campaign_1944_B9045.jpg">via Wikimedia Commons</a>
</footer>

</body>
<script type="text/javascript">
    $(document).ready(function(){
        $("#wargame").focus();
    });
</script>
</html>
