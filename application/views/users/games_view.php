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
<?php foreach($games as $key => $game){?>
    <li>

        <?php
            if($game->name){
                echo $game->name;
            }
            $delUrl = "deleteGame/?";
                $delUrl .= "killGame=".$game->key;

        echo " <a href='$delUrl'>delete</a>";
        ?>
    </li>
<?php } ?>
</body>
</html>