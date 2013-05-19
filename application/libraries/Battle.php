<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
define ("WARGAMES","/Users/markarianr/Sites/");
//define ("WARGAMES","/var/www/");
class Battle
{
    private static $theBattle;
       public function resize($size,$player){

       }
    public static  function getBattle($name = false,$doc = null, $arg = false){

        if(self::$theBattle){
            return self::$theBattle;
        }
        self::loadGame($name);
        $thisBattle = new $name($doc, $arg);
        self::$theBattle = $thisBattle;
        return self::$theBattle;;
    }
    public static function getView($name,$mapUrl, $player = 0, $arg = false){
        self::loadGame($name);
        $name::getView($mapUrl,$player, $arg);
    }
    public static function getHeader($name,$data){
        self::loadGame($name);
        $name::getHeader($data);

    }
    public static function playAs($name,$wargame){
        switch($name){
            case "MartianCivilWar":
                @include_once(WARGAMES . "MartianCivilWar/MartianCivilWar.php");
                MartianCivilWar::playAs($wargame);
                break;
            case "Napoleon":
                @include_once(WARGAMES . "MartianCivilWar/Napoleon.php");
                Napoleon::playAs($wargame);
                break;
            case "Tutorial":
                @include_once(WARGAMES . "MartianCivilWar/Tutorial.php");
                Tutorial::playAs($wargame);
                break;
            case "OMCW":
                @include_once(WARGAMES . "MartianCivilWar/OrigMartianCivilWar.php");
                OMCW::playAs($wargame);
                break;
            case "Pink":
                @include_once(WARGAMES."pink/pink.php");
                Pink::playAs($wargame);
                break;
            case "NapOnMars":
                @include_once(WARGAMES . "MartianCivilWar/NapOnMars.php");
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
                @include_once(WARGAMES . "MartianCivilWar/BattleForAllenCreek.php");
                BattleForAllenCreek::playAs($wargame);

                break;
            default:
                throw(new Exception("Bad Class playAs '$name''"));
        }

    }

    public static function loadGame($name){
        switch($name){
            case "MartianCivilWar":
                include_once(WARGAMES . "MartianCivilWar/MartianCivilWar.php");
                break;
            case "Napoleon":
                include_once(WARGAMES . "MartianCivilWar/Napoleon.php");
                break;
            case "Tutorial":
                include_once(WARGAMES . "MartianCivilWar/Tutorial.php");
                break;
            case "OMCW":
                include_once(WARGAMES . "MartianCivilWar/OrigMartianCivilWar.php");
                break;
            case "Pink":
                @include_once(WARGAMES."pink/pink.php");
                break;
            case "NapOnMars":
                @include_once(WARGAMES . "MartianCivilWar/NapOnMars.php");
                break;
            case "Waterloo":
                @include_once(WARGAMES."NapOnMars/Waterloo.php");
                break;
            case "BattleOfMoscow":
                include_once(WARGAMES."BattleOfMoscow/BattleOfMoscow.php");
                break;
            case "BattleForAllenCreek":
                include_once(WARGAMES . "BattleOfAllenCreek/BattleForAllenCreek.php");
                break;
            default:
                throw(new Exception("Bad Class in loadGame '$name'"));
        }
    }

}
