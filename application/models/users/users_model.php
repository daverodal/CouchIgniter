<?php
/**
 * Created by JetBrains PhpStorm.
 * User: markarianr
 * Date: 1/29/13
 * Time: 3:49 PM
 * To change this template use File | Settings | File Templates.
 */
class Users_model extends CI_Model
{

    public function getUsersByUsername(){
        $seq = $this->couchsag->get("/_design/newFilter/_view/userByUsername");
        return $seq->rows;
    }

    public function getUsersByEmail(){
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            return $usersDoc->userByEmail;
        }

    }

    public function getUserByEmail($email){
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $user = $usersDoc->userByEmail->$email;

            return $user;
        }

    }

    public function initDoc(){
        $data = array("_id" => "users", "docType" => "users", "userByEmail" => new stdClass(), "userById"=> new stdClass(), "userId"=> 1);
        $this->couchsag->create($data);
        return;

        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            $email = "dave.rodal@gmail.com";
            $user = new stdClass();
            $user->id = $usersDoc->userId++;
            $user->password = "2havefun";
            var_dump($usersDoc->userByEmail);
            $usersDoc->userByEmail->$email = $user;
            echo "<pre>";
            var_dump($usersDoc);
            $this->couchsag->update($usersDoc->_id, $usersDoc);
            return $user;
        }

    }
    public function addUser($email, $password, $username){
        $strikes = 0;
        while($strikes < 3){
            $usersDoc = $this->couchsag->get("users");
    //        $usersDoc->_rev = "";
            if($usersDoc->docType == "users"){
                $users = $usersDoc->userByEmail;
                if($users->$email){
                    return "Email already used: $email";
                }

                foreach($users as  $user){
                    if($user->username == $username){
                        return "Username already used: $username";
                    }
                }
                $user = new stdClass();
                $user->id = $usersDoc->userId++;
                $user->username = $username;
                $user->password = $password;
                $usersDoc->userByEmail->$email = $user;
                $ret = $this->couchsag->update($usersDoc->_id, $usersDoc);
                if($ret && $ret->ok){
                    return false;
                }
            }
        }
        return "Cannot save $strikes strikes";
    }

    public function isValidLogin($email, $password){
        $usersDoc = $this->couchsag->get("users");
        if($usersDoc->docType == "users"){
            if(isset($usersDoc->userByEmail->$email))
            {
                if($usersDoc->userByEmail->$email->password == $password){
                    return true;
                }
            }
        }
        return false;
    }
}