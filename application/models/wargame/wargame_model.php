<?php
require_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleForAllenCreek.php");

class Wargame_model extends CI_Model
{

    public function enterWargame($user, $wargame)
    {
        $doc = $this->couchsag->get($wargame);
        if (!is_array($doc->users)) {
            $doc->users = array();
        }
        if (!in_array($user, $doc->users)) {
            $doc->users[] = $user;
        }
        $this->couchsag->update($wargame, $doc);

    }


    public function leaveWargame($user, $wargame)
    {
        $doc = $this->couchsag->get($wargame);
        if(!$doc)
            return;
        $newUsers = array();
        if (in_array($user, $doc->users)) {
            foreach ($doc->users as $aUser) {
                if ($user != $aUser) {
                    $newUsers[] = $aUser;
                }
            }
        }
        $doc->users = $newUsers;
        $this->couchsag->update($wargame, $doc);
    }

    public function initDoc()
    {
        $views = new StdClass();
        $views->getLobbies = new StdClass;
        $views->getLobbies->map = "function(doc){if(doc.docType == 'wargame'){emit(doc._id,doc._id);}}";
        $filters = new StdClass();
        $filters->namefind = "function(doc){if(doc.docType == 'wargame'){emit(doc._id,doc._id);}}";
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
        $data = array('docType' => "wargame", "_id" => $name, "name" => $name);
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


    public function getChanges($wargame, $last_seq = '', $chatsIndex = 0){
        // mode names
        $mode_name = array();
        $mode_name[ 1] = "moving mode";
        $mode_name[ 2] = "moving mode";
        $mode_name[ 3] = "combat setup mode";
        $mode_name[ 4] = "combat resolution";
        $mode_name[ 5] = "fire combat setup mode";
        $mode_name[ 6] = "fire combat resolution";
        $mode_name[ 7] = "retreating mode";
        $mode_name[ 8] = "retreating mode";
        $mode_name[ 9] = "retreating mode";
        $mode_name[10] = "retreating mode";
        $mode_name[11] = "advancing mode";
        $mode_name[12] = "advancing mode";
        $mode_name[13] = "select units to delete";
        $mode_name[14] = "deleting unit";
        $mode_name[15] = "checking combat";
        $mode_name[16] = "game over";

        $phase_name = array();
        $phase_name[1] = "Blue Move";
        $phase_name[2] = "Blue Combat";
        $phase_name[3] = "Blue Fire Combat";
        $phase_name[4] = "Red Move";
        $phase_name[5] = "Red Combat";
        $phase_name[6] = "Red Fire Combat";
        $phase_name[7] = "Victory";

        do{
            if ($last_seq) {
                $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=namefilter/namefind&name=$wargame");
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
        $units = $doc->wargame->force->units;
        $wargame = $doc->wargame;

        $mapGrid = new MapGrid($doc->wargame->mapData);
        $mapUnits = array();
        $moveRules = $doc->wargame->moveRules;
        foreach($units as $unit){
            $mapGrid->setHexagonXY( $unit->hexagon->x, $unit->hexagon->y);
            $mapUnit = new StdClass();
            $mapUnit->x = $mapGrid->getPixelX();
            $mapUnit->y = $mapGrid->getPixelY();
            $mapUnits[] = $mapUnit;
        }
        $gameRules = $wargame->gameRules;
        $clock = "The turn is ".$gameRules->turn.". The Phase is ". $phase_name[$gameRules->phase].". The mode is ". $mode_name[$gameRules->mode];
        return compact('seq', 'chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock', 'mapUnits','moveRules');
    }

}