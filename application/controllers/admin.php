<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
/* wargame.php */


//@include_once("/home/davidrod/webwargaming/BattleForAllenCreek.php");

class Admin extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('users/users_model');
        $user = $this->session->userdata("user");

        if (!$user || $user != "Markarian") {
            redirect("/users/login/");
        }
    }
    public function index(){
        $this->load->view("admin/adminHome");
    }
    public function allGames(){
        $this->load->helper('date');
        $user = $this->session->userdata("user");

        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");
        if(!$wargame){
//            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
            //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
            $seq = $this->couchsag->get("/_design/newFilter/_view/allGames?");
            $lobbies = [];
            date_default_timezone_set("America/New_York");
            $odd = 0;

            foreach($seq->rows as $row){
                $keys = $row->key;
                $creator = array_shift($keys);
                $gameName = array_shift($keys);

                $name = array_shift($keys);
                $gameType = array_shift($keys);
                $id = array_shift($keys);
//               $key = implode($keys,"  ");
                $id = $row->id;
                $dt = new DateTime($row->value[1]);
                $thePlayers = $row->value[2];
                $playerTurn = $thePlayers[$playerTurn];
                $myTurn = "";
                if($playerTurn == $user){
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                }else{
                    $playerTurn .= "'s";
                }
                array_shift($thePlayers);
                $players = implode($thePlayers," ");
                $row->value[1] = "created ".formatDateDiff($dt)." ago";
                $odd ^= 1;

                $lobbies[] =  array("odd"=>$odd ? "odd":"","id"=>$id, "gameName" => $gameName, "name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType, "turn"=>$playerTurn, "players"=>$players,"myTurn"=>$myTurn);
            }
            $seq = $this->couchsag->get("/_design/newFilter/_view/getGamesImIn?");

               $otherGames = array();
            foreach($seq->rows as $row){
                $keys = $row->key;
                $you = array_shift($keys);
                $creator = array_shift($keys);
                $name = array_shift($keys);
                $gameType = array_shift($keys);
                $playerTurn = array_shift($keys);
                $filename = array_shift($keys);
                $id = $row->id;
                $dt = new DateTime($row->value[1]);
                $thePlayers = $row->value[2];
                $playerTurn = $thePlayers[$playerTurn];
                if($playerTurn == $user){
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                }
                array_shift($thePlayers);
                $players = implode($thePlayers," ");
                $row->value[1] = "created ".formatDateDiff($dt)." ago";
                $otherGames[] =  array("name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType, "turn"=>$playerTurn, "players"=>$players,"myTurn"=>$myTurn);
            }
            $myName = $user;

            $this->parser->parse("admin/wargameLobbyView",compact("lobbies","otherGames","myName"));
            return;

        }
    }
    public function fetchLobby( $last_seq = '')
    {
        return;

        $user = $this->session->userdata("user");
        $this->load->helper('date');
        $wargame = urldecode($this->session->userdata("wargame"));


        header("Content-Type: application/json");
        $this->load->model("wargame/wargame_model");


        $lastSeq = $this->wargame_model->getLobbyChanges($user,$last_seq);
        //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;
        foreach($seq->rows as $row){
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameName = array_shift($keys);
            $name = array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
//               $key = implode($keys,"  ");
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";
            if($playerTurn == $user){
                $playerTurn = "Your";
                $myTurn = "myTurn";
            }else{
                $playerTurn .= "'s";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers," ");
            $row->value[1] = "created ".formatDateDiff($dt)." ago";
            $odd ^= 1;
            $lobbies[] =  array("odd"=>$odd ? "odd":"","gameName"=>$gameName, "name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType, "turn"=>$playerTurn, "players"=>$players,"myTurn"=>$myTurn);
        }
        $seq = $this->couchsag->get("/_design/newFilter/_view/getGamesImIn");

        $odd = 0;
        $otherGames = array();
        foreach($seq->rows as $row){
            $keys = $row->key;
            $you = array_shift($keys);
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";
            if($playerTurn == $user){
                $playerTurn = "Your";
                $myTurn = "myTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers," ");
            $row->value[1] = "created ".formatDateDiff($dt)." ago";
            $odd ^= 1;
            $otherGames[] =  array("odd"=>$odd ? "odd":"","name"=>$name, 'date'=>$row->value[1], "id"=>$id, "creator"=>$creator,"gameType"=>$gameType, "turn"=>$playerTurn, "players"=>$players,"myTurn"=>$myTurn);
        }
        $results = $lastSeq->results;
        $last_seq = $lastSeq->last_seq;
        $ret = compact("lobbies","otherGames","last_seq","results");
        echo json_encode($ret);
    }
    function deleteGame($gameName){
        $user = $this->session->userdata("user");
        if($gameName){
            try{

                $doc = $this->couchsag->get($gameName);
                if($user == "Markarian" || $doc->createUser == $user){
                    if($doc && $doc->_id && $doc->_rev){
                        $this->couchsag->delete($doc->_id,$doc->_rev);
                    }
                }
            }catch(Exception $e){}
        }
        redirect("/admin/allGames");
    }


    function games(){
        $this->load->model('users/users_model');
        $games = $this->users_model->getAvailGames(true);
        $this->load->view('admin/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }
    function addGame(){
//        var_dump($_GET);
        $this->load->model('users/users_model');
        if($_GET['dir']){
            $this->load->library("battle");
            $info = $this->battle->getInit($_GET['dir']);

            $games = $this->users_model->addGame($info);
            redirect('admin/games');
        }

        $this->load->view('admin/addGame_view',compact("games"));
//        $this->load->view('users/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }
    function deleteGameType(){
        $this->load->model('users/users_model');
        $games = $this->users_model->deleteGame($_GET['killGame']);
        redirect('admin/games');
//        $this->load->view('users/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }

}