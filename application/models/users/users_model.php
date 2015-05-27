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
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 1/29/13
 * Time: 3:49 PM
 * To change this template use File | Settings | File Templates.
 */
class Users_model extends CI_Model
{

    private $prevDB;

    private function _setDB(){
        $this->prevDB = $this->couchsag->sag->currentDatabase();
        $dbName = $this->config->item('user_db');
        $this->couchsag->sag->setDatabase($dbName);

    }
    private function _restoreDB(){
        $this->couchsag->sag->setDatabase($this->prevDB);
    }
    public function getUserByUserName($name){
        $this->_setDB();
        $startKey = "";
        if($name !== false){
            $startKey = "?startkey=\"$name\"&endkey=\"$name"."zzzzzzzzzzzzzzzzzzzzzzzz\"";
        }
        $seq = $this->couchsag->get("/_design/newFilter/_view/userByUsername$startKey");
        $this->_restoreDB();
        $rows = $seq->rows;
        foreach($rows as $row){
            if($row->key == $name){
                return $row->value;
            }
        }
        return false;
    }
    public function getUsersByUsername($name = false){
        $this->_setDB();
        $seq = $this->couchsag->get("/_design/newFilter/_view/userByUsername");
        $this->_restoreDB();
        return $seq->rows;
    }

    public function getUsersByEmail(){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $this->_restoreDB();
            return $usersDoc->userByEmail;
        }
        $this->_restoreDB();
        return array();
    }

    public function getUserByEmail($email){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $user = $usersDoc->userByEmail->$email;
            $this->_restoreDB();
            return $user;
        }
        $this->_restoreDB();
        return array();
    }

    public function initDoc(){

        $dbName = $this->config->item('user_db');
        $this->couchsag->sag->setDatabase($dbName);

        try{
            echo "is Users doc presesnt?\n";
            $doc = $this->couchsag->get("users");
        }catch(Exception $e){};
        if(!$doc){
            $data = array("_id" => "users", "docType" => "users", "userByEmail" => new stdClass(), "userId"=> 1);
            echo "createing users\n";
                $this->couchsag->create($data);
            echo "Created them\n";
        }else{
            echo "users doc found, leaving untouched\n";
        }

        $doc = false;

        try{
            echo "is userLogins doc presesnt?\n";
            $doc = $this->couchsag->get("userLogins");
        }catch(Exception $e){};
        if(!$doc){
            $data = array("_id" => "userLogins", "docType" => "userLogins", "logins" => array());
            echo "createing userLogins\n";
            $this->couchsag->create($data);
            echo "Created them\n";
        }else{
            echo "userLogins doc found, leaving untouched\n";
        }

        $doc = false;
        try{
            echo "is gnuGamesAvail doc presesnt?\n";
            $doc = $this->couchsag->get("gnuGamesAvail");
            var_dump($doc);
            $this->couchsag->delete($doc->_id,$doc->_rev);
            echo "deleted it";
        }catch(Exception $e){};
        if(true || !$doc){
            $data = array("_id" => "gnuGamesAvail", "docType" => "gnuGamesAvail", "games" => new stdClass());
            echo "createing gnuGamesAvail\n";
            $this->couchsag->create($data);
            var_dump($data);
            echo "Created them\n";
        }else{
            var_dump($doc);
            echo "gnuGamesAvail doc found, leaving untouched\n";
        }

        $users = new StdClass();
        $users->map = <<<aHEREMAP
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        emit(email,doc.userByEmail[email]);
                    }
                }
            }
        }
aHEREMAP;


        $userById = new stdClass();
        $userById->map = <<<byId
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    var aThing;
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].id,theUser);
                    }
                }
            }
        }
byId;
        $userByUsername = new stdClass();
        $userByUsername->map = <<<byUsername
        function(doc) {
            if(doc.docType == 'users'){
                var ret = 0;

                if(doc.userByEmail){
                    for(var email in doc.userByEmail){
                        aUser = doc.userByEmail[email];
                        theUser = {};
                        for(x in aUser){
                            theUser[x] = aUser[x];
                        }
                        theUser.email = email;
                        emit(doc.userByEmail[email].username,theUser);
                    }
                }
            }
        }

