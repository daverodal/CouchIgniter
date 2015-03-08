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
<head>
<style type="text/css">
    body{
        background: url('<?=base_url("js/Gen._Ulysses_S._Grant_and_portion_of_staff,_Gen._John_A._Rawlins._-_NARA_-_524492.jpg");?>');
        background-repeat: no-repeat;
        background-size:    100%;

    }
    div{
        font-size:22px;
        background:rgba(255,255,255,.9);
        border:1px solid #333;
        border-radius:15px;
        margin:40px;
        padding:20px;
        float:left;
        box-shadow: 10px 10px 10px rgba(20,20,20,.7);
    }
    input{
        margin-left:15px;
    }
    a{
        color:#333;
    }
    .attribution{
        position:absolute;
        bottom:0px;
    }
</style>
</head>
<body>
<div>
    Please login:
<form method="POST">
Username<input type="text" name="name"><br>
    Password
    <input type="password" name="password">
    <input type="submit">
</form>
    <a href="/">Or back to front page</a>
</div>
<div class="attribution">
    Mathew Brady [Public domain], <a target='blank' href="http://commons.wikimedia.org/wiki/File%3AGen._Ulysses_S._Grant_and_portion_of_staff%2C_Gen._John_A._Rawlins._-_NARA_-_524492.jpg">via Wikimedia Commons</a>
</div>
</body>
</html>
