<?php

class Wargame_model extends CI_Model
{

    public function enterHotseat($wargame){
        $user = $this->session->userdata("user");
        $doc = $this->couchsag->get($wargame);
        if($user != $doc->createUser){
            return false;
        }
        $doc->playerStatus = "hot seat";
        foreach($doc->wargame->players as $k => $v){
            if($v != $user){
                $doc->wargame->players[$k] = "";
            }
        }
//        $doc->wargame->players[1] = $user;
        $this->couchsag->update($doc->_id, $doc);
        return true;
        }
    public function enterMulti($wargame,$user, $other){
        $doc = $this->couchsag->get($wargame);
//        var_dump($doc->wargame->players);
        $doc->playerStatus = "multi";
        $doc->wargame->players = array("",$user,$other);
        $this->couchsag->update($doc->_id, $doc);
    }
    public function enterWargame($user, $wargame)
    {

        $doc = $this->couchsag->get($wargame);
        if($doc->playerStatus == "created"){
            return;
        }
        if($doc->playerStatus == "multi"){
            return;
        }
        if($doc->playerStatus == "hot seat"){
            if($user == $doc->createUser){
                $player = $doc->wargame->gameRules->attackingForceId;
            }else{
                $player = 0;
            }
        }
        if (!is_array($doc->wargame->players)) {
            $doc->wargame->players = array("","","");
        }
        if (!in_array($user, $doc->wargame->players)) {
            $doc->wargame->players[$player] = $user;
        }else{
            $index = array_search($user, $doc->wargame->players);
            $doc->wargame->players[$index] = "";
            $doc->wargame->players[$player] = $user;
        }
        $this->couchsag->update($doc->_id, $doc);

    }


