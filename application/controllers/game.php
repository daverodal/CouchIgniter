<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Game extends CI_Controller
{
    protected $alive = true;

    function kill()
    {
        $doc = $this->couchsag->get("TheGame");
        $newDoc = new stdClass();
        $newDoc->_id = $doc->_id;
        $newDoc->_rev = $doc->_rev;
        $success = $this->couchsag->update($doc->_id, $newDoc);
        echo $success;

    }

    function index()
    {
        $this->load->helper("url");
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/game/login/");
        }
        echo "Welcome $user";
        $this->load->view("game");

    }

    function logout()
    {
        $user = $this->session->userdata("user");
        $this->load->helper("url");
        $doc = $this->couchsag->get("TheGame");
        $newUsers = array();
        if (in_array($user, $doc->users)) {
            foreach ($doc->users as $aUser) {
                if ($user != $aUser) {
                    $newUsers[] = $aUser;
                }
            }
        }
        $doc->users[] = $newUsers;
        $this->couchsag->update("TheGame", $doc);
        $user = $this->session->sess_destroy();
        redirect("/game/");
    }

    function login()
    {
        echo system('ls');
        $this->load->helper("url");
        $user = $this->session->userdata("user");
        $data = $this->input->post();
        if (!$user && $data) {
            $user = $data['name'];
            $this->session->set_userdata(array("user" => $user));
            $doc = $this->couchsag->get("TheGame");
            if (!is_array($doc->users)) {
                $doc->users = array();
            }
            if (!in_array($user, $doc->users)) {
                $doc->users[] = $user;
            }
            $doc->$user->data->army = array();
            $doc->$user->data->gold = 50;
            $doc->$user->data->mines = 5;
            $doc->$user->data->factories = 0;
            $doc->$user->data->startdate = time();
            $this->couchsag->update("TheGame", $doc);

            redirect("/game/");
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
        $doc = $this->couchsag->get("TheGame");
        $user = $this->session->userdata("user");
        foreach ($doc->users as $auser) {
            if ($auser != $user) {
                $myEnemy = $auser;
                break;
            }
        }
        $chats = array_slice($doc->chats, $chatsIndex);
        $chatsIndex = count($doc->chats);
        $userData = $doc->$user->data;
        $gold = $userData->gold;
        $mines = $userData->mines;
        $factories = $userData->factories;
        $army = $userData->army;
        $users = $userData->users;
        $lose = $userData->lose;
        $win = $doc->$myEnemy->data->lose;
        $clock = $doc->clock;
        $building = $userData->building;
        $enemy = $doc->$myEnemy->data->army;
        $battle = array($doc->$user->data->battle, $doc->$myEnemy->data->battle);
        echo json_encode(compact('chats', 'chatsIndex', 'last_seq', 'users', 'army', 'mines', 'factories', 'gold', 'clock', 'building', "enemy", "lose", "battle", "win"));
    }

    public function add($chat)
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/game/login");
        }
        $saved = false;
        echo "HI";
        if ($_POST) {
            $data = $this->input->post();
            while (!$saved) {
                try {
                    $doc = $this->couchsag->get("TheGame");

                    if ($data["army"]) {
                        if ($doc->$user->data->gold >= 100) {
                            $doc->$user->data->gold -= 100;
                            $num = count($doc->$user->data->army);
                            $doc->$user->data->building[] = array("name" => "$num Army", "hp" => 0);
                        }
                    }
                    if ($data["mines"]) {
                        if ($doc->$user->data->gold >= 50) {
                            $doc->$user->data->gold -= 50;
                            $doc->$user->data->mines++;
                        }
                    }
                    if ($data["factories"]) {
                        if ($doc->$user->data->gold >= 50) {
                            $doc->$user->data->gold -= 50;
                            $doc->$user->data->factories++;
                        }
                    }
                    if ($data["chats"]) {
                        $doc->chats[] = $data["chats"];
                    }
                    $success = $this->couchsag->update($doc->_id, $doc);
                    $saved = true;
                } catch (Exception $e) {
                    if ($e->getCode() == 409) continue;
                    $success = $e->getMessage();
                }
            }
        }
        return compact('success');
    }

    public function clock()
    {

        $startdate = time();
        echo "B4";
        $doc = $this->couchsag->get("TheGame");
        var_dump($doc);
        echo "after";
        foreach ($doc->users as $user) {
            $doc->$user->data->lose = false;
            $doc->$user->data->gold = 50;
            $doc->$user->data->mines = 5;
            $doc->$user->data->factories = 1;
            $doc->$user->data->battle = array();
            $doc->$user->data->building = array();
            $doc->$user->data->army = array();
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
            $doc->$user->data->army[] = array("name" => "$num Army", "hp" => 30);
        }
        var_dump($doc);
        $success = $this->couchsag->update($doc->_id, $doc);

        try {
            $loop = 0;
            while ($this->alive) {
                $date = date("H:i:s A");
                echo "getting\n";
                try {
                    $doc = $this->couchsag->get("TheGame");
                } catch (Exceptioin $e) {
                    echo "Getting " + $e->getMessage();
                    echo "Getting " + $e->getCode();
                }
                if ($startdate) {
                    $doc->startdate = $startdate;
                    $startdate = false;
                }
                echo "Got\n";
                $date = time();
                $doc->date = $date;
                $sd = new DateTime("@" . $doc->startdate);
                $cd = new DateTime("@$date");
                $datediff = $cd->diff($sd);
                $doc->clock = $datediff->format("%H:%I:%S");
                foreach ($doc->users as $user) {
                    $doc->$user->data->gold += $doc->$user->data->mines * .33;
                    $factories = $doc->$user->data->factories;
                    $building = array();
                    foreach ($doc->$user->data->building as $k => $unit) {
                        if ($factories > 0) {
                            $unit->hp++;
                            $factories--;
                            if ($unit->hp >= 30) {
                                $doc->$user->data->army[] = $unit;
                            } else {
                                $building[] = $unit;
                            }
                        } else {
                            $building[] = $unit;
                        }
                    }
                    $doc->$user->data->building = $building;
                }
                foreach ($doc->users as $user) {
                    if (count($doc->$user->data->battle) == 0) {
                        if (!$army = array_shift($doc->$user->data->army)) {
                            $doc->$user->data->lose = true;
                            echo "isn't alive";
                            var_dump($doc->$user->data);
                            $this->alive = false;
                        } else {
                            echo $user . " Fetched a reserve\n";
                            $doc->$user->data->battle[] = $army;
                        }
                    }
                }
                foreach ($doc->users as $user) {
                    foreach ($doc->users as $auser) {
                        if ($auser != $user) {
                            $myEnemy = $auser;
                            break;
                        }
                    }
                    echo "$user attacks $myEnemy\n";
                    $enemies = count($doc->$myEnemy->data->battle);
                    $friends = count($doc->$user->data->battle);
                    echo "Friends $friends Enemy $enemies\n";
                    $battleResults->$user->battle = $doc->$user->data->battle;
                    while ($enemies--) {
                        $battleResults->$user->battle[0]->hp -= 1;
                        echo "$user(s) $enemies hit " . $battleResults->$user->battle[0]->hp . "\n";
                        if ($battleResults->$user->battle[0]->hp <= 0) {
                            echo "$user Lost a unit in action";
                            var_dump($battleResults->$user->battle);
                            array_shift($battleResults->$user->battle);
                            var_dump($battleResults->$user->battle);
                        }
                    }
                }
                foreach ($battleResults as $aUser => $battle) {
                    $doc->$aUser->data->battle = $battleResults->$aUser->battle;
                }
                try {
                    echo "putting=================================================================\n";
                    $success = $this->couchsag->update($doc->_id, $doc);
                    echo "put $loop\n";
                    $loop++;
                } catch (Exception $e) {
                    if ($e->getCode() == 409) {
                        $this->alive = true;
                        continue;
                        echo "Exception !!! " + $e->getCode();
                        echo "Exception !!! " + $e->getMessage();
                    }
                }
                echo "sleepy";
                sleep(1);
                echo "awake";
            }
            echo "out of here why?";
            echo $this->alive;
            echo "is avlie";
        } catch (Exception $e) {
            echo $e->getMessage;
            echo $e->getCode;
            echo $loop;
        }
    }

}
