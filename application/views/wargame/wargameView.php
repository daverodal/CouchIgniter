<!doctype html>
<html>
    <?=$this->load->view("wargame/wargameHead.php");?>
    <body>
    <fieldset style="float:right;"><legend>Comlink</legend><div id="comlink"></div></fieldset>
        <h1>Welcome {user} To the <span style="font-style: italic;">&ldquo;{wargame}&rdquo;"</span></h1>
        <div style="clear:both"></div>
        {lobbies}
            <a href="<?=site_url("wargame/changeWargame");?>/{id}">{name}</a>
        {/lobbies}
        <a href="<?=site_url("wargame/createWargame");?>">Create Wargame</a>
        <a href="<?=site_url("wargame/logout");?>">logout</a>
        <a href="<?=site_url("wargame/unitInit");?>">Nuke Game</a>
        <div id="content">
            <style type="text/css">
                body{
                    background:#eee;
                    color:#333;
                }
                fieldset{
                    background:wheat;
                    border-radius:9px;
                }
                #crt{
                    border-radius:15px;
                    border:10px solid #1AF;
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
                    background :#1af;
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
                #gameturnContainer{
                    position:relative;
                    float:right;
                }
                #gameturnContainer div{
                    float:left;
                    height:36px;
                    width:36px;
                    border:solid black;
                    border-width:1px 1px 1px 0;
                    font-size:20px;
                    text-indent:5px;
                }
                #gameturnContainer #turn1{
                    border-width:1px;
                }
                #gameturnContainer #turnCounter{
                    position:absolute;
                    z-index:20;
                    width:32px;
                    height:32px;
                    color:black;
                    background:#9ff;
                    font-size:10px;
                    text-indent:0px;
                    top:2px;
                    left:2px;
                    text-align:center;
                    border-width:1px;
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
            <img class="unit" id="0" alt="0" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: 180px; top: 140px; z-index:100">
            <img class="unit" id="1" alt="1" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -138px; top: 144px;z-index:100">
            <img class="unit" id="2" alt="2" src="<?php echo base_url();?>js/infantry-3a.png" class="counter" style="position: absolute; left: -62px; top: 44px;  z-index:100; ">
            <img class="unit" id="3" alt="3" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -94px; top: 124px;z-index:100 ">
            <img class="unit" id="4" alt="4" src="<?php echo base_url();?>js/infantry-1a.png" class="counter" style="position: absolute; left: -126px; top: 164px; z-index:100">
            <img class="unit" id="5" alt="5" src="<?php echo base_url();?>js/armour-1a.png" class="counter" style="position: absolute; left: -158px; top: 204px; z-index:100; ">
            <fieldset id="redReinBox" style="z-index:20;background:transparent;position:absolute;left:278px;top:46px;width:26px;height:42px;border:black 1px solid;"><legend>Red</legend></fieldset>
            <fieldset id="blueReinBox" style="z-index:20;background:transparent;position:absolute;left:278px;top:123px;width:26px;height:127px;border:black 1px solid;"><legend>Blue</legend></fieldset>
        </div>
        <!-- end gameImages -->
        </div>
    <div id="gameturnContainer">
        <div id="turn1">1</div>
        <div id="turn2">2</div>
        <div id="turn3">3</div>
        <div id="turn4">4</div>
        <div id="turn5">5</div>
        <div id="turn6">6</div>
        <div id="turn7">7</div>
        <div id="turnCounter">Game Turn</div>
    </div>

    <div style="clear:both;height:20px;"> </div>
    <div style="float:left;margin-left: 80px">
        <form onsubmit="doit();return false;" id="chatform" method="post">

    <input id="mychat" name="chats" type="text">
    <input name="submit" type="submit">
    <fieldset>
        <legend>Chats
        </legend>
        <div id="chats"></div>
    </fieldset>
    </div>
    </form>


        <div style="clear:both;"></div>
        <button id="nextPhaseButton">Next Phase</button>
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
            <fieldset>
                <legend>Games
                </legend>
                <div id="games"></div>
            </fieldset>
     </body>
</html>