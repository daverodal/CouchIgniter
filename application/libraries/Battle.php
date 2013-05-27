<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
if(strpos('__FILE__',"/var/www") === false){
    define ("WARGAMES","/xampp/htdocs/");
}else{
    define ("WARGAMES","/var/www/");
}
class Battle
{
    private static $theBattle;
       public function resize($size,$player){

       }
    public static  function getBattle($name = false,$doc = null, $arg = false){
        try{
        if(self::$theBattle){
            return self::$theBattle;
        }
        self::loadGame($name);
        $thisBattle = new $name($doc, $arg);
        self::$theBattle = $thisBattle;
        return self::$theBattle;;

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getView($name,$mapUrl, $player = 0, $arg = false){
        try{
        self::loadGame($name);
        $name::getView($name, $mapUrl,$player, $arg);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getHeader($name,$data){
        try{
        self::loadGame($name);
        $name::getHeader($name,$data);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }
    public static function playAs($name,$wargame){
        try{
            self::loadGame($name);
            $name::playAs($name,$wargame);

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }

    public static function loadGame($name){
        try{
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
            case "Jagersdorf":
                @include_once(WARGAMES . "MartianCivilWar/Jagersdorf.php");
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
    }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

}

}
