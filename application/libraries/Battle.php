<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
define ("WARGAMES","/xampp/htdocs/");
class Battle
{
       public function resize($size,$player){

       }
    public static  function getBattle($name,$doc, $arg = false){
        self::loadGame($name);
        return new $name($doc, $arg);
    }
    public static function getView($name){
        self::loadGame($name);
        $name::getView();
    }
    public static function getHeader($name,$data){
        self::loadGame($name);
        $name::getHeader($data);

    }
    public static function playAs($name,$wargame){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES."MartianCivilWar/MartianCivilWar.php");
                MartianCivilWar::playAs($wargame);
                break;
            case "MCW":
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
                MCW::playAs($wargame);
                break;
            case "Pink":
                @include_once(WARGAMES."pink/pink.php");
                Pink::playAs($wargame);
                break;
            case "NapOnMars":
                @include_once(WARGAMES."NapoleonOnMars/NapOnMars.php");
                echo "Wargame is $wargame";
                NapOnMars::playAs($wargame);
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                break;
            case "Waterloo":
                @include_once(WARGAMES."NapOnMars/Waterloo.php");
                echo "Wargame is $wargame";
                Waterloo::playAs($wargame);
//                @include_once("/Documents and Settings/Owner/Desktop/webwargaming/header.php");
                break;
            case "BattleOfMoscow":
                redirect("/wargame/play");
                break;
            case "BattleForAllenCreek":
                redirect("/wargame/play");

                break;
            default:
                throw(new Exception("Bad Class playAs '$name''"));
        }

    }

    public static function loadGame($name){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES."MartianCivilWar/MartianCivilWar.php");
                break;
            case "MCW":
                @include_once(WARGAMES."MCW/MartianCivilWar.php");
                break;
            case "Pink":
                @include_once(WARGAMES."pink/pink.php");
                break;
            case "NapOnMars":
                @include_once(WARGAMES."NapoleonOnMars/NapOnMars.php");
                break;
            case "Waterloo":
                @include_once(WARGAMES."NapOnMars/Waterloo.php");
                break;
            case "BattleOfMoscow":
                include_once(WARGAMES."BattleOfMoscow/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                include_once(WARGAMES."webwargaming/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class in loadGame '$name'"));
        }
    }

}