byUsername;

        $gnuGetAvailGames = new stdClass();
        $gnuGetAvailGames->map = <<<gamesAvail
            function(doc){
                if(doc.docType == 'gnuGamesAvail'){
                    if(doc.games){
                        for(var i in doc.games){
                            emit([doc.games[i].path,doc.games[i].genre,i],doc.games[i]);
                        }
                    }
                }
            }

gamesAvail;
        $getCustomScenarios = new stdClass();
        $getCustomScenarios->map = <<<customScenarios
            function(doc){
                if(doc.docType == 'scenariosAvail'){
                    if(doc.games){
                        for(var i in doc.games){
                          for(var s in doc.games[i].scenarios){
                            emit([doc.games[i].path,doc.games[i].genre,i,s],doc.games[i].scenarios[s]);
                          }
                        }
                    }
                }
            }

customScenarios;

        $gnuGetAvailGames->reduce = <<<gamesAvailReduce
            function(keys, values, rereduce) {
                if (rereduce) {
                    return sum(values);
                } else {
                    return values.length;
                }
            }

gamesAvailReduce;



        $views['userByEmail'] = $users;
        $views['userById'] = $userById;
        $views['userByUsername'] = $userByUsername;
        $views['gnuGetAvailGames'] = $gnuGetAvailGames;
        $views['getCustomScenarios'] = $getCustomScenarios;

