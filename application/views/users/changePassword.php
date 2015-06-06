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
?><html>
<head>
    <title>My Form</title>
    <link href="<?= base_url("js/lobby.css"); ?>" rel="stylesheet" type="text/css">
    <style type="text/css">
        body{
            background-image: url(<?= base_url("js/French_nuclear-powered_aircraft_carrier_Charles_de_Gaulle.jpeg");?>);
            background-repeat: no-repeat;
            background-position: center top;
            background-size:100%;
        }
        label {
            display:inline-block;
            min-width: 200px;
        }
        .attribution{
            position: absolute;
            bottom:0px;
            background:#eee;
            box-shadow: 10px 10px 10px #666;
        }
    </style>
</head>
<body>
<div class="coolBox">
<a href="<?=site_url("wargame/play");?>">back</a>
<?php echo validation_errors(); ?>
<?php if($save_errors){echo $save_errors;}?>
<?php echo form_open('users/changePassword'); ?>



<label>Old Password</label>
<input type="password" name="currPassword" value="" size="50" />
<br>
<label>Password</label>
<input type="password" name="password" value="" size="50" />
<br>
<label>Password Confirm</label>
<input type="password" name="passconf" value="" size="50" />


<div><input type="submit" value="Submit" /></div>

</form>
    </div>

<div class="attribution">
    By David Townsend, U.S. Navy [Public domain], <a target="blank" href="http://commons.wikimedia.org/wiki/File%3AFrench_nuclear-powered_aircraft_carrier_Charles_de_Gaulle.JPEG">via Wikimedia Commons</a>
</div>
</body>
</html>