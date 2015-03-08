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