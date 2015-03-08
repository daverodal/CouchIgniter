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
</head>
<body>
<a href="<?=site_url("wargame/play");?>">back</a>
<?php echo validation_errors(); ?>
<?php if($save_errors){echo $save_errors;}?>
<?php echo form_open('users/changePassword'); ?>



<h5>Old Password</h5>
<input type="password" name="currPassword" value="" size="50" />

<h5>Password</h5>
<input type="password" name="password" value="" size="50" />

<h5>Password Confirm</h5>
<input type="password" name="passconf" value="" size="50" />


<div><input type="submit" value="Submit" /></div>

</form>

</body>
</html>