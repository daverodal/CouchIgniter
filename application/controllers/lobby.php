<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* lobby.php */

class Lobby extends CI_Controller
{
    function index($lobby = "MainLobby")
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/lobby/login/");
        }
        redirect("/lobby/chat");
        $this->load->view("lobby/lobbyView",compact("lobby"));

    }

    function chat()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/lobby/login/");
        }
        $lobby = $this->session->userdata("lobby");
        $seq = $this->couchsag->get("/_design/lobbies/_view/getLobbies");
        foreach($seq->rows as $row){
            $lobbies[] =  array("name"=>$row->id);
        }
        echo "Welcome $user";
        //echo $this->twig->render("lobby/lobbyView.php",compact("lobby","lobbies"));
        $this->parser->parse("lobby/lobbyView",compact("lobby","lobbies"));

    }

    function logout()
    {
        $user = $this->session->userdata("user");
        $lobby = $this->session->userdata("lobby");
        $this->load->model("lobby/lobby_model");
        $this->lobby_model->leaveLobby($user,$lobby);
        $this->session->sess_destroy();
        redirect("/lobby/");
    }

    function login()
    {
        echo "this";
        $user = $this->session->userdata("user");
        echo $user;echo "ii";
        $data = $this->input->post();
        echo $data;
        if (!$user && $data) {
            $user = $data['name'];
            $this->session->set_userdata(array("user" => $user));
            $this->session->set_userdata(array("lobby" => "MainLobby"));
            $this->load->model('lobby/lobby_model');
            $this->lobby_model->enterLobby($user, "MainLobby");
            redirect("/lobby/");
        }
        $this->load->view("login");

    }

    function changeLobby($newLobby = "MainLobby"){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/lobby/login/");
        }
        $lobby = $this->session->userdata("lobby");

        $this->load->model("lobby/lobby_model");
        $this->lobby_model->leaveLobby($user,$lobby);
        $this->lobby_model->enterLobby($user,$newLobby);

        $this->session->set_userdata(array("lobby" => $newLobby));
        redirect("/lobby/");
    }

    public function fetch($lobby = "MainLobby", $last_seq = '')
    {
        header("Content-Type: application/json");
        $this->load->model("lobby/lobby_model");
        $chatsIndex = $this->input->post('chatsIndex');
        $ret = $this->lobby_model->getChanges($lobby, $last_seq,$chatsIndex);
        echo json_encode($ret);
    }

    public function add($lobby = "MainLobby")
    {
        $user = $this->session->userdata("user");
        $chat = $this->input->post('chat');
        $this->load->model("lobby/lobby_model");
        $this->lobby_model->addChat($chat,$user,$lobby);
        return compact('success');
    }

    public function clock()
    {

        while (true) {
            $date = date("h:i:s A");
            echo "HI";
            $doc = $this->couchsag->get("MainLobby");
            $doc->clock = $date;
            $success = $this->couchsag->update($doc->_id, $doc);
            sleep(1);
        }
    }
}
