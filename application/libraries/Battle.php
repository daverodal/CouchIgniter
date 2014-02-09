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
    public static function pokePlayer($player){
        $CI =& get_instance();

        $CI->load->database();

        $que = 'SELECT count(*) as COUNT FROM  `ci_sessions` WHERE user_data LIKE  "%'.$player.'%" LIMIT 0 , 30';
        $query = $CI->db->query($que);
        foreach ($query->result() as $row)
        {
            if(!$row->COUNT){
                $CI->load->model('users/users_model');
                $userObj = $CI->users_model->getUsersByUsername($player)[0]->value;
                Battle::sendReminder($userObj->email);
            }
        }
    }
    public static function sendReminder($emailAddr){
        $CI =& get_instance();
        $poke_user = $CI->config->item('poke_users');

        if(!$poke_user){
            return;
        }
        $CI->load->library('email');

        $CI->email->from('gameBot@davidrodal.com', 'GameBot ');
        $CI->email->to('dave.rodal@gmail.com');

        $CI->email->subject('Email Test sending to '.$emailAddr);
        $CI->email->message('Your turn.');

        $CI->email->send();

        echo $CI->email->print_debugger();


    }
    private static $theBattle;
    private static $isLoaded = false;
    private static $game;
       public function resize($size,$player){

       }
    public static function getInit($dir){
        $file = file_get_contents(WARGAMES."/".$dir."/info.json");
        return json_decode($file);
    }
    public static  function getBattle($name = false,$doc = null, $arg = false){
        try{
        if(self::$theBattle){
            return self::$theBattle;
        }
        $game = self::loadGame($name, $arg);

        if($game !== false && $arg !== false){
            $scenarios = $game->scenarios->$arg;
            if(!$scenarios){
                $scenarios = new stdClass();
            }
            $className = $game->className;
            $thisBattle = new $className($doc, $arg, $scenarios, $game);
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
        $game = self::loadGame($name, $arg);
            $className = $game->className;
        $className::getView($name, $mapUrl,$player, $arg, $argTwo, $game);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    }
    public static function getHeader($name,$data,$arg){

        if(!isset($arg)){
            die("NO HEADER");
        }
        try{
        $game = self::loadGame($name, $arg);
            $className = $game->className;

            $className::getHeader($name,$data);
        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }
    public static function playAs($name,$wargame,$arg){
        try{
            $game = self::loadGame($name,$arg);
            $className = $game->className;
            $className::playAs($name,$wargame);

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }

    public static function playMulti($name,$wargame,$arg){
        try{
            $game = self::loadGame($name,$arg);
            $className = $game->className;
            $className::playMulti($name,$wargame);

        }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}

    }

    public static function loadGame($name, $arg = false){
        if(self::$isLoaded){
            return self::$game;
        }
        if($arg === false){
            var_dump(debug_backtrace());
            die("loadGame no arg");
        }

        try{
            $CI =& get_instance();
            $CI->load->model('users/users_model');
            $game = $CI->users_model->getGame($name);

            if($game !== false){
                self::$isLoaded = true;
                self::$game = $game;
                $path = $game->path;
                $argTwo = $game->scenarios->$arg;
                set_include_path(WARGAMES . $path . PATH_SEPARATOR . get_include_path());
                require_once(WARGAMES . $path."/".$game->fileName);
                $game->className = preg_replace("/.php$/","",$game->fileName);
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
