<?php
/**
 * Created by JetBrains PhpStorm.
 * User: drodal
 * Date: 10/17/11
 * Time: 5:31 PM
 * To change this template use File | Settings | File Templates.
 */
$remote = false;
if($remote){
}else{
    $config["host"] = "localhost";

    $config["port"] = 5984;

    $config["database"] = "mydatabase";
}