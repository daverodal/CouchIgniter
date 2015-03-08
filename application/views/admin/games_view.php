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

<style type="text/css">
    li {
        list-style: none;
    }

    form {
        display: inline;
    }
</style>
<div>
    <a href='<?= site_url() ?>/admin/addGame'>add</a>
    <ul>
        <?php foreach ($games as $key => $game) { ?>
            <li>

                <?php
                if ($game->name) {
                    echo $game->name;
                }
                $delUrl = "deleteGameType/?";
                $delUrl .= "killGame=" . $game->key[2];

                echo " <a href='$delUrl'>delete</a>";
                ?>
                <form action="<?= site_url() ?>/admin/addGame">
                    <input type="hidden" name="dir" value="<?= $game->path; ?>">
                    <!--    <input type="text" name="newgame[]">-->
                    <!--    <input type="text" name="newgame[]">-->
                    <!--    <input type="text" name="newgame[]">-->
                    <input value="refresh" type="submit">
                </form>

            </li>
        <?php } ?>
    </ul>
</div>

</body>
</html>