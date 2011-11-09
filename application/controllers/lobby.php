<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Lobby extends CI_Controller
{
    function index()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/lobby/login/");
        }
        echo "Welcome $user";
        $this->load->view("lobby/lobbyView");

    }

    function logout()
    {
        $user = $this->session->userdata("user");
        $doc = $this->couchsag->get("MainLobby");
        $newUsers = array();
        if (in_array($user, $doc->users)) {
            foreach ($doc->users as $aUser) {
                if ($user != $aUser) {
                    $newUsers[] = $aUser;
                }
            }
        }
        $doc->users = $newUsers;
        $this->couchsag->update("MainLobby", $doc);
        $user = $this->session->sess_destroy();
        redirect("/lobby/");
    }

    function login()
    {
        echo APPPATH . "<br>";
        echo base_url();
        $user = $this->session->userdata("user");
        $data = $this->input->post();
        if (!$user && $data) {
            $user = $data['name'];
            $this->session->set_userdata(array("user" => $user));
            $doc = $this->couchsag->get("MainLobby");
            if (!is_array($doc->users)) {
                $doc->users = array();
            }
            if (!in_array($user, $doc->users)) {
                $doc->users[] = $user;
            }
            $this->couchsag->update("MainLobby", $doc);
            redirect("/lobby/");
        }
        $this->load->view("login");

    }

    public function fetch($last_seq = '')
    {
        header("Content-Type: application/json");
        if ($last_seq) {
            $seq = $this->couchsag->get("/_changes?since=$last_seq&feed=longpoll");

        } else {
            $seq = $this->couchsag->get("/_changes");
        }
        $last_seq = $seq->last_seq;
        $data = $this->input->post();
        $chatsIndex = 0;
        if ($data["chatsIndex"])
            $chatsIndex = $data["chatsIndex"];
        $doc = $this->couchsag->get("MainLobby");
        $games = $doc->games;
        $chats = array_slice($doc->chats, $chatsIndex);
        $chatsIndex = count($doc->chats);
        $users = $doc->users;
        $clock = $doc->clock;
        echo json_encode(compact('chats', 'chatsIndex', 'last_seq', 'users', 'games', 'clock'));
    }

    public function add($chat)
    {
        $user = $this->session->userdata("user");
        $doc = $this->couchsag->get("MainLobby");
        if ($_POST) {
            if (!is_array($doc->chats))
                $doc->chats = array();

            $doc->chats[] = $user . ": " . $_POST["chat"];
            $success = $this->couchsag->update($doc->_id, $doc);
        }
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