//        $data = array("_id" => "_design/newFilter", "views" => $views, "filters" => $filters, "updates"=> $updates);
        $data = array("_id" => "_design/newFilter", "views" => $views);

        try{
            $doc = $this->couchsag->get("_design/newFilter");
        }catch(Exception $e){};
        if($doc){
            echo "design doc found, deleting.\n";
            $delDoc = $this->couchsag->delete($doc->_id,$doc->_rev);
            echo "deleted\n";
        }
        echo "creating design doc\n";
        $this->couchsag->create($data);
        echo "did it";
        return;

        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $email = "dave.rodal@gmail.com";
            $user = new stdClass();
            $user->id = $usersDoc->userId++;
            $user->password = "2havefun";
            $usersDoc->userByEmail->$email = $user;
            $this->couchsag->update($usersDoc->_id, $usersDoc);
            return $user;
        }

    }
    public function addUser($email, $password, $username, $editor){
        $this->_setDB();
        $strikes = 0;
        while($strikes < 3){
            $usersDoc = $this->couchsag->get("users");
    //        $usersDoc->_rev = "";
            if($usersDoc->docType == "users"){
                $users = $usersDoc->userByEmail;
                if($users->$email){
                    return "Email already used: $email";
                }

                foreach($users as  $user){
                    if($user->username == $username){
                        return "Username already used: $username";
                    }
                }
                $user = new stdClass();
                $user->id = $usersDoc->userId++;
                $user->username = $username;
                $user->password = $password;
                if($editor){
                    $user->editor = true;
                }
                $usersDoc->userByEmail->$email = $user;
                if(isset($usersDoc->userById)){
                    unset($usersDoc->userById);
                }
                $ret = $this->couchsag->update($usersDoc->_id, $usersDoc);
                if($ret && $ret->ok){
                    return false;
                }
            }
        }
        return "Cannot save $strikes strikes";
    }

    public function changePassword($password, $newPassword){
            $username = $this->session->userdata("user");
        $this->_setDB();
        $strikes = 0;
        while($strikes < 3){
            $usersDoc = $this->couchsag->get("users");
            //        $usersDoc->_rev = "";
            if($usersDoc->docType == "users"){
                $users = $usersDoc->userByEmail;

                foreach($users as $email => $user){
                    if($user->username == $username){
                        $foundUser = $user;
                        break;
                    }
                }
                if(!$foundUser){
                    return "Cannot find user $username";
                }

                if($foundUser->password != $password){
                    return "Old password does not match";
                }
                $foundUser->password = $newPassword;
                $usersDoc->userByEmail->{$email} = $user;
                $ret = $this->couchsag->update($usersDoc->_id, $usersDoc);
                if($ret && $ret->ok){
                    $headers = 'From: wargame_daemon@davidrodal.com' . "\r\n" .
                        'Reply-To: dave.rodal@gmail.com' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    mail($email,"Password Change","Dear $username, Your password for davidrodal.com has been changed",$headers);
                    return false;
                }
            }
        }
        return "Cannot save $strikes strikes";
    }

    public function isValidLogin($email, $password){
        $this->_setDB();
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            if(isset($usersDoc->userByEmail->$email))
            {
                if($usersDoc->userByEmail->$email->password == $password){
                    $this->_restoreDB();
                    return true;
                }
            }
        }
        $this->_restoreDB();
        return false;
    }
    public function addGame($games){
        $this->_setDB();
        $doc = $this->couchsag->get("gnuGamesAvail");
        if($doc->docType == "gnuGamesAvail"){
            foreach($games as $name => $game) {
                if($game->disabled === true){
                    continue;
                }
                $doc->games->$name = $game;
            }
        }
        $ret = $this->couchsag->update($doc->_id, $doc);
        $this->_restoreDB();
    }
    public function deleteGame($killGame){
        if(!$killGame){
            return false;
        }
        $this->_setDB();
        $doc = $this->couchsag->get("gnuGamesAvail");
        if(!$doc->docType == "gnuGamesAvail"){
            return false;
        }
        unset($doc->games->$killGame);
        $ret = $this->couchsag->update($doc->_id, $doc);
        $this->_restoreDB();
    }
	public function userLoggedIn($user){
		$this->_setDB();
		$doc = $this->couchsag->get("userLogins");
		$gnu = new stdClass();
		$gnu->name = $user;
		$gnu->time = date("Y-m-d H:i:s");
		$doc->logins[] = $gnu;
		$this->couchsag->update($doc->_id, $doc);
		$this->_restoreDB();
	}
    public function getAvailGames($dir = false, $genre = false, $game = false){
        $reduceArgs = "group=true&group_level=2";
        if($dir !== false){
            $reduceArgs = "group=true&group_level=2&startkey=[\"$dir\"]&endkey=[\"$dir\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
            if($dir === true){
                $reduceArgs = "reduce=false";
            }
            if($genre !== false){
                $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\"]&endkey=[\"$dir\",\"$genre\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                if($game !== false){
                    $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\",\"$game\"]&endkey=[\"$dir\",\"$genre\",\"$game\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                }
            }
        }
        $this->_setDB();

        $seq = $this->couchsag->get("/_design/newFilter/_view/gnuGetAvailGames?$reduceArgs");
        $this->_restoreDB();
        $rows = $seq->rows;
        $games = [];

        foreach($rows as $row){
            if($dir === true){
                $game = $row->value;
                $game->key = $row->key;
            }else{
                $game = new stdClass();
                $game->dir = $row->key[0];
                $game->genre = $row->key[1];
                $game->game = $row->key[2];
                $game->value = $row->value;
            }
            $games[] = $game;
        }

        return $games;
    }
    public function getCustomScenarios($dir = false, $genre = false, $game = false){
        $reduceArgs = "group=true&group_level=2";
        if($dir !== false){
            $reduceArgs = "group=true&group_level=2&startkey=[\"$dir\"]&endkey=[\"$dir\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
            if($dir === true){
                $reduceArgs = "reduce=false";
            }
            if($genre !== false){
                $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\"]&endkey=[\"$dir\",\"$genre\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                if($game !== false){
                    $reduceArgs = "reduce=false&startkey=[\"$dir\",\"$genre\",\"$game\"]&endkey=[\"$dir\",\"$genre\",\"$game\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]";
                }
            }
        }
        $this->_setDB();

        $seq = $this->couchsag->get("/_design/newFilter/_view/getCustomScenarios?$reduceArgs");
        $this->_restoreDB();
        $rows = $seq->rows;
        $games = [];

        foreach($rows as $row){
            if($dir === true){
                $game = $row->value;
                $game->key = $row->key;
            }else{
                $game = new stdClass();
                $game->dir = $row->key[0];
                $game->genre = $row->key[1];
                $game->game = $row->key[2];
                $game->scenario = $row->key[3];
                $game->value = $row->value;
            }
            $games[] = $game;
        }

        return $games;
    }

    public function getGame($gameName){
        $games = $this->getAvailGames(true);
        foreach($games as $game){
            if($gameName == $game->key[2]){
                return $game;
            }
        }
        return false;
    }

    public function getLogins(){
        $this->_setDB();
        $logins = $this->couchsag->get("userLogins");
        $this->_restoreDB();
        return $logins;

    }

}
