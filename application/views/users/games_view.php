<html>
<head>
    <style type="text/css">
        li{
            list-style: none;
        }
    </style>
    <title>All Users</title>
</head>
<body>
<a href='<?=site_url()?>/users/addGame'>add</a>
<ul>
<?php foreach($games as $game){?>
    <li>
        <?php $delUrl = "deleteGame/?";
            foreach($game->value as $arg){
                $delUrl .= "killgames[]=".$arg."&";
            echo $arg." ";
        }
        echo " <a href='$delUrl'>delete</a>";
        ?>
    </li>
<?php } ?>
</body>
</html>