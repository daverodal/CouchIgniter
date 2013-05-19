<html>
<head>
    <title>All Users</title>
</head>
<body>
<a href="<?=site_url()?>/users/addUser">Add</a>
<ul>
<?php foreach($users as $user){?>
    <li><?=$user->value->username?></li>
<?php } ?>
    </ul>
    <a href="<?=site_url()?>/wargame/logout">logout</a>
</body>
</html>