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

/**
 * User: David M. Rodal
 * Date: 4/25/12
 * Time: 5:03 PM
 * Link: http://davidrodal.com
 * */
if(strpos(__FILE__,"/var/www") === false){
    if(strpos(__FILE__, "/Library/WebServer/Documents") !== false) {
        define ("WARGAMES", "/Library/WebServer/Documents/MartianCivilWar/");
    }else if(strpos(__FILE__, "/Users/david/Sites/") !== false){
        define ("WARGAMES","/Users/david/Sites/MartianCivilWar/");
    }else  if(strpos(__FILE__,"/Users/david_rodal") !== false){
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
//        $CI =& get_instance();
//
//        $CI->load->database();
//
//        $que = 'SELECT count(*) as COUNT FROM  `ci_sessions` WHERE user_data LIKE  "%\"'.$player.'\"%" LIMIT 0 , 30';
//        $query = $CI->db->query($que);
//        foreach ($query->result() as $row)
//        {
//            if(!$row->COUNT){
//                $CI->load->model('users/users_model');
//                $userObj = $CI->users_model->getUserByUsername($player);
//                echo "$player ";
//                die($row->COUNT);
//                if($userObj){
//                    Battle::sendReminder($userObj->email);
//                }
//            }
//        }
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
    public static  function getBattle($name = false,$doc = null, $arg = false, $options = false){
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
            $params = $game->params ? $game->params : new stdClass();
            foreach($params as $pKey => $pValue){
                if(!isset($scenarios->$pKey)){
                    $scenarios->$pKey = $pValue;
                }
            }
            if($options){
                foreach($options as $name){
                    foreach($game->options as $gameOption){
                        if($gameOption->keyName === $name){
                            if($gameOption->extra){
                                foreach($gameOption->extra as $k=>$v){
                                    $scenarios->$k = $v;
                                }
                            }
                            $scenarios->$name = true;
                        }
                    }

                }
            }
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

    public static function transformChanges($doc, $last_seq, $user){
        try{
            $game = self::loadGame($doc->gameName,$doc->wargame->arg);
            $className = $game->className;
            return $className::transformChanges($doc, $last_seq, $user);

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
                $className = preg_replace("/.php$/","",$game->fileName);

                $matches = [];
                if(preg_match("%([^/]*)/%",$className, $matches)){
                    set_include_path(WARGAMES . "$path/".$matches[1] . PATH_SEPARATOR . get_include_path());
                }


                require_once(WARGAMES . $path."/".$game->fileName);
                $game->className = preg_replace("%[^/]*/%","",$className);
                return $game;
            }
            switch($name){
            default:
                throw(new Exception("Bad Class in loadGame '$name'"));
        }
    }catch(Exception $e){echo $e->getMessage()." ".$e->getFile()." ".$e->getLine();}
    return false;
}

}
