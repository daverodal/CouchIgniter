<?php

class Wargame_model extends CI_Model
{

    public function enterWargame($user, $wargame, $player = 0)
    {
        $doc = $this->couchsag->get($wargame);
        var_dump($doc->wargame->playerData);//die("here");
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
        var_dump($doc->wargame->playerData);
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
        $views->getLobbies = new StdClass;
        $views->getLobbies->map = "function(doc){if(doc.docType == 'wargame'){emit(doc._id,doc.gameName);}}";
        $views->getAvailGames = new StdClass;
        $views->getAvailGames->map = "function(doc){if(doc.docType == 'gamesAvail'){if(doc.games){for(var i in doc.games){emit(doc.games[i],doc.games[i]);}}}}";
        $filters = new StdClass();
        $filters->namefind = "function(doc,req){if(!req.query.name){return false;} var names = req.query.name;names = names.split(',');for(var i = 0;i < names.length;i++){if(doc._id == names[i]){return true;}}return false;}";
        $users = new StdClass();
        $users->map = <<<aHEREMAP
        function(doc) {
            if(doc.docType == 'game' || doc.docType == 'wargame'){
                var ret = 0;

                if(doc.users){
                    for(var i = 0;i < doc.users.length;i++){
                    emit([doc.docType,doc._id,doc.users[i]],1);
                    }
                    if(doc.users.length == 0){
                        emit([doc.docType,doc._id,null],0);
                    }
                }
            }
        }
aHEREMAP;
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
        $views->users = $users;
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
        $this->couchsag->create($data);
    }

    public function createWargame($name)
    {
        $data = array('docType' => "wargame", "_id" => $name, "name" => $name, "chats" => array());
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


    public function getChanges($wargame, $last_seq = '', $chatsIndex = 0,$user = 'observer'){
        global $mode_name, $phase_name;

        do{
            if ($last_seq) {
                $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=newFilter/namefind&name=$wargame");
            } else {
                $seq = $this->couchsag->get("/_changes");
            }
        }while(count($seq->results) == 0);
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
        $playerData = $doc->wargame->mapData[$player];
        $mapGrid = new MapGrid($playerData);
        $mapUnits = array();
        $moveRules = $doc->wargame->moveRules;
        $combatRules = $doc->wargame->combatRules;
        $moveRules->index = $combatRules->index;
        $units = $force->units;
        $storm = $combatRules->storm;
        $attackingId = $doc->wargame->gameRules->attackingForceId;
        foreach($units as $unit){
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
        $force->units = $units;
        $gameRules = $wargame->gameRules;
        $gameRules->phase_name = $phase_name;
        $gameRules->mode_name = $mode_name;
        $gameRules->exchangeAmount = $force->exchangeAmount;
        $clock = "The turn is ".$gameRules->turn.". The Phase is ". $phase_name[$gameRules->phase].". The mode is ". $mode_name[$gameRules->mode];
        return compact("combatRules",'force','seq', 'chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock', 'mapUnits','moveRules','gameRules');
    }

}