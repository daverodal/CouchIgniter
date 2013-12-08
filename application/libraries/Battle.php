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
        define ("WARGAMES","/xampp/htdocs/MartianCivilWar/");
    }
}else{
    define ("WARGAMES","/var/www/MartianCivilWar/");
}
set_include_path(WARGAMES . "/stdIncludes" . PATH_SEPARATOR . WARGAMES . PATH_SEPARATOR .  get_include_path());

class Battle
{
    private static $theBattle;
       public function resize($size,$player){

       }
    public static function getInit($dir){
        echo "In $dir ";
        $file = file_get_contents(WARGAMES."/".$dir."/info.json");
        return json_decode($file);
    }
    public static  function getBattle($name = false,$doc = null, $arg = false){
        try{
        if(self::$theBattle){
            return self::$theBattle;
        }
        $game = self::loadGame($name);

        if($game !== false && $arg !== false){
            $scenarios = $game->scenarios->$arg;
            if(!$scenarios){
                $scenarios = new stdClass();
            }
            $className = $game->className;
            $thisBattle = new $className($doc, $arg, $scenarios);
        }else{
            $className = $game->className;

            $thisBattle = new $className($doc);
        }
        self::$theBattle = $thisBattle;
        return self::$theBattle;

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getView($name,$mapUrl, $player = 0, $arg = false, $argTwo = false){
        try{
        $game = self::loadGame($name);
            $className = $game->className;
        $className::getView($name, $mapUrl,$player, $arg, $argTwo);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getHeader($name,$data){
        try{
        $game = self::loadGame($name);
            $className = $game->className;

            $className::getHeader($name,$data);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }
    public static function playAs($name,$wargame){
        try{
            $game = self::loadGame($name);
            $className = $game->className;
            $className::playAs($name,$wargame);

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }

    public static function playMulti($name,$wargame){
        try{
            $game = self::loadGame($name);
            $className = $game->className;
            $className::playMulti($name,$wargame);

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }

    public static function loadGame($name){
        try{
            $CI =& get_instance();
            $CI->load->model('users/users_model');
            $game = $CI->users_model->getGame($name);
            if($game !== false){
                $path = $game->path;
                require_once(WARGAMES . $path."/".$game->fileName);
                $game->className = preg_replace("/.php$/","",$game->fileName);;
                return $game;
            }
            switch($name){
//            case "MartianCivilWar":
//                require_once(WARGAMES . "TMCW/MartianCivilWar.php");
//                break;
//            case "Napoleon":
//                include_once(WARGAMES . "NAPOLEON/Napoleon.php");
//                break;
//            case "Tutorial":
//                include_once(WARGAMES . "Tutorial/Tutorial.php");
//                break;
//            case "OMCW":
//                include_once(WARGAMES . "OMCW/OrigMartianCivilWar.php");
//                break;
//            case "Pink":
//                @include_once(WARGAMES."pink.php");
//                break;
//            case "NapOnMars":
//                @require_once(WARGAMES . "NOM/NapOnMars.php");
//                break;
//            case "Jagersdorf":
//                @include_once(WARGAMES . "Jagersdorf/Jagersdorf.php");
//                break;
//            case "Waterloo":
//                @include_once(WARGAMES."Waterloo.php");
//                break;
//            case "BattleOfMoscow":
//                include_once(WARGAMES."BattleOfMoscow.php");
//                break;
//            case "BattleForAllenCreek":
//                include_once(WARGAMES . "BattleOfAllenCreek/BattleForAllenCreek.php");
//                break;
//            case "NapoleonsTrainingAcademy":
//                include_once(WARGAMES . "NTA/NapoleonsTrainingAcademy.php");
//                break;
//            case "HotWar":
//                require_once(WARGAMES . "HotWar/HotWar.php");
//                break;
            default:
                throw(new Exception("Bad Class in loadGame '$name'"));
        }
    }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    return false;
}

}
