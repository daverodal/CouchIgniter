<!doctype html>
<html>
    <?=$this->load->view("wargame/wargameHead.php");?>
    <body>
        <h1>Welcome {user} To the <span style="font-style: italic;">&ldquo;{wargame}&rdquo;"</span></h1>
        {lobbies}
            <a href="<?=site_url("wargame/changeWargame");?>/{id}">{name}</a>
        {/lobbies}
        <a href="<?=site_url("wargame/createWargame");?>">Create Wargame</a>
        <a href="<?=site_url("wargame/logout");?>">logout</a>
        <div id="content">
        <div id="gameImages" style="position: relative;">
            <img id="map" alt="map" src="<?php echo base_url();?>js/BattleForAllenCreekMap.png" style="position: relative;visibility: visible;z-index: 0;">
            <img id="0" alt="0" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: 180px; top: 140px; "><img id="1" alt="1" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -138px; top: 144px; "><img id="2" alt="2" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -62px; top: 44px; "><img id="3" alt="3" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -94px; top: 124px; "><img id="4" alt="4" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -126px; top: 164px; "><img id="5" alt="5" src="<?php echo base_url();?>js/armour-1a.png" class="counter" style="position: absolute; left: -158px; top: 204px; "></div>
        <!-- end gameImages -->
        </div>
        <div style="clear:both;"></div>
        <form onsubmit="doit();return false;" id="chatform" method="post">
            <fieldset style="float:left;">
                <legend>Time
                </legend>
                <div id="clock"></div>
            </fieldset>
            <fieldset style="float:left;">
                <legend>Status
                </legend>
                <div id="status"></div>
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
     </body>
</html>