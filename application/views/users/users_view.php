<html>
<head>
    <title>All Users</title>
</head>
<body>
<ul>
<?php foreach($users as $user){?>
    <li><?=$user->value->username?></li>
<?php } ?>
</body>
</html>