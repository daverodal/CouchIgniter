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
            <style type="text/css">
                body{
                    background:wheat;
                    color:#333;
                }
                fieldset{
                    background:wheat;
                    border-radius:9px;
                }
                #crt{
                    border-radius:15px;
                    border:10px solid rgb(153,255,255);
                    position:relative;
                    width:250px;
                    height:210px;
                    background:#fff;color:black;
                    font-weight:bold;
                    padding:1px 5px 0 15px;
                    float:left;
                }
                #crt span{
                    width:25px;
                    position:absolute;
                }
                .col1{
                    left:20px;
                }
                .col2{
                    left:60px;
                }
                .col3{
                    left:100px;
                }
                .col4{
                    left:140px;
                }
                .col5{
                    left:180px;
                }
                .col6{
                    left:220px;
                }
                .roll{
                    height:20px;
                    width:90%;
                    background :rgb(153,255,255);
                    position:absolute;

                }
                .even{
                    color:black;
                }
                .odd{
                    color:black;
                }
                .row1{
                    top:80px;

                }
                .row2{
                    top:100px;
                    background:white;
                }
                .row3 {
                    top:120px;
                }
                .row4{
                    top:140px;
                    background:white;
                }
                .row5{
                    top:160px;
                }
                .row6{
                    top:180px;
                    background:white;
                }
                #odds{
                    position:absolute;
                    text-indent:8px;
                    top:60px;
                }
                .roll span{
                    margin-right:10px
                }
            </style>
            <div id="crt">
                <h3>Combat Odds</h3>
                <div id="odds"><span class="col1">0</span> <span class="col2">1</span> <span class="col3">2</span> <span class="col4">3</span> <span class="col5">4</span> <span class="col6">5</span></div>
                <div class="roll row1 odd"><span class="col0">1</span><span class="col1">DR</span> <span class="col2">DR</span> <span class="col3">DR</span> <span class="col4">DE</span> <span class="col5">DE</span> <span class="col6">DE</span></div>
                <div class="roll row2 even"><span class="col0">2</span><span class="col1">NR</span> <span class="col2">DR</span> <span class="col3">DR</span> <span class="col4">DR</span> <span class="col5">DE</span> <span class="col6">DE</span></div>
                <div class="roll row3 odd"><span class="col0">3</span><span class="col1">NR</span> <span class="col2">NR</span> <span class="col3">NR</span> <span class="col4">DR</span> <span class="col5">DR</span> <span class="col6">DE</span></div>
                <div class="roll row4 even"><span class="col0">4</span><span class="col1">AR</span> <span class="col2">NR</span> <span class="col3">NR</span> <span class="col4">DR</span> <span class="col5">DR</span> <span class="col6">DR</span></div>
                <div class="roll row5 odd"><span class="col0">5</span><span class="col1">AR</span> <span class="col2">AR</span> <span class="col3">NR</span> <span class="col4">NR</span> <span class="col5">DR</span> <span class="col6">DR</span></div>
                <div class="roll row6 even"><span class="col0">6</span><span class="col1">AE</span> <span class="col2">AR</span> <span class="col3">AR</span> <span class="col4">NR</span> <span class="col5">DR</span> <span class="col6">DR</span></div>
            </div>
        <div id="gameImages" style="float:left;margin-left:10px;position: relative;width:252px;height:260px;border:10px solid #333;border-radius:10px;">
            <img id="map" alt="map" src="<?php echo base_url();?>js/BattleForAllenCreekMap.png" style="position: relative;visibility: visible;z-index: 0;">
            <img class="unit" id="0" alt="0" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: 180px; top: 140px; ">
            <img class="unit" id="1" alt="1" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -138px; top: 144px; ">
            <img class="unit" id="2" alt="2" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -62px; top: 44px; ">
            <img class="unit" id="3" alt="3" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -94px; top: 124px; ">
            <img class="unit" id="4" alt="4" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -126px; top: 164px; ">
            <img class="unit" id="5" alt="5" src="<?php echo base_url();?>js/armour-1a.png" class="counter" style="position: absolute; left: -158px; top: 204px; "></div>
        <!-- end gameImages -->
        </div>
        <div style="clear:both;"></div>
        <button id="nextPhaseButton">Next Phase</button>
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