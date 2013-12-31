<html>
<head>
    <style type="text/css">
        li{
            list-style: none;
        }
        form{
            display:inline;
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
        <form action="<?=site_url()?>/users/addGame">
            <input type="hidden" name="dir" value="<?=$game->path;?>">
            <!--    <input type="text" name="newgame[]">-->
            <!--    <input type="text" name="newgame[]">-->
            <!--    <input type="text" name="newgame[]">-->
            <input value="refresh" type="submit">
        </form>

    </li>
<?php } ?>
</body>
</html>