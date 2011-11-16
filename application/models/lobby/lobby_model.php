<?php

class Lobby_model extends CI_Model
{

    public function enterLobby($user, $lobby)
    {
        $doc = $this->couchsag->get($lobby);
        if (!is_array($doc->users)) {
            $doc->users = array();
        }
        if (!in_array($user, $doc->users)) {
            $doc->users[] = $user;
        }
        $this->couchsag->update($lobby, $doc);

    }


    public function leaveLobby($user, $lobby)
    {
        $doc = $this->couchsag->get($lobby);
        $newUsers = array();
        if (in_array($user, $doc->users)) {
            foreach ($doc->users as $aUser) {
                if ($user != $aUser) {
                    $newUsers[] = $aUser;
                }
            }
        }
        $doc->users = $newUsers;
        $this->couchsag->update($lobby, $doc);
    }

    public function addChat($chat, $user, $lobby)
    {
        $doc = $this->couchsag->get($lobby);
        if (!is_array($doc->chats))
            $doc->chats = array();

        $doc->chats[] = $user . ": " . $chat;
        $success = $this->couchsag->update($doc->_id, $doc);
        return $success;
    }

    public function getChanges($lobby, $last_seq = '', $chatsIndex = 0){
        if ($last_seq) {
            $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll&filter=namefilter/namefind&name=$lobby");
        } else {
            $seq = $this->couchsag->get("/_changes");
        }
        $last_seq = $seq->last_seq;

        $doc = $this->couchsag->get($lobby);
        $games = $doc->games;
        $chats = array_slice($doc->chats, $chatsIndex);
        $chatsIndex = count($doc->chats);
        $users = $doc->users;
        $clock = $doc->clock;
        return compact('chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock');
    }

}