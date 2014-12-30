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

    function unattachedGame($dir = false, $genre = false, $game = false, $theScenario = false)
    {

        $this->load->model('users/users_model');
        $gamesAvail = $this->users_model->getAvailGames($dir, $genre, $game);
        $plainGenre = rawurldecode($genre);
        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies");
        $games = array();
        $theGame = false;
        $siteUrl = site_url("wargame/unattachedGame/");

        if ($game !== false) {
            $terrainName = "terrain-".$game;
            if($theScenario){
                $terrainName .= ".$theScenario";
            }
            try {
                $terrain = $this->couchsag->get($terrainName);
            }catch(Exception $e){}
            if(!$terrain){
                $terrain = $this->couchsag->get("terrain-".$game);
            }

            $mapUrl = $terrain->terrain->mapUrl;
            if(isset($terrain->terrain->smallMapUrl)){
                $mapUrl = $terrain->terrain->smallMapUrl;
            }

            $theGame = $gamesAvail[0];
            $gameFeed = strtolower($game);
            $feed = file_get_contents("http://davidrodal.com/pubs/category/$gameFeed/feed");
            if ($feed !== false) {
                $xml = new SimpleXmlElement($feed);

                foreach ($xml->channel->item as $entry) {
                    if (preg_match("/Historical/", $entry->title)) {
                        $matches = [];
                        preg_match("/p=(\d+)$/",$entry->guid,$matches);
                        $editLink = "http://davidrodal.com/pubs/wp-admin/post.php?post=".$matches[1]."&action=edit";
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;
                        // http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php
                        $str = preg_replace('/[[:^print:]]/', '', $str); // should be aA
                        $str = preg_replace("/></","> <", $str);
                        $theGame->value->longDesc = $str;
                        $theGame->value->histEditLink = "<a target='blank' href='$editLink'>edit</a>";
                    }
                    if (preg_match("/Player/", $entry->title)) {
                        $content = $entry->children('http://purl.org/rss/1.0/modules/content/');
                        $str = $content->encoded;

                        // http://stackoverflow.com/questions/8781911/remove-non-ascii-characters-from-string-in-php
                        $str = preg_replace('/[[:^print:]]/', '', $str); // should be aA
                        $matches = [];
                        if(preg_match("/p=(\d+)$/",$entry->guid,$matches)){
                            $editLink = "http://davidrodal.com/pubs/wp-admin/post.php?post=".$matches[1]."&action=edit";
                            $theGame->value->playerEditLink = "<a target='blank' href='$editLink'>edit</a>";
                        }
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
                $gameAvail->urlGenre = rawurlencode($gameAvail->genre);
                $games[] = $gameAvail;
            }
        }
        $nest = [];

        $this->parser->parse("wargame/wargameUnattached", compact("mapUrl","theScenario", "plainGenre", "theGame", "games", "nest","siteUrl"));

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
        echo json_encode(["success"=>true, "emsg"=>false]);
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
                if($key == "adjustments"){
                    continue;
                }
                $newUnit[$key] = $value;
            }
            $newUnit['class'] = $aUnit->nationality;
            $newUnit['type'] = $aUnit->class;
            $newUnit['unitSize'] = $aUnit->name;
            $newUnit['unitDesig'] = $aUnit->unitDesig;
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
        echo json_encode(["success"=>true, "emsg"=>false]);
    }

    public function makePrivate($game = false)
    {

        if ($game === false) {
            redirect("wargame/play");
        }
        $this->load->model("wargame/wargame_model");
        $ret = $this->wargame_model->makePrivate($game);
        echo json_encode(["success"=>true, "emsg"=>false]);
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
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        $ter = false;
        if ($doc->wargame->terrainName) {
            try {
                $ter = $this->wargame_model->getDoc($doc->wargame->terrainName);
            }catch(Exception $e){var_dump($e->getMessage());}
            $doc->wargame->terrain = $ter->terrain;
        }
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

                $this->wargame_model->setDoc($doc);
                $success = true;

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

    public function terrainInit($game = "MartianCivilWar", $arg = false, $terrainDocId = false)
    {
        $user = $this->session->userdata("user");

        $this->load->library("battle");

        $this->load->model('users/users_model');
        $battle = $this->battle->getBattle($game, null, $arg);


        if (method_exists($battle, 'terrainGen')) {
            $this->load->model('rest/rest_model');
            $terrainDoc = $this->rest_model->get($terrainDocId);
            $mapId = $terrainDoc->hexStr->map;
            $mapDoc = $this->rest_model->get($mapId);
            $battle->terrainGen($mapDoc, $terrainDoc);
        }else{
            echo "No TerrainGen ";
            return;
        }

        $mapUrl = $battle->terrain->mapUrl;
        $mapWidth = $battle->terrain->mapWidth;
        if($mapWidth && $mapWidth !== "auto"){
            $mapWidth = preg_replace("/[^\d]*(\d*)[^\d]*/","$1", $mapWidth);
            $battle->terrain->mapUrl = $this->resizeImage($mapUrl, $mapWidth, "images");
        }
        $battle->terrain->smallMapUrl = $this->resizeImage($mapUrl);
        $wargameDoc = $battle->save();

        $this->load->model("wargame/wargame_model");
        $this->wargame_model->saveTerrainDoc(urldecode($wargameDoc->terrainName.".".$arg), $wargameDoc);

        if($mapDoc->map->isDefault){
            $this->wargame_model->saveTerrainDoc(urldecode($wargameDoc->terrainName), $wargameDoc);

        }
        $ret = new stdClass();
        $ret->ok = true;
        echo json_encode($ret);
//        redirect("wargame/playAs/$game");
    }

    public function resizeImage($filename, $new_width = 500, $dir = 'smallImages')
    {

// Get new dimensions
        list($width, $height, $type) = getimagesize($filename);
        $new_height = ($height / $width) * $new_width;

// Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        switch($type){
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filename);
                break;
        }
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// Output
        $f = "$dir/".basename($filename,'.png'). ".png";
        imagepng($image_p, "js/$f");
        return dirname($filename)."/".$f;

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


        if (method_exists($battle, 'terrainInit')) {
            try{
                $terrainName = "terrain-$game.$arg";
                $terrainDoc = $this->couchsag->get($terrainName);
            }catch(Exception $e){}
            if(!$terrainDoc){
                try{
                    $terrainName = "terrain-$game";
                    $terrainDoc = $this->couchsag->get($terrainName);
                }catch(Exception $e){var_dump($e->getMessage());}
            }
            $battle->terrainInit($terrainDoc);
        }
        if (method_exists($battle, 'init')) {
            $battle->init();
        }
        $doc->wargame = $battle->save();
        $doc->wargame->terrainName = $terrainName;
        $click = $doc->_rev;
        $matches = array();
        preg_match("/^([0-9]+)-/", $click, $matches);
        $click = $matches[1];
        $doc->wargame->gameRules->phaseClicks[] = $click + 1;
        /* should probably get rid of this old code for genTerrain */
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
                /* totally throw the old one away */

                $this->couchsag->delete($doc->wargame->terrainName, $ter->_rev);
                $this->couchsag->create($data);

            }
            unset($doc->wargame->terrain);
            $doc->wargame->genTerrain = false;

        }
        $doc->chats = array();
        $doc->gameName = $game;

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

    public function createWargame($game, $scenario)
    {

        $message = "";
        $wargame = $this->input->post('wargame');
        if ($wargame) {
            $this->load->model("wargame/wargame_model");
            $ret = $this->wargame_model->createWargame($wargame);
            if (is_object($ret) === true) {
                $this->session->set_userdata(array("wargame" => $ret->body->id));
                redirect("/wargame/unitInit/$game/$scenario");
            }
            $message = "Name $wargame already used, please enter new name";
        }
        if ($this->input->post()) {
            $message = "Please in put a name (need not be unique)";
        }
        $this->load->view("wargame/wargameCreate", compact("message", "game","scenario"));
    }
}
