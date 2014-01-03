<html>
<head>
    <title>All Users</title>
</head>
<body>
<a href="<?=site_url()?>/users/addUser">Add</a>
<ul>
<?php foreach($logins as $login){?>
    <li><?=$login->name;?> <?=$login->time;?></li>
<?php } ?>
    </ul>
    <a href="<?=site_url()?>/users/logout">logout</a>
</body>
</html>