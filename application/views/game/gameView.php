<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
?><!doctype html>
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
