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

class Wargame_model extends CI_Model
{

    public function enterHotseat($wargame)
    {
        $user = $this->session->userdata("user");
        try {
            $doc = $this->couchsag->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!doc || $user != $doc->createUser) {
            return false;
        }
        $doc->playerStatus = "hot seat";
        $doc->wargame->players[1] = $doc->wargame->players[2] = $user;
//        foreach($doc->wargame->players as $k => $v){
//            if($v != $user){
//                $doc->wargame->players[$k] = "";
//            }
//        }
//        $doc->wargame->players[1] = $user;
        $doc->wargame->gameRules->turnChange = true;
        $this->couchsag->update($doc->_id, $doc);
        return true;
    }

    public function makePublic($wargame)
    {
        $user = $this->session->userdata("user");
        try {
            $doc = $this->couchsag->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!doc || $user != $doc->createUser) {
            return false;
        }
        $doc->visibility = "public";
        $this->couchsag->update($doc->_id, $doc);
        return true;
    }

    public function makePrivate($wargame)
    {
        $user = $this->session->userdata("user");
        try {
            $doc = $this->couchsag->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!doc || $user != $doc->createUser) {
            return false;
        }
        $doc->visibility = "private";
        $this->couchsag->update($doc->_id, $doc);
        return true;
    }
    public function enterMulti($wargame, $playerOne, $playerTwo, $visibility, $playerThree, $playerFour)
    {
        $user = $this->session->userdata("user");
        try {
            $doc = $this->couchsag->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        if (!doc || $user != $doc->createUser) {
            return false;
        }
        $doc->playerStatus = "multi";
        $doc->visibility = $visibility;
        $doc->wargame->players = array("", $playerOne, $playerTwo);
        if($playerThree){
            $doc->wargame->players[] = $playerThree;
            if($playerFour){
                $doc->wargame->players[] = $playerFour;
            }
        }
        $doc->wargame->gameRules->turnChange = true;
        $this->couchsag->update($doc->_id, $doc);
        return true;
    }
    public function enterWargame($user, $wargame)
    {

        $doc = $this->couchsag->get($wargame);
        if ($doc->playerStatus == "created") {
            return;
        }
        if ($doc->playerStatus == "multi") {
            return;
        }
        if ($doc->playerStatus == "hot seat") {
            return;
            if ($user == $doc->createUser) {
                $player = $doc->wargame->gameRules->attackingForceId;
            } else {
                $player = 0;
            }
        }
//        if (!is_array($doc->wargame->players)) {
//            $doc->wargame->players = array("","","");
//        }
//        if (!in_array($user, $doc->wargame->players)) {
//            $doc->wargame->players[$player] = $user;
//        }else{
//            $index = array_search($user, $doc->wargame->players);
//            $doc->wargame->players[$index] = "";
//            $doc->wargame->players[$player] = $user;
//        }
        $this->couchsag->update($doc->_id, $doc);

    }

    public function saveTerrainDoc($terrainDocName, $wargameDoc){
        try {
            $ter = $this->couchsag->get($terrainDocName);
        } catch (Exception $e) {
        };
        if (!$ter) {
            $data = array("_id" => $terrainDocName, "docType" => "terrain", "terrain" => $wargameDoc->terrain);
            $this->couchsag->create($data);
        } else {

            $data = array("_id" => $terrainDocName, "docType" => "terrain", "terrain" => $wargameDoc->terrain);
            /* totally throw the old one away */

            $this->couchsag->delete($terrainDocName, $ter->_rev);
            $this->couchsag->create($data);
        }
    }

    public function leaveWargame($user, $wargame)
    {
        return;
        echo "leave";
        $doc = $this->couchsag->get($wargame);
        if (!$doc) {
            return;
        }
        if (!$doc->wargame) {
            return;
        }
        echo "iswargema";
        $newUsers = array();
        if (!is_array($doc->wargame->players)) {
            echo "not is array<br>";
            $doc->wargame->players = array("", "", "");
        }
        if (in_array($user, $doc->wargame->players)) {
            echo "in array<br>";
            foreach ($doc->wargame->players as $i => $aUser) {
                echo $aUser . " <br>";
                if ($user == $aUser) {
                    $doc->wargame->players[i] = "";
                }
            }
        }
        $this->couchsag->update($doc->_id, $doc);
    }

    public function initDoc()
    {
        $views = new StdClass();
        $views->getGamesImIn = new StdClass;
        $views->getGamesImIn->map = "function(doc){/*comment */
            if(doc.docType == 'wargame' && doc.playerStatus == 'multi'){
                for(var i in doc.wargame.players){
                    if(doc.wargame.players[i] == '' || doc.wargame.players[i] == doc.createUser){
                        continue;
                    }
                    var gameName = doc.gameName;
                    if(doc.wargame.arg){
                        gameName += '-'+doc.wargame.arg;
                    }

                    emit([doc.wargame.players[i],doc.createUser, doc.name, gameName, doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players,doc.wargame.victory.gameOver]);
                }
            }
        }";
        $views->publicGames = new StdClass;
        $views->publicGames->map = "function(doc){/*comment */
            if(doc.docType == 'wargame' &&  doc.visibility == 'public'){
                    var gameName = doc.gameName;
                    if(doc.wargame.arg){
                        gameName += '-'+doc.wargame.arg;
                    }

                    emit([doc.createUser, doc.name, gameName, doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players]);

            }
        }";
        $views->getLobbies = new StdClass;
        $views->getLobbies->map = "function(doc){
            if(doc.docType == 'wargame'){
               var gameName = doc.gameName;
	        	if(doc.wargame.arg){
		            gameName += '-'+doc.wargame.arg;
                }
                emit([doc.createUser, doc.playerStatus, gameName,doc.name,doc.wargame.gameRules.attackingForceId, doc._id, doc.visibility],[doc.gameName,doc.createDate,doc.wargame.players,doc.wargame.mapData.mapUrl,doc.wargame.victory.gameOver,doc.wargame.gameRules.turn, doc.wargame.gameRules.maxTurn]);
            }}";
