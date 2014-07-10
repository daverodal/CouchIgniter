<html>
<head>
    <script src="<?=base_url("js/jquery-1.11.1.min.js");?>"></script>
    <style type="text/css">
    body{
        background:url("<?=base_url("js/britishTank.jpg")?>") #333 no-repeat;
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
<fieldset><legend>What would you like to name your new game? May use spaces or any character you like, need not be unique</legend>
<form method="POST">
 <input id="wargame" name="wargame">
    <input type="submit" value="GO GO GO!">
</form></fieldset>
<br>
<a href="<?=site_url("users/logout");?>">Logout</a><br>
<a href="<?=site_url("wargame/leaveGame");?>">back to lobby</a>
</body>
<script type="text/javascript">
    $(document).ready(function(){
        $("#wargame").focus();
    });
</script>
</html>
