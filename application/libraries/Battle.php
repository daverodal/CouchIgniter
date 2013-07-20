<?php
/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
if(strpos(__FILE__,"/var/www") === false){
    if(strpos(__FILE__,"/Users/david_rodal") !== false){
        define ("WARGAMES","/Users/david_rodal/MampRoot/Game/");
    }else{
        define ("WARGAMES","/xampp/htdocs/");
    }
}else{
    define ("WARGAMES","/var/www/MartianCivilWar");
}
class Battle
{
    private static $theBattle;
       public function resize($size,$player){

       }
    public static  function getBattle($name = false,$doc = null, $arg = false, $argTwo = false){
        try{
        if(self::$theBattle){
            return self::$theBattle;
        }
        self::loadGame($name);
        $thisBattle = new $name($doc, $arg, $argTwo);
        self::$theBattle = $thisBattle;
        return self::$theBattle;;

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getView($name,$mapUrl, $player = 0, $arg = false, $argTwo = false){
        try{
        self::loadGame($name);
        $name::getView($name, $mapUrl,$player, $arg, $argTwo);
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
                require_once(WARGAMES . "MartianCivilWar.php");
                break;
            case "Napoleon":
                include_once(WARGAMES . "Napoleon.php");
                break;
            case "Tutorial":
                include_once(WARGAMES . "Tutorial.php");
                break;
            case "OMCW":
                include_once(WARGAMES . "OrigMartianCivilWar.php");
                break;
            case "Pink":
                @include_once(WARGAMES."pink.php");
                break;
            case "NapOnMars":
                @require_once(WARGAMES . "NapOnMars.php");
                break;
            case "Jagersdorf":
                @include_once(WARGAMES . "Jagersdorf.php");
                break;
            case "Waterloo":
                @include_once(WARGAMES."Waterloo.php");
                break;
            case "BattleOfMoscow":
                include_once(WARGAMES."BattleOfMoscow.php");
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
