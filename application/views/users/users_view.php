<?php
$this->load->view('admin/adminHeader');
$this->load->view('admin/adminMenu');
?>
<div>
    <a href="<?= site_url() ?>/users/addUser">Add</a>
    <ul>
        <?php foreach ($users as $user) { ?>
            <li><?= $user->value->username ?></li>
        <?php } ?>
    </ul>
    <a href="<?= site_url() ?>/wargame/logout">logout</a>
</div>
</body>
</html>