//        $views->getAvailGames = new StdClass;
//        $views->getAvailGames->map = "function(doc){if(doc.docType == 'gamesAvail'){if(doc.games){for(var i in doc.games){emit(doc.games[i],doc.games[i]);}}}}";
        $filters = new StdClass();
        $filters->namefind = "function(doc,req){if(!req.query.name){return false;} var names = req.query.name;names = names.split(',');for(var i = 0;i < names.length;i++){if(doc._id == names[i]){return true;}}return false;}";
        $filters->lobbyChanges = <<<LobbyChanges
        function(doc,req){
            if(doc._deleted === true){
                return true;
            }
            if(!req.query.name){
                return false;
            }
            var player = req.query.name;
            if(doc.docType != "wargame"){
                return false;
            }
            if(doc.playerStatus == "created" && doc.createUser == req.query.name){
                return true;
            }
            if(typeof(doc.wargame) == 'undefined'){
                return false;
            }

            if(doc.visibility !== "public" && doc.createUser != player && doc.wargame.players[1] != player && doc.wargame.players[2] != player){
                return false;
            }
            if(doc.wargame.gameRules.turnChange){
                return true;
            }
            return false;
       }
LobbyChanges;

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

        $views->allGames = new stdClass();
        $views->allGames->map = "function(doc){
            if(doc.docType == 'wargame'){
               var gameName = doc.gameName;
	        	if(doc.wargame && doc.wargame.arg){
		            gameName += '-'+doc.wargame.arg;
                }
                emit([doc.createUser,gameName,doc.name,doc.playerStatus,doc.createDate, doc._id],[doc.gameName,doc.createDate]);
            }}";
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
        $wargame = new StdClass();
        $wargame->map = <<<HEREMAP
        function(doc) {
            if(doc.docType == 'game' || doc.docType == 'wargame'){
                var ret = 0;

                if(doc.users ){
                    ret = doc.users.length;
                }
                emit([doc.docType,doc._id],ret);
            }
        }
HEREMAP;
        $wargame->reduce = <<<HERE
function(keys,values){return sum(values);}
HERE;
        $update = <<<HEREUPDATE
function(doc,req){
    doc.chats.push(req.query.chat);
    doc.chats_index++;
    doc.chitty = "ssssss";
    return [doc,"done"];
}
HEREUPDATE;


        $updates = new StdClass();

//        $updates->addchat = $update;
//        $views->wargame = $wargame;
//        $views->userByEmail = $users;
//        $views->userById = $userById;
//        $views->userByUsername = $userByUsername;

//        $this->couchsag->sag->setDatabase('users');


        $data = array("_id" => "_design/newFilter", "views" => $views, "filters" => $filters, "updates" => $updates);
        try {
            $doc = $this->couchsag->get("_design/newFilter");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/newFilter\n";
            $deldoc = $this->couchsag->delete($doc->_id, $doc->_rev);
            if ($deldoc->body) {
                echo "Deleted\n";
            }
        }
        try {
            $this->couchsag->create($data);
        } catch (Exception $e) {
        }
    }

    public function createWargame($name)
    {
        date_default_timezone_set("America/New_York");
//        $data = array('docType' => "wargame", "_id" => $name, "name" => $name, "chats" => array(),"createDate"=>date("r"),"createUser"=>$this->session->userdata("user"),"playerStatus"=>"created");
//        $data = array('docType' => "wargame", "name" => $name, "chats" => array(),"createDate"=>date("r"),"createUser"=>$this->session->userdata("user"),"playerStatus"=>"created");
        try {
            $data = new stdClass();
            $data->docType = "wargame";
            $data->name = $name;
            $data->chats = array();
            $data->createDate = date("r");
            $data->createUser = $this->session->userdata("user");
            $data->playerStatus = "created";
            $ret = $this->couchsag->create($data);
        } catch (Exception $e) {
            ;
            return $e->getMessage();
        }
        return $ret;
    }

    public function addChat($chat, $user, $wargame)
    {
        $doc = $this->couchsag->get($wargame);
        if (!is_array($doc->chats))
            $doc->chats = array();

        $doc->chats[] = $user . ": " . $chat;
        $success = $this->couchsag->update($doc->_id, $doc);
        return $success;
    }

    public function getDoc($wargame)
    {
        try {
            $doc = $this->couchsag->get($wargame);
        } catch (Exception $e) {
            return false;
        }
        return $doc;
    }

    public function setDoc($doc)
    {
        $success = $this->couchsag->update($doc->_id, $doc);
        return $success;
    }

    public function getLobbyChanges($user, $last_seq = 0, $chatsIndex = 0)
    {
        do {
            $retry = false;
            try {
                if ($last_seq) {
                    $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/lobbyChanges&name=$user");
                } else {
                    $seq = $this->couchsag->get("/_changes");
                }
            } catch (Exception $e) {
                $retry = true;
            }
        } while ($retry || $seq->last_seq <= $last_seq);
        return $seq;
    }

    public function getChanges($wargame, $last_seq = 0, $chatsIndex = 0, $user = 'observer')
    {
        global $mode_name, $phase_name;
        $time = false;
        $timeBranch = false;
        $timeFork = false;
        if ($_GET['timeTravel']) {
            $time = $_GET['timeTravel'];
            if ($_GET['branch']) {
                $timeBranch = true;
            }
            if ($_GET['fork']) {
                $timeFork = true;
            }
        }

        /*
         * TODO: make this have a trip switch so it won't spin out of control if the socket is down
         */
        if (!$time) {
            do {
                $retry = false;
                try {
                    if ($last_seq) {
                        $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/namefind&name=$wargame");
                    } else {
                        $seq = $this->couchsag->get("/_changes");
                    }
                } catch (Exception $e) {
                    $retry = true;
                }
            } while ($retry || $seq->last_seq <= $last_seq);
            $last_seq = $seq->last_seq;
        }


//        $time = $this->session->userdata("time");
//        $time = $last_seq;
        $match = "";
//        $time = 65;
//        $time = false;
        $revision = "";
        if ($time) {
            $doc = $this->couchsag->get($wargame . "?revs_info=true");
            $revs = $doc->_revs_info;
            foreach ($revs as $k => $v) {
                if (preg_match("/^$time-/", $v->rev)) {
                    $revision = "?rev=" . $v->rev;
                    $currentRev = $doc->_rev;
                    break;
                }
            }

        }
//        file_put_contents("/tmp/perflog","\nGetting ".microtime(),FILE_APPEND);
        $doc = $this->couchsag->get($wargame . $revision);
        if ($timeBranch) {
            $doc->_rev = $currentRev;
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $this->couchsag->update($doc->_id, $doc);
            $last_seq = 0;
        }

        if($timeFork){
            $doc->wargame->gameRules->flashMessages[] = "Time Travel to click $time";
            $doc->forkedFrom = $doc->_id;
            unset($doc->_id);
            unset($doc->_rev);
            $doc->name .= "Clone $time";
            $ret = $this->couchsag->create($doc);
            $last_seq = 0;

            if (is_object($ret) === true) {
                $wargame = $ret->body->id;
                $this->session->set_userdata(array("wargame" => $wargame));
            }
            $doc = $this->couchsag->get($wargame);

        }

        return Battle::transformChanges($doc, $last_seq, $user);
    }

}