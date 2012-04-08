<?php

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
        var_dump($doc);
        $success = $this->couchsag->update($doc->_id, $doc);
        var_dump($success);
        return $success;
    }


    public function getChanges($wargame, $last_seq = '', $chatsIndex = 0){
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
        return compact('seq', 'chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock');
    }

}