    public function leaveWargame($user, $wargame)
    {
        return;
        echo "leave";
        $doc = $this->couchsag->get($wargame);
        if(!$doc){
            return;
        }
        if(!$doc->wargame){
            return;
        }
        echo "iswargema";
        $newUsers = array();
        if(!is_array($doc->wargame->players)){
            echo "not is array<br>";
            $doc->wargame->players = array("","","");
        }
        if (in_array($user, $doc->wargame->players)) {
            echo "in array<br>";
            foreach ($doc->wargame->players as $i => $aUser) {
                echo $aUser." <br>";
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
        $views->getGamesImIn->map = "function(doc){if(doc.docType == 'wargame' && doc.playerStatus == 'multi'){for(var i in doc.wargame.players){if(doc.wargame.players[i] == '' || doc.wargame.players[i] == doc.createUser){continue;}emit([doc.wargame.players[i],doc.createUser, doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players]);}}}";
        $views->getLobbies = new StdClass;
        $views->getLobbies->map = "function(doc){if(doc.docType == 'wargame'){emit([doc.createUser,doc.gameName,doc.playerStatus, doc.wargame.gameRules.attackingForceId, doc._id],[doc.gameName,doc.createDate,doc.wargame.players]);}}";
        $views->getAvailGames = new StdClass;
        $views->getAvailGames->map = "function(doc){if(doc.docType == 'gamesAvail'){if(doc.games){for(var i in doc.games){emit(doc.games[i],doc.games[i]);}}}}";
        $filters = new StdClass();
        $filters->namefind = "function(doc,req){if(!req.query.name){return false;} var names = req.query.name;names = names.split(',');for(var i = 0;i < names.length;i++){if(doc._id == names[i]){return true;}}return false;}";
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

        $updates->addchat = $update;
        $views->wargame = $wargame;
        $views->userByEmail = $users;
        $views->userById = $userById;
        $views->userByUsername = $userByUsername;
        var_dump($wargame);echo "HEE";
        $data = array("_id" => "_design/newFilter", "views" => $views, "filters" => $filters, "updates"=> $updates);
        try{
        $doc = $this->couchsag->get("_design/newFilter");
        }catch(Exception $e){};
        if($doc){
            var_dump($doc);
echo "HI";
            var_dump($this->couchsag->delete($doc->_id,$doc->_rev));
            echo "IH";
        }
        echo "create";
        var_dump($data);
        try{
        echo $this->couchsag->create($data);
        }catch(Exception $e){echo "<pre> EXC";var_dump($e);}
        echo "created";
    }

    public function createWargame($name)
    {
        date_default_timezone_set("America/New_York");

        $data = array('docType' => "wargame", "_id" => $name, "name" => $name, "chats" => array(),"createDate"=>date("r"),"createUser"=>$this->session->userdata("user"),"playerStatus"=>"created");
        $this->couchsag->create($data);
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
        $doc = $this->couchsag->get($wargame);
        return $doc;
    }
    public function setDoc($doc)
    {
        $success = $this->couchsag->update($doc->_id, $doc);
        return $success;
    }


    public function getChanges($wargame, $last_seq = 0, $chatsIndex = 0,$user = 'observer'){
        global $mode_name, $phase_name;

        /*
         * TODO: make this have a trip switch so it won't spin out of control if the socket is down
         */
        do{
            $retry = false;
            try{
                if ($last_seq) {
                    $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/namefind&name=$wargame");
                } else {
                    $seq = $this->couchsag->get("/_changes");
                }
            }catch(Exception $e){$retry = true;}
        }while($retry || $seq->last_seq <= $last_seq);
        $last_seq = $seq->last_seq;

        $doc = $this->couchsag->get($wargame);
        $games = $doc->games;
        $chats = array_slice($doc->chats, $chatsIndex);
        $chatsIndex = count($doc->chats);
        $users = $doc->users;
        $clock = $doc->clock;
        $players = $doc->wargame->players;
        $player = array_search($user,$players);
        if($player === false){
            $player = 0;
        }
        $force = $doc->wargame->force;
        $wargame = $doc->wargame;
        $gameName = $doc->gameName;

        Battle::loadGame($gameName);
//Battle::getHeader();
        if(isset($doc->wargame->mapViewer)){
        $playerData = $doc->wargame->mapViewer[$player];
        }else{
            $playerData = $doc->wargame->mapData[$player];
        }
        $mapGrid = new MapGrid($playerData);
        $mapUnits = array();
        $moveRules = $doc->wargame->moveRules;
        $combatRules = $doc->wargame->combatRules;
        $moveRules->index = $combatRules->index;
        $display = $doc->wargame->display;
        $units = $force->units;
        $storm = $combatRules->storm;
        $attackingId = $doc->wargame->gameRules->attackingForceId;
        foreach($units as $unit){
            if(is_object($unit->hexagon)){
//                $unit->hexagon->parent = $unit->parent;
            }else{
                $unit->hexagon = new Hexagon($unit->hexagon);
              }
//            $unit->hexagon->parent = $unit->parent;
            $mapGrid->setHexagonXY( $unit->hexagon->x, $unit->hexagon->y);
            $mapUnit = new StdClass();
            $mapUnit->isReduced = $unit->isReduced;
            $mapUnit->x = $mapGrid->getPixelX();
            $mapUnit->y = $mapGrid->getPixelY();
            $mapUnit->parent = $unit->hexagon->parent;
            $mapUnit->moveAmountUsed = $unit->moveAmountUsed;
            $mapUnit->maxMove = $unit->maxMove;
            $mapUnit->strength = $unit->strength ;
            $mapUnits[] = $mapUnit;
        }
        foreach($units as $i => $unit){
            $u = new StdClass();
            $u->status = $unit->status;
            $u->moveAmountUsed = $unit->moveAmountUsed;
            $u->maxMove = $unit->maxMove;
            $u->forceId = $unit->forceId;
            $units[$i] = $u;
        }
        if($moveRules->moves){
            foreach($moveRules->moves as $k => $move){
                $hex = new Hexagon($k);
                $mapGrid->setHexagonXY( $hex->getX(), $hex->getY());

                $moveRules->moves->{$k}->pixX = $mapGrid->getPixelX();
                $moveRules->moves->{$k}->pixY = $mapGrid->getPixelY();
            }
            if(false && $moveRules->path){
                foreach($moveRules->path as $hexName){
                    $hex = new Hexagon($hexName);
                    $mapGrid->setHexagonXY($hex->x,$hex->y);

                    $path = new stdClass();
                    $path->pixX = $mapGrid->getPixelX();
                    $path->pixY = $mapGrid->getPixelY();
                    $moveRules->hexPath[] = $path;
                }
            }
        }
        $force->units = $units;
        $gameRules = $wargame->gameRules;
        $gameRules->display = $display;
        $gameRules->phase_name = $phase_name;
        $gameRules->mode_name = $mode_name;
        $gameRules->exchangeAmount = $force->exchangeAmount;
        $newSpecialHexes = new stdClass();
        if($doc->wargame->mapData->specialHexes){
            $specialHexes = $doc->wargame->mapData->specialHexes;
            foreach($specialHexes as $k => $v){
                $hex = new Hexagon($k);
                $mapGrid->setHexagonXY($hex->x,$hex->y);

                $path = new stdClass();
                $newSpecialHexes->{"x".$mapGrid->getPixelX()."y".$mapGrid->getPixelY()} = $v;
            }
        }
        $specialHexes = $newSpecialHexes;
        $newSpecialHexesChanges = new stdClass();
        if($doc->wargame->mapData->specialHexesChanges){
            $specialHexesChanges = $doc->wargame->mapData->specialHexesChanges;
            foreach($specialHexesChanges as $k => $v){
                $hex = new Hexagon($k);
                $mapGrid->setHexagonXY($hex->x,$hex->y);

                $path = new stdClass();
                $newSpecialHexesChanges->{"x".$mapGrid->getPixelX()."y".$mapGrid->getPixelY()} = $v;
            }
        }
        $flashMessages = $gameRules->flashMessages;
        if(count($flashMessages)){

        }
//        $flashMessages = array("Victory","Is","Mine");
        $specialHexesChanges = $newSpecialHexesChanges;
        $gameRules->playerStatus = $doc->playerStatus;
        $clock = "The turn is ".$gameRules->turn.". The Phase is ". $phase_name[$gameRules->phase].". The mode is ". $mode_name[$gameRules->mode];
        return compact("flashMessages","specialHexes","specialHexesChanges","combatRules",'force','seq', 'chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock', 'mapUnits','moveRules','gameRules');
    }

}