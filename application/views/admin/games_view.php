<?php
$this->load->view('admin/adminHeader');
$this->load->view('admin/adminMenu');
?>

    <style type="text/css">
        li{
            list-style: none;
        }
        form{
            display:inline;
        }
    </style>
<div>
<a href='<?=site_url()?>/admin/addGame'>add</a>
<ul>
<?php foreach($games as $key => $game){?>
    <li>

        <?php
            if($game->name){
                echo $game->name;
            }
            $delUrl = "deleteGameType/?";
                $delUrl .= "killGame=".$game->key;

        echo " <a href='$delUrl'>delete</a>";
        ?>
        <form action="<?=site_url()?>/admin/addGame">
            <input type="hidden" name="dir" value="<?=$game->path;?>">
            <!--    <input type="text" name="newgame[]">-->
            <!--    <input type="text" name="newgame[]">-->
            <!--    <input type="text" name="newgame[]">-->
            <input value="refresh" type="submit">
        </form>

    </li>
<?php } ?>
    </ul>
    </div>

</body>
</html>