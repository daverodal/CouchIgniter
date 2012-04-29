<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
class Battle
{

    public static  function getBattle($name,$doc){
        switch($name){
            case "MartianCivilWar":
                @include_once("/home/davidrod/webwargaming/MartianCivilWar.php");
                //@include_once("/Documents and Settings/Owner/Desktop/webwargaming/MartianCivilWar.php");
                break;
            case "NapOnMars":
                @include_once("/Documents and Settings/Owner/Desktop/NapOnMars/NapOnMars.php");
                break;
            case "BattleOfMoscow":
                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
        return new $name($doc);
    }

    public static function getView($name){
        switch($name){
            case "MartianCivilWar":
                @include_once("/Documents and Settings/Owner/Desktop/MCW/MartianCivilWar.php");
                MartianCivilWar::getView();
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/view.php");
                break;
            case "NapOnMars":
                @include_once("/Documents and Settings/Owner/Desktop/NapOnMars/napOnMars.php");
                NapOnMars::getView();
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/view.php");
                break;
           case "BattleOfMoscow":
               @include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/view.php");
                break;
            case "BattleForAllenCreek":
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/BattleForAllenCreek.php");
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/view.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
    }
    public static function getHeader($name,$data){
        switch($name){
            case "MartianCivilWar":
                @include_once("/Documents and Settings/Owner/Desktop/MCW/MartianCivilWar.php");
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                MartianCivilWar::getHeader($data);
             break;
            case "NapOnMars":
                @include_once("/Documents and Settings/Owner/Desktop/NapOnMars/NapOnMars.php");
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                NapOnMars::getHeader($data);
                break;
            case "BattleOfMoscow":
                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                break;
            case "BattleForAllenCreek":
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/BattleForAllenCreek.php");
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/header.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }

    }

    public static function loadGame($name){
        switch($name){
            case "MartianCivilWar":
                @include_once("/Documents and Settings/Owner/Desktop/MCW/MartianCivilWar.php");
                break;
            case "NapOnMars":
                @include_once("/Documents and Settings/Owner/Desktop/NapOnMars/NapOnMars.php");
                break;
            case "BattleOfMoscow":
                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                @include_once("/Documents and Settings/Owner/Desktop/BfAC/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
    }

}
