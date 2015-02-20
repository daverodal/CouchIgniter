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
    function login()
    {
        $this->load->model('users/users_model');
        $user = $this->session->userdata("user");
        $data = $this->input->post();

        if (!$user && $data) {
            if ($this->users_model->isValidLogin($data['name'], md5($data['password']))) {
                $user = $this->users_model->getUserByEmail($data['name']);
                $user = $user->username;
                $this->session->set_userdata(array("user" => $user));
                $this->users_model->userLoggedIn($user);
//            $this->session->set_userdata(array("wargame" => "MainWargame"));
//            $this->load->model('wargame/wargame_model');
//            $this->wargame_model->enterWargame($user, "MainWargame");
                redirect("/wargame/play#welcome");
            }
        }
        if($user){
            redirect("/");
        }
        $this->load->view("login");

    }

    function logout()
    {
        $user = $this->session->userdata("user");
        $wargame = $this->session->userdata("wargame");
        $this->session->sess_destroy();
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->leaveWargame($user, $wargame);
        redirect("/wargame/");
    }

    function index()
    {
        $this->_isLoggedIn();
        $this->load->model('users/users_model');
        $users = $this->users_model->getUsersByUsername();
        $this->load->view('users/users_view', compact("users"));
//        var_dump($this->users_model->getUsersByEmail());
    }

    function userAdded()
    {
        $this->load->view('users/userAdded');
    }

    function addUser()
    {
        if ($this->_anyUser() != 0) {
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
        if ($this->form_validation->run() == FALSE) {
            echo "error will";
            $this->load->view('users/addUser', array("save_errors" => ""));
        } else {
            $this->load->model('users/users_model');
            $err = $this->users_model->addUser($this->input->post('email'), $this->input->post('password'), $this->input->post('username'));
            if ($err) {
                $this->load->view('users/addUser', array("save_errors" => $err));

            } else {
                redirect('users/userAdded');
            }
        }
    }

    function changePassword()
    {
        $this->_isRegUser();

//	echo "No";
//	return;
        $this->load->helper(array('form', 'url'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('currPassword', 'Old Password', 'required|md5');
        $this->form_validation->set_rules('password', 'Password', 'required|matches[passconf]|md5');
        $this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
        $this->form_validation->set_message('valid_username', 'Usernames must be letters, numbers, underscore _, dash -, or space , they must also have at least one letter or number in them');
        if ($this->form_validation->run() == FALSE) {
            $this->load->view('users/changePassword', array("save_errors" => ""));
        } else {
            $this->load->model('users/users_model');
            $err = $this->users_model->changePassword($this->input->post('currPassword'), $this->input->post('password'), $this->input->post('username'));
            if ($err) {
                $this->load->view('users/changePassword', array("save_errors" => $err));

            } else {
                redirect('/');
            }
        }
    }

    public function valid_username($username)
    {
        $ret = preg_match("/^[a-zA-Z0-9_\- ]+$/", $username);
        if ($ret) {
            $ret = preg_match("/[a-zA-Z0-9]/", $username);
            if ($ret) {
                return true;
            }
        }
        return false;
    }

    public function view()
    {
    }

    private function _isLoggedIn()
    {
        $user = $this->session->userdata("user");
        if (!$user || $user != "Markarian") {
            redirect("/admin/login/");
        }

    }

    private function _isRegUser()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/admin/login/");
        }

    }

    private function _anyUser()
    {
        $this->load->model('users/users_model');
        $users = $this->users_model->getUsersByUsername();
        return (count($users));
    }

    public function initDoc()
    {
        $this->load->model('users/users_model');
        $this->users_model->initDoc();
    }

    function logins()
    {
        $this->_isLoggedIn();
        $this->load->model('users/users_model');
        $logins = $this->users_model->getLogins();
        $logins = $logins->logins;
        $this->load->view('users/users_logins', compact("logins"));
    }
}
