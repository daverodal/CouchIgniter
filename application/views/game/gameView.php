<!doctype html>
<html>
<?=$this->load->view("game/gameHead.php");?>
<body>
<img src="<?=base_url('/clockwait.gif')?>">

    <fieldset>
        <legend>Time
        </legend>
        <div id="clock"></div>
    </fieldset>
    <span class="mil">!"</span>
    <a href="<?=site_url("game/logout");?>">logout</a>
    <button onclick="doit({army:true});return false;">Army</button>
    <button onclick="doit({mines:true});return false;">Mine</button>
    <button onclick="doit({factories:true});return false;">Factory</button>

    <form onsubmit="doit({chats:$('#mychat').attr('value')});return false;" id="chatform" method="post">
        <input id="mychat" name="chats" type="text">
        <input name="submit" type="submit">
    </form>
    <div id="yourside">
        <fieldset>
            <legend>Army
            </legend>
            <div id="army" class='mil'></div>
        </fieldset>
        <fieldset>
            <legend>Building
            </legend>
            <div id="building"></div>
        </fieldset>
        <fieldset>
            <legend>Chats
            </legend>
            <div id="chats"></div>
        </fieldset>
        <fieldset>
            <legend>Gold
            </legend>
            <div id="gold"></div>
        </fieldset>
        <fieldset>
            <legend>Mines
            </legend>
            <div id="mines"></div>
        </fieldset>
        <fieldset>
            <legend>Factories
            </legend>
            <div id="factories"></div>
        </fieldset>
    </div>
    <div id="themap">
        <div id="battle0"></div>
        <div id="battle1"></div>
    </div>
    <div id="theirside">
        <fieldset>
            <legend>Enemy
            </legend>
            <div id="enemy" class='mil'></div>
        </fieldset>
    </div>
</body>
