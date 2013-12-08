<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 1/29/13
 * Time: 2:53 PM
 * To change this template use File | Settings | File Templates.
 * renamed to users.php
 * added this here
 */
class Users extends CI_Controller
{
    function index(){
        $this->_isLoggedIn();
        $this->load->model('users/users_model');
        $users = $this->users_model->getUsersByUsername();
        $this->load->view('users/users_view',compact("users"));
//        var_dump($this->users_model->getUsersByEmail());
    }

    function userAdded(){
        $this->load->view('users/userAdded');
    }
    function addUser()
    {
        if($this->_anyUser() != 0){
            $this->_isLoggedIn();
        }

//	echo "No";
//	return;
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'Username', 'required|alpha_dash');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]|md5');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
        $this->form_validation->set_message('valid_username', 'Usernames must be letters, numbers, underscore _, dash -, or space , they must also have at least one letter or number in them');
        if ($this->form_validation->run() == FALSE)
        {
            echo "error will";
            $this->load->view('users/addUser',array("save_errors"=>""));
        }
        else
        {
            $this->load->model('users/users_model');
            $err = $this->users_model->addUser($this->input->post('email'),$this->input->post('password'),$this->input->post('username'));
            if($err){
                $this->load->view('users/addUser',array("save_errors"=>$err));

            }else{
                redirect('users/userAdded');
            }
        }
    }

    public function valid_username($username){
        $ret =preg_match("/^[a-zA-Z0-9_\- ]+$/",$username);
        if($ret){
            $ret = preg_match("/[a-zA-Z0-9]/",$username);
            if($ret){
                return true;
            }
        }
        return false;
    }
    public function view(){
     }

    private function _isLoggedIn(){
        $user = $this->session->userdata("user");
        if (!$user || $user != "Markarian") {
            redirect("/wargame/login/");
        }


    }
    private function _anyUser(){
        $this->load->model('users/users_model');
        $users = $this->users_model->getUsersByUsername();
        return(count($users));
    }
    public function initDoc(){
        $this->load->model('users/users_model');
        $this->users_model->initDoc();
    }
    function addGame(){
        $this->_isLoggedIn();
//        var_dump($_GET);
        $this->load->model('users/users_model');
        if($_GET['dir']){
            $this->load->library("battle");
            $info = $this->battle->getInit($_GET['dir']);
            echo "<pre>";
            var_dump($info);
            $games = $this->users_model->addGame($info);
            redirect('users/games');
        }

        $this->load->view('users/addGame_view',compact("games"));
//        $this->load->view('users/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }
    function deleteGame(){
        $this->_isLoggedIn();
        $this->load->model('users/users_model');
        var_dump($_GET);
        $games = $this->users_model->deleteGame($_GET['killGame']);
        redirect('users/games');
//        $this->load->view('users/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }
    function games(){
        $this->_isLoggedIn();
        $this->load->model('users/users_model');
        $games = $this->users_model->getAvailGames();
        $this->load->view('users/games_view',compact("games"));
//        var_dump($this->users_model->getUsersByEmail());
    }
    function logins(){
        $this->load->model('users/users_model');
        $logins = $this->users_model->getLogins();
        $logins = $logins->logins;
        $this->load->view('users/users_logins',compact("logins"));
    }
}
