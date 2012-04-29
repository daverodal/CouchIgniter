<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
define ("WARGAMES","/Documents and Settings/Owner/Desktop/");
class Battle
{

    public static  function getBattle($name,$doc){
        switch($name){
            case "MartianCivilWar":
//                @include_once("/home/davidrod/webwargaming/MartianCivilWar.php");
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
                break;
            case "NapOnMars":
                @include_once(WARGAMES."NapOnMars/NapOnMars.php");
                break;
            case "BattleOfMoscow":
                @include_once(WARGAMES."webwargaming/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                @include_once(WARGAMES."BfAC/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
        return new $name($doc);
    }
    public static function getView($name){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
                MartianCivilWar::getView();
//                @include_once(WARGAMES."webwargaming/view.php");
                break;
            case "NapOnMars":
                @include_once(WARGAMES."NapOnMars/napOnMars.php");
                NapOnMars::getView();
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/view.php");
                break;
           case "BattleOfMoscow":
               @include_once(WARGAMES."webwargaming/BattleOfMoscow.php");
                @include_once(WARGAMES."webwargaming/view.php");
                break;
            case "BattleForAllenCreek":
                @include_once(WARGAMES."BfAC/BattleForAllenCreek.php");
                @include_once(WARGAMES."BfAC/view.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
    }
    public static function getHeader($name,$data){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                MartianCivilWar::getHeader($data);
             break;
            case "NapOnMars":
                @include_once(WARGAMES."NapOnMars/NapOnMars.php");
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                NapOnMars::getHeader($data);
                break;
            case "BattleOfMoscow":
                @include_once(WARGAMES."webwargaming/BattleOfMoscow.php");
                @include_once(WARGAMES."webwargaming/header.php");
                break;
            case "BattleForAllenCreek":
                @include_once(WARGAMES."BfAC/BattleForAllenCreek.php");
                @include_once(WARGAMES."BfAC/header.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }

    }

    public static function loadGame($name){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
                break;
            case "NapOnMars":
                @include_once(WARGAMES."NapOnMars/NapOnMars.php");
                break;
            case "BattleOfMoscow":
                @include_once(WARGAMES."webwargaming/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                @include_once(WARGAMES."BfAC/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
    }

}
