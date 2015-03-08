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
?><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
<!--    <link href="--><?//=base_url("js/jquery-ui.css");?><!--" rel="stylesheet" type="text/css"/>-->
    <script src="<?=base_url("js/jquery-1.11.1.min.js");?>"></script>
    <script src="<?=base_url("js/jquery-ui-1.11.0.min.js");?>"></script>
<!--    <script src="--><?//=base_url("js/jquery.ui.touch-punch.min.js");?><!--"></script>-->
    <script src="<?=base_url("js/jquery.panzoom/dist/jquery.panzoom.js");?>"></script>
    <script src="<?=base_url("js/jquery.panzoom/test/libs/jquery.mousewheel.js");?>"></script>
    <script src="<?=base_url("js/sync.js");?>"></script>
    <?php Battle::getHeader($gameName, $playerData, $arg);?>
</head>