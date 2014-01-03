<html>
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