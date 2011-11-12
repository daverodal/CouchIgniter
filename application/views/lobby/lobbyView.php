<!doctype html>
<html>
    <?=$this->load->view("lobby/lobbyHead.php");?>

    <body>
        <a href="<?=site_url("lobby/logout");?>"/>logout</a>
        <form onsubmit="doit();return false;" id="chatform" method="post">
            <fieldset style="float:left;">
                <legend>Time
                </legend>
                <div id="clock"></div>
            </fieldset>
            <fieldset style="float:right;">
                <legend>Users
                </legend>
                <div id="users"></div>
            </fieldset>
            <div style="clear:both;"></div>
            <input id="mychat" name="chats" type="text">
            <input name="submit" type="submit">
            <fieldset>
                <legend>Chats
                </legend>
                <div id="chats"></div>
            </fieldset>
            <fieldset>
                <legend>Games
                </legend>
                <div id="games"></div>
            </fieldset>


        </form>
        <div style="height:100px;width:100px;background:pink;position:relative">
            <div style="position:absolute;top:14px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
            <div style="position:absolute;top:40px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
            <div style="position:absolute;top:64px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
            <div style="position:absolute;top:14px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
            <div style="position:absolute;top:40px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
            <div style="position:absolute;top:64px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
        </div>
    </body>