<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* wargame.php */


//@include_once("/home/davidrod/webwargaming/BattleForAllenCreek.php");

class Wargame extends CI_Controller
{
    /*
     * Ugh, declaring models used as public properties, really grinds my gears.....
     */
    /* @var Battle $battle */
    public $battle;
    /* @var Couchsag $couchsag */
    public $couchsag;

    /* @var Wargame_model $wargame_model */

    public function __construct()
    {
        parent::__construct();
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/users/login/");
        }
    }

    function test()
    {
        echo "testing";
        $this->load->library("battle");
        include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
        echo "testing";

//        $doc = $this->couchsag->get("/MyWargame");
//        $doc->alist[] = "alis";
//        var_dump($doc);
//        $seq = $this->couchsag->update($doc->_id,$doc);

    }

    function nuke()
    {
        return;

        $data = $this->couchsag->get("Splunge");
        //$data = array("_id" => "Splunge", "docType" => "gamesAvail", "games" => array(array("BattleForAllenCreek")));
        $data->games[] = array("MartianCivilWar");
        //array_pop($data->games);
        $this->couchsag->update("Splunge", $data);
        $data = $this->couchsag->get("Splunge");
    }

    function index()
    {
        redirect("/wargame/play");
    }

    function leaveGame()
    {
        $this->session->unset_userdata('wargame');
        redirect("/wargame/play");
    }

    function unattachedGame($dir = false, $genre = false, $game = false)
    {
        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");

        $doc = $this->wargame_model->getDoc($wargame);
        $gameName = $doc->gameName;
        if ($gameName) {
            redirect("/wargame/play/");
        }

        $this->load->model('users/users_model');
        $gamesAvail = $this->users_model->getAvailGames($dir, $genre, $game);
        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies");
        $games = array();
        $theGame = false;
        if ($game !== false) {
            $theGame = $gamesAvail[0];
            $gameFeed = strtolower($game);
            $feed = file_get_contents("http://davidrodal.com/pubs/category/$gameFeed/feed");
            if ($feed !== false) {
                $xml = new SimpleXmlElement($feed);

                foreach ($xml->channel->item as $entry) {
                    if (preg_match("/Historical/", $entry->title)) {
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;
                        $theGame->value->longDesc = $str;
                    }
                    if (preg_match("/Player/", $entry->title)) {
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;
                        $theGame->value->playerNotes = $str;
                    }
                }
            }

//            $feed = file_get_contents('http://localhost/~david/wordpress/?feed=rss2&s=Lobositz');
//            $feed = preg_replace("/\n/","",$feed);
//            $matches  = array();
//            $regExp = preg_match("/<content:encoded><!\[CDATA\[(.*)]]><\/content:encoded/", $feed, $matches);
        } else {
            foreach ($gamesAvail as $gameAvail) {
                $games[] = $gameAvail;
            }
        }
        $nest = [];

        $this->load->view("wargame/wargameUnattached", compact("genre", "theGame", "games", "nest"));

    }

    function deleteGame($gameName)
    {
        $user = $this->session->userdata("user");
        if ($gameName) {
            try {

                $doc = $this->couchsag->get($gameName);
                if ($doc->createUser == $user) {
                    if ($doc && $doc->_id && $doc->_rev) {
                        $this->couchsag->delete($doc->_id, $doc->_rev);
                    }
                }
            } catch (Exception $e) {
            }
        }
        redirect("/wargame/play");
    }

    function play($poll = false)
    {
        $this->load->helper('date');
        $user = $this->session->userdata("user");

        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");
        if (!$wargame) {
//            $users = $this->couchsag->get('/_design/newFilter/_view/userByEmail');
//            $userids = $this->couchsag->get('/_design/newFilter/_view/userById');

//            var_dump($poll);
//            echo $this->wargame_model->getLobbyChanges(false,$poll);
            //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
            $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
            $lobbies = [];
            date_default_timezone_set("America/New_York");
            $odd = 0;

            foreach ($seq->rows as $row) {
                $keys = $row->key;
                $creator = array_shift($keys);
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
                if ($playerTurn == $user) {
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                } else {
                    $playerTurn .= "'s";
                }
                array_shift($thePlayers);
                $players = implode($thePlayers, " ");
                $row->value[1] = "created " . formatDateDiff($dt) . " ago";
                $odd ^= 1;
                $lobbies[] = array("odd" => $odd ? "odd" : "", "name" => $row->value[0], 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
            }
            $seq = $this->couchsag->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

            $otherGames = array();
            foreach ($seq->rows as $row) {
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
                if ($playerTurn == $user) {
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                }
                array_shift($thePlayers);
                $players = implode($thePlayers, " ");
                $row->value[1] = "created " . formatDateDiff($dt) . " ago";
                $otherGames[] = array("name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
            }
            $myName = $user;
            $this->parser->parse("wargame/wargameLobbyView", compact("lobbies", "otherGames", "myName"));
            return;

        }
        $doc = $this->wargame_model->getDoc($wargame);

        $name = $doc->name;
        $gameName = $doc->gameName;
        if (!$gameName) {
            redirect("/wargame/unattachedGame/");
        }
        if ($doc->playerStatus && $doc->playerStatus == "created") {
            redirect("/wargame/playAs");
        }
        $players = $doc->wargame->players;
        $player = array_search($user, $players);
        if ($player === false) {
            $player = 0;
        }
        $this->load->library('battle');
        $units = $doc->wargame->force->units;
        /* single unit docs */
//        if(is_numeric($units)){
//            $num = $units;
//            $units = array();
//            for($i = 0;$i< $num;$i++){
//                $units[] =  $this->couchsag->get("$wargame-id".$i);
//            }
//        }
//        var_dump($units[0]);
//        $units = array($units[0]);
//        $units = array("hi",'hell');
        $playerData = array($doc->wargame->playerData->$player);
        if (!$units) {
            $units = array();
        }
        $newUnits = array();
        foreach ($units as $aUnit) {
            $newUnit = array();
            foreach ($aUnit as $key => $value) {
                if ($key == "hexagon") {
                    continue;
                }
                $newUnit[$key] = $value;
            }
            $newUnit['class'] = $aUnit->nationality;
            $newUnit['type'] = $aUnit->class;
            $newUnit['unitSize'] = $aUnit->name;
            if ($aUnit->name == "infantry-1") {
                $newUnit['unitSize'] = 'xx';
            }
            if ($newUnit['range'] == 1) {
                $newUnit['range'] = '';
            }
            $newUnits[] = $newUnit;
        }
        $units = $newUnits;
        $mapUrl = $doc->wargame->mapData->mapUrl;
        $arg = $doc->wargame->arg;
        $scenario = $doc->wargame->scenario;
        $scenarioArray = [];
        $scenarioArray[] = $scenario;
        $this->parser->parse("wargame/wargameView", compact("scenarioArray", "name", "arg", "player", "mapUrl", "units", "playerData", "gameName", "wargame", "user"));
    }


    function changeWargame($newWargame = false)
    {
        $user = $this->session->userdata("user");
        $wargame = $this->session->userdata("wargame");

        $this->load->model("wargame/wargame_model");
        if ($newWargame == false) {
            $newWargame = $wargame;
        }
        if ($this->wargame_model->getDoc($newWargame)) {
            $this->wargame_model->leaveWargame($user, $wargame);
            $this->wargame_model->enterWargame($user, $newWargame);

            $this->session->set_userdata(array("wargame" => $newWargame));
        }
        redirect("/wargame/");
    }

    public function enterHotseat($newWargame = false)
    {
        if (!$newWargame) {
            redirect("wargame/play");
        }
        $wargame = $this->session->userdata("wargame");
        $this->load->model("wargame/wargame_model");
        $ret = $this->wargame_model->enterHotseat($newWargame);
        if ($ret) {
            redirect("wargame/changeWargame/$newWargame");
        } else {
            redirect("wargame/play");
        }
    }

    public function makePublic($game = false)
    {

        if ($game === false) {
            redirect("wargame/play");
        }
        $this->load->model("wargame/wargame_model");
        $ret = $this->wargame_model->makePublic($game);
        redirect("wargame/play");
    }

    public function makePrivate($game = false)
    {

        if ($game === false) {
            redirect("wargame/play");
        }
        $this->load->model("wargame/wargame_model");
        $ret = $this->wargame_model->makePrivate($game);
        redirect("wargame/play");
    }

    public function enterMulti($wargame = false, $playerOne = "", $playerTwo = "")
    {
        $user = $this->session->userdata("user");
        if (!$wargame) {
            redirect("wargame/play");

        }
        $this->load->model('wargame/wargame_model');
        $doc = $this->wargame_model->getDoc($wargame);
        if (!doc || $doc->createUser != $user) {
            redirect("wargame/play");
        }

        if ($playerOne == "") {
            $this->load->model('users/users_model');
            $users = $this->users_model->getUsersByUsername();
            foreach ($users as $k => $val) {
                if ($val->key == $user) {
                    unset($users[$k]);
                    continue;
                }
                $val->value = false;
                unset($val->value);
                $users[$k] = (array)$val;
            }

            $this->load->model("wargame/wargame_model");
            $doc = $this->wargame_model->getDoc(urldecode($wargame));
            if (!$doc || $doc->createUser != $user) {
                redirect("wargame/play");
            }
            $this->load->library("battle");
            $game = $doc->gameName;

            $path = site_url("wargame/enterMulti");
            $me = $user;
            $others = $users;

            $arg = $doc->wargame->arg;
            $this->parser->parse("wargame/wargameMulti", compact("game", "users", "wargame", "me", "path", "others", "arg"));
            return;
        }

//        $wargame = $this->session->userdata("wargame");
        $this->load->model("wargame/wargame_model");
        if ($playerTwo == "") {
            $playerTwo = $user;
        }
        $this->wargame_model->enterMulti($wargame, $playerOne, $playerTwo);
        redirect("wargame/changeWargame/$wargame");
    }

    public function testDB($name = "aaa")
    {
        $this->load->model("wargame/wargame_model");
        $cnt = 300;
        while ($cnt--) {
            $before = microtime(true);

            $doc = $this->wargame_model->getDoc($name);
            if ($doc) {
                $this->wargame_model->setDoc($doc);
                $after = microtime(true);
                echo $after - $before;
                if ($after - $before > .1) {
                    echo " BAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAD";
                }
                echo "<br>\n";
            }
        }
        echo "WE";
    }

    public function initDoc()
    {
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->initDoc();
    }

    public function fetch($last_seq = '')
    {
        $user = $this->session->userdata("user");
        $wargame = urldecode($this->session->userdata("wargame"));


        header("Content-Type: application/json");
        $this->load->model("wargame/wargame_model");
        $chatsIndex = $this->input->post('chatsIndex');
        $this->load->library("battle");
        /* @var Wargame_Model $this ->wargame_model */
        $ret = $this->wargame_model->getChanges($wargame, $last_seq, $chatsIndex, $user);
        echo json_encode($ret);
    }

    public function fetchLobby($last_seq = '')
    {

        $user = $this->session->userdata("user");
        $this->load->helper('date');
        $wargame = urldecode($this->session->userdata("wargame"));


        header("Content-Type: application/json");
        $this->load->model("wargame/wargame_model");


        $lastSeq = $this->wargame_model->getLobbyChanges($user, $last_seq);
        //$seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"hot seat\"]&endkey=[\"$user\",\"hot seat\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");
        $lobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameType = array_shift($keys);
            $gameName = array_shift($keys);
            $name = array_shift($keys);
            $playerTurn = array_shift($keys);
            array_shift($keys);
            $public = array_shift($keys);
            $filename = array_shift($keys);
//               $key = implode($keys,"  ");
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[4];
            $currentTurn = $row->value[5];
            $maxTurn = $row->value[6];
            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {

                $playerTurn = "$currentTurn of $maxTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $lobbies[] = array("public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies?startkey=[\"$user\",\"multi\"]&endkey=[\"$user\",\"multi\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $multiLobbies = [];
        date_default_timezone_set("America/New_York");
        $odd = 0;
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $gameType = array_shift($keys);
            $gameName = array_shift($keys);
            $name = array_shift($keys);
            $playerTurn = array_shift($keys);
            array_shift($keys);
            $public = array_shift($keys);
            $filename = array_shift($keys);
//               $key = implode($keys,"  ");
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[4];

            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {
                if ($playerTurn == $user) {
                    $playerTurn = "It's Your Turn";
                    $myTurn = "myTurn";
                } else {
                    $playerTurn = "It's " . $playerTurn . "'s Turn";
                }
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $multiLobbies[] = array("public" => $public, "odd" => $odd ? "odd" : "", "gameName" => $gameName, "name" => $name, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->couchsag->get("/_design/newFilter/_view/getGamesImIn?startkey=[\"$user\"]&endkey=[\"$user\",\"zzzzzzzzzzzzzzzzzzzzzzzz\"]");

        $odd = 0;
        $otherGames = array();
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $you = array_shift($keys);
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameName = array_shift($keys);
            $oldGame = array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $gameOver = $row->value[3];
            $myTurn = "";
            if ($gameOver === true) {
                $playerTurn = "Game Over";
                $myTurn = "gameOver";
            } else {
                if ($playerTurn == $user) {
                    $playerTurn = "Your";
                    $myTurn = "myTurn";
                }
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $otherGames[] = array("odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $seq = $this->couchsag->get("/_design/newFilter/_view/publicGames");

        $odd = 0;
        $publicGames = array();
        foreach ($seq->rows as $row) {
            $keys = $row->key;
            $creator = array_shift($keys);
            $name = array_shift($keys);
            $gameName = array_shift($keys);
            array_shift($keys);
            $gameType = array_shift($keys);
            $playerTurn = array_shift($keys);
            $filename = array_shift($keys);
            $id = $row->id;
            $dt = new DateTime($row->value[1]);
            $thePlayers = $row->value[2];
            $playerTurn = $thePlayers[$playerTurn];
            $myTurn = "";
            if ($playerTurn == $user) {
                $playerTurn = "Your";
                $myTurn = "myTurn";
            }
            array_shift($thePlayers);
            $players = implode($thePlayers, " ");
            $row->value[1] = "created " . formatDateDiff($dt) . " ago";
            $odd ^= 1;
            $publicGames[] = array("odd" => $odd ? "odd" : "", "name" => $name, "gameName" => $gameName, 'date' => $row->value[1], "id" => $id, "creator" => $creator, "gameType" => $gameType, "turn" => $playerTurn, "players" => $players, "myTurn" => $myTurn);
        }
        $results = $lastSeq->results;
        $last_seq = $lastSeq->last_seq;
        $ret = compact("lobbies", "multiLobbies", "otherGames", "last_seq", "results", "publicGames");
        echo json_encode($ret);
    }

    public function add()
    {
        $user = $this->session->userdata("user");
        $wargame = urldecode($this->session->userdata("wargame"));
        $chat = $this->input->post('chat', TRUE);
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->addChat($chat, $user, urldecode($wargame));
        return compact('success');
    }

    public function save()
    {

    }


    public function unitTest()
    {
        return;
        while (true) {
            $now = explode(" ", microtime());
            $now[0] = preg_replace("/^0/", "", $now[0]);
            $now = $now[1] . $now[0];
//            echo "Reading ".$now."\n";
            $then = $now;
            $data = $this->couchsag->get("Mcw");
            $now = explode(" ", microtime());
            $now[0] = preg_replace("/^0/", "", $now[0]);
            $now = $now[1] . $now[0];
//            echo "rev ".$data->_rev."\n";
//            echo "readit writing $now  \n";
            echo "Diff " . ($now - $then) . "\n";
            $then = $now;
            $this->couchsag->update("Mcw", $data);
            $now = explode(" ", microtime());
            $now[0] = preg_replace("/^0/", "", $now[0]);
            $now = $now[1] . $now[0];
//            echo "rev ".$data->_rev."\n";
//            echo "written ".$now."\n";
            echo "Diff " . ($now - $then) . "\n\n\n";
            sleep(1);
        }
    }

    public function poke()
    {
        $user = $this->session->userdata("user");

        $player = $this->session->userdata("player");
        $wargame = urldecode($this->session->userdata("wargame"));

        $x = (int)$this->input->post('x', FALSE);
        $y = (int)$this->input->post('y', FALSE);
        $event = (int)$this->input->post('event', FALSE);
        $id = $this->input->post('id', FALSE);

        $this->load->model("wargame/wargame_model");
        /*  @var  Wargame_model */
//        file_put_contents("/tmp/perflog","\nGetting poke ".microtime(),FILE_APPEND);
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        $ter = false;
        if ($doc->wargame->terrainName) {
            $ter = $this->wargame_model->getDoc($doc->wargame->terrainName);
            $doc->wargame->terrain = $ter->terrain;
        }
        /* single unit docs */
//        if(is_numeric($doc->wargame->force->units)){
//            $num = $doc->wargame->force->units;
//            $doc->wargame->force->units = array();
//            for($i = 0;$i< $num;$i++){
//                $doc->wargame->force->units[] =  $this->couchsag->get("$wargame-id".$i);
//
//            }
//        }
//        file_put_contents("/tmp/perflog","\nGotten poke ".microtime(),FILE_APPEND);
        $this->load->library("battle");
        $game = $doc->gameName;
        $emsg = false;
        $click = $doc->_rev;
        $matches = array();
        preg_match("/^([0-9]+)-/", $click, $matches);
        $click = $matches[1];
        try {
            $battle = $this->battle->getBattle($game, $doc->wargame, $doc->wargame->arg);
            $doSave = $battle->poke($event, $id, $x, $y, $user, $click);
            $success = false;
            if ($doSave) {
                $doc->wargame = $battle->save();
                /* single unit docs */
//            $num = 0;
//            foreach($doc->wargame->force->units as $unit){
//                $num++;
//                if($unit->dirty){
//                    $unit->_id = "$wargame-id".$unit->id;
//                    $this->couchsag->update($unit->_id, $unit);
//                }
//
//            }
//            $doc->wargame->force->units = $num;
                $this->wargame_model->setDoc($doc);
                $success = true;

//            file_put_contents("/tmp/perflog","\nsaving poke ".microtime(),FILE_APPEND);

//            $ter = $this->wargame_model->getDoc("terrain-MartianCivilWar");
//            $ter->terrain = $doc->wargame->terrain;
//            $this->wargame_model->setDoc($ter);
//            file_put_contents("/tmp/perflog","\nsaving poked ".microtime(),FILE_APPEND);

            }
            if ($doSave === 0) {
                $success = true;
            }
        } catch (Exception $e) {
            $emsg = $e->getMessage() . " \nFile: " . $e->getFile() . " \nLine: " . $e->getLine() . " \nCode: " . $e->getCode();
            $success = false;
        }
        if (!$success) {
            header("HTTP/1.1 404 Not Found");
        }
        echo json_encode(compact('success', "emsg"));
    }

    public function resize($small = true)
    {
        $user = $this->session->userdata("user");
        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));

        $players = $doc->wargame->players;
        $player = array_search($user, $players);
        if ($player === false) {
            $player = 0;
        }
        $this->load->library("battle");
        $game = $doc->gameName;
        $battle = $this->battle->getBattle($game, $doc->wargame);
        $battle->resize($small, $player);
        $doc->wargame = $battle->save();
        $doc = $this->wargame_model->setDoc($doc);
        redirect("/wargame/play/");
    }

    public function unitInit($game = "MartianCivilWar", $arg = false)
    {
        $user = $this->session->userdata("user");
        $wargame = urldecode($this->session->userdata("wargame"));
        $chat = $this->input->post('chat', TRUE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        if ($user != $doc->createUser) {
            redirect("wargame/play");
        }

        $this->load->library("battle");

        $this->load->model('users/users_model');
        $battle = $this->battle->getBattle($game, null, $arg);


        if (method_exists($battle, 'init')) {
            $battle->init();
        }
        $doc->wargame = $battle->save();
        $click = $doc->_rev;
        $matches = array();
        preg_match("/^([0-9]+)-/", $click, $matches);
        $click = $matches[1];
        $doc->wargame->gameRules->phaseClicks[] = $click + 1;
        if ($doc->wargame->genTerrain) {
            try {
                $ter = $this->wargame_model->getDoc($doc->wargame->terrainName);
            } catch (Exception $e) {
            };
            if (!$ter) {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                $this->couchsag->create($data);
            } else {
                $data = array("_id" => $doc->wargame->terrainName, "docType" => "terrain", "terrain" => $doc->wargame->terrain);
                /* totall throw the old one away */
//                $ter->terrain = $doc->wargame->terrain;
//                $this->couchsag->update($data['_id'],$data);
                $this->couchsag->delete($doc->wargame->terrainName, $ter->_rev);
                $this->couchsag->create($data);

            }
            unset($doc->wargame->terrain);
            $doc->wargame->genTerrain = false;

        }
        $doc->chats = array();
        $doc->gameName = $game;
        /* single Unit docs */
//        $num = 0;
//        echo "HEY";
//        foreach($doc->wargame->force->units as $unit){
//            echo "around ";
//            if($unit->dirty){
//                $num++;
//                $unit->_id = "$wargame-id".$unit->id;
//                try{
//                    $dead = $this->couchsag->get($unit->_id);
//                    $this->couchsag->delete($dead->_id, $dead->_rev);
//
//                }catch(Exception $e){echo "tee ";}
//                $this->couchsag->update($unit->_id, $unit);
//            }
//        }

//        $doc->wargame->force->units = $num;
        $doc = $this->wargame_model->setDoc($doc);
        redirect("wargame/playAs/$game");

    }

    function playAs($game = false)
    {
        $user = $this->session->userdata("user");
        $wargame = urldecode($this->session->userdata("wargame"));
        if (!$wargame && $game) {
            $wargame = $game;
        }
//        $wargame = "MainWargame";
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        if (!$doc || $doc->createUser != $user) {
            redirect("wargame/play");
        }
        $this->load->library("battle");
        $game = $doc->gameName;
        $arg = $doc->wargame->arg;
        $this->load->view("wargame/wargamePlayAs", compact("game", "user", "wargame", $doc->wargame, "arg"));
    }

    public function createWargame()
    {

        $message = "";
        $wargame = $this->input->post('wargame');
        if ($wargame) {
            $this->load->model("wargame/wargame_model");
            $ret = $this->wargame_model->createWargame($wargame);
            if (is_object($ret) === true) {
                $this->session->set_userdata(array("wargame" => $ret->body->id));
                redirect("/wargame/play");
            }
            $message = "Name $wargame already used, please enter new name";
//            redirect("/wargame/unitInit");
        }
        if ($this->input->post()) {
            $message = "Please in put a name (need not be unique)";
        }
        $this->load->view("wargame/wargameCreate", compact("message"));
    }
//    public function clock()
//    {
//
//        while (true) {
//            $date = date("h:i:s A");
//            $doc = $this->couchsag->get("MainWargame");
//            $doc->clock = $date;
//            $success = $this->couchsag->update($doc->_id, $doc);
//            sleep(1);die();
//        }
//    }
}
