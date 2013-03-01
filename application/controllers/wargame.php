<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* wargame.php */


//@include_once("/home/davidrod/webwargaming/BattleForAllenCreek.php");

class Wargame extends CI_Controller
{
    /* @var Wargame_model $wargame_model */

    function test(){
        echo "testing";
        $this->load->library("battle");
        include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
        echo "testing";

//        $doc = $this->couchsag->get("/MyWargame");
//        $doc->alist[] = "alis";
//        var_dump($doc);
//        $seq = $this->couchsag->update($doc->_id,$doc);

    }

	function nuke(){
return;

        $data = $this->couchsag->get("Splunge");
        //$data = array("_id" => "Splunge", "docType" => "gamesAvail", "games" => array(array("BattleForAllenCreek")));
	$data->games[] = array("MartianCivilWar");
	//array_pop($data->games);
        $this->couchsag->update("Splunge",$data);
        $data = $this->couchsag->get("Splunge");
var_dump($data);
    }

    function index()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        redirect("/wargame/play");
    }

    function leaveGame(){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $this->session->unset_userdata('wargame');
               redirect("/wargame/play");
    }
    function unattachedGame()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");

        $doc = $this->wargame_model->getDoc($wargame);
        $gameName = $doc->gameName;
        if($gameName){
            redirect("/wargame/play/");
        }


        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies");
        $gamesAvail = $this->couchsag->get("/_design/newFilter/_view/getAvailGames");
        $games = array();
        foreach($gamesAvail->rows as $row){
        $games[] =  array("name"=>$row->value[0],'arg'=>$row->value[1]);
    }

        $this->parser->parse("wargame/wargameUnattached",compact("games"));

    }
    function play()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");

        if(!$wargame){
            $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies");
            foreach($seq->rows as $row){
                $lobbies[] =  array("name"=>$row->value, "id"=>$row->id);
            }
            $this->parser->parse("wargame/wargameLobbyView",compact("lobbies"));
            return;

        }
        $doc = $this->wargame_model->getDoc($wargame);

        $gameName = $doc->gameName;
        if(!$gameName){
            redirect("/wargame/unattachedGame/");
        }
        $players = $doc->wargame->players;
        $player = array_search($user,$players);
        if($player === false){
            $player = 0;
        }
        $this->load->library('battle');
        $gamesAvail = $this->couchsag->get("/_design/newFilter/_view/getAvailGames");
        foreach($gamesAvail->rows as $row){
            $games[] =  array("name"=>$row->value[0],'arg'=>$row->value[1]);
        }
        $units = $doc->wargame->force->units;
//        var_dump($units[0]);
//        $units = array($units[0]);
//        $units = array("hi",'hell');
        $playerData = array($doc->wargame->playerData->$player);
        if(!$units) {
            $units = array();
        }
        $newUnits = array();
        foreach($units as $aUnit){
            $newUnit = array();
            foreach($aUnit as $key => $value){
                if($key == "hexagon"){
                    continue;
                }
                $newUnit[$key] = $value;
            }
                $newUnit['class'] = $aUnit->nationality;
            $newUnits[] = $newUnit;
        }
        $units = $newUnits;
//        $myCrt = new CombatResultsTable();
        //echo "Welcome $user";
        //echo $this->twig->render("wargame/wargameView.php",compact("wargame","lobbies"));
//        $this->parser->parse("wargame/wargameView",array("things" => array(array("a"=>"a"),array("b"=>"b"),array("c"=>"c"))));
        $this->parser->parse("wargame/wargameView",compact("units","playerData","games","gameName","wargame","lobbies","user"));

    }

    function logout()
    {
        $user = $this->session->userdata("user");
        $wargame = $this->session->userdata("wargame");
        $this->session->sess_destroy();
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->leaveWargame($user,$wargame);
        redirect("/wargame/");
    }

    function _getBattle($name,$warGame){
        switch($name){
            case "BattleOfMoscow":
            @include_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleOfMoscow.php");
            break;
            case "BattleForAllenCreek":
            @include_once("/Documents and Settings/Owner/Desktop/BfAC/BattleForAllenCreek.php");
            break;
            default:
                throw(new Exception("Bad Class Dude!"));
        }
        return new $name($warGame);
    }
    function login()
    {
        $user = $this->session->userdata("user");
        $data = $this->input->post();
        if (!$user && $data) {
            if($data['password'] == "2havefun")
            {
            $user = $data['name'];
            $this->session->set_userdata(array("user" => $user));
//            $this->session->set_userdata(array("wargame" => "MainWargame"));
//            $this->load->model('wargame/wargame_model');
//            $this->wargame_model->enterWargame($user, "MainWargame");
            redirect("/wargame/");
            }
        }
        $this->load->view("login");

    }

    function changeWargame($newWargame = "MainWargame", $player = "0"){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = $this->session->userdata("wargame");

        $this->load->model("wargame/wargame_model");
        $this->wargame_model->leaveWargame($user,$wargame);
        $this->wargame_model->enterWargame($user,$newWargame,$player);

//        $this->session->set_userdata(array("player" => $player));
//        $this->session->set_userdata(array("mapWidth" => "783px"));
//        $this->session->set_userdata(array("mapHeight" => "638px"));
//        $this->session->set_userdata(array("unitSize" => "48px"));

        $this->session->set_userdata(array("wargame" => $newWargame));
        redirect("/wargame/");
    }

    public function initDoc(){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->initDoc();
    }

    public function fetch( $last_seq = '')
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));


        header("Content-Type: application/json");
        $this->load->model("wargame/wargame_model");
        $chatsIndex = $this->input->post('chatsIndex');
        $this->load->library("battle");

        $ret = $this->wargame_model->getChanges($wargame, $last_seq,$chatsIndex,$user);
        echo json_encode($ret);
    }

    public function add()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->addChat($chat,$user,urldecode($wargame));
        return compact('success');
    }
 /*   public function unit($unit = null)
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $player = $this->session->userdata("player");

        $wargame = urldecode($this->session->userdata("wargame"));
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        if($doc->wargame->gameRules->attackingForceId !== (int)$player){
             echo "Nope $player";
            return "nope";
        }
        $battle = new BattleForAllenCreek($doc->wargame);

        	$battle->gameRules->processEvent(SELECT_COUNTER_EVENT, $unit, $battle->force->getUnitHexagon($unit));
        $units = $battle->force->units;
        $combats = array();
        foreach($units as $unitId => $unit){
            if($unit->combatNumber){
                $combats[$unit->combatNumber]['combatIndex'] = $unit->combatIndex;
                $combats[$unit->combatNumber]['units'][] = $unitId;
            }
        }
        $doc->wargame = $battle->save();
        $succ = $this->wargame_model->setDoc($doc);
        return compact('success');
    }*/
    public function save()
    {
//        $user = $this->session->userdata("user");
//        if (!$user) {
//            redirect("/wargame/login/");
//        }
//        $player = $this->session->userdata("player");
//        $wargame = urldecode($this->session->userdata("wargame"));
//        $chat = $this->input->post('chat',TRUE);
//        $this->load->model("wargame/wargame_model");
//        $doc = $this->wargame_model->getDoc(urldecode($wargame));
//        $battle = new BattleForAllenCreek($doc->wargame);
//
//        $battle->gameRules->processEvent(SELECT_COUNTER_EVENT, $unit, $battle->force->getUnitHexagon($unit));
//        $units = $battle->force->units;
//        $combats = array();
//        foreach($units as $unitId => $unit){
//            if($unit->combatNumber){
//                $combats[$unit->combatNumber]['combatIndex'] = $unit->combatIndex;
//                $combats[$unit->combatNumber]['units'][] = $unitId;
//            }
//        }
//        $doc->wargame = $battle->save();
//        $succ = $this->wargame_model->setDoc($doc);
//        return compact('success');
    }
/*    public function map()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $player = $this->session->userdata("player");
        $wargame = urldecode($this->session->userdata("wargame"));
        $x = (int)$this->input->post('x',FALSE);
        $y = (int)$this->input->post('y',FALSE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        if($doc->wargame->gameRules->attackingForceId !== (int)$player){
            echo "Nope $player";
            return "nope";
        }

        $battle = new BattleForAllenCreek($doc->wargame);
        $mapGrid = new MapGrid($battle->mapData);
        $mapGrid->setPixels($x, $y);
        $battle->gameRules->processEvent(SELECT_MAP_EVENT, MAP, $mapGrid->getHexagon() );
        $units = $battle->force->units;
        $combats = array();
        foreach($units as $unitId => $unit){
            if($unit->combatNumber){
                $combats[$unit->combatNumber]['combatIndex'] = $unit->combatIndex;
                $combats[$unit->combatNumber]['units'][] = $unitId;
            }
        }
        $doc->wargame = $battle->save();
        $doc->wargame->combats = $combats;
        $doc = $this->wargame_model->setDoc($doc);

        return compact('success');
    }*/

    public function poke()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }

        $player = $this->session->userdata("player");
        $wargame = urldecode($this->session->userdata("wargame"));

        $x = (int)$this->input->post('x',FALSE);
        $y = (int)$this->input->post('y',FALSE);
        $event = (int)$this->input->post('event',FALSE);
        $id = $this->input->post('id',FALSE);

        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        $this->load->library("battle");
        $game = $doc->gameName;
        $battle = $this->battle->getBattle($game,$doc->wargame);
//        $battle = $this->_getBattle($game,$doc->wargame);

//        $battle = new BattleForAllenCreek($doc->wargame);
        $battle->poke($event,$id,$x,$y, $user);
        $doc->wargame = $battle->save();
        $this->wargame_model->setDoc($doc);

        return compact('success');
    }

    public function resize($small = true)
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));

        $players = $doc->wargame->players;
        $player = array_search($user,$players);
        if($player === false){
            $player = 0;
        }
        $this->load->library("battle");
        $game = $doc->gameName;
        $battle = $this->battle->getBattle($game,$doc->wargame);
        $battle->resize($small,$player);
        $doc->wargame = $battle->save();
        $doc = $this->wargame_model->setDoc($doc);
        redirect("/wargame/play/");
    }
 /*   public function phase()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $player = $this->session->userdata("player");
        $wargame = urldecode($this->session->userdata("wargame"));
        $x = $this->input->post('x',FALSE);
        $y = $this->input->post('y',FALSE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        if($doc->wargame->gameRules->attackingForceId !== (int)$player){
            var_dump($doc->wargame->gameRules->attackingForceId);
            var_dump($player);
            echo "Nope $player";
            return "nope";
        }
        $battle = new BattleForAllenCreek($doc->wargame);
        $mapGrid = new MapGrid($battle->mapData);
        $mapGrid->setPixels($x, $y);
//        echo "HIeeI $x $y ";var_dump($mapGrid->getHexagon()->number);echo "Hexed";
        $battle->gameRules->processEvent(SELECT_BUTTON_EVENT, "next_phase", 0,0 );

        //        $myBattle = $battle->save();
        //        $jBattle = json_encode($myBattle);
        //        //    $jBattle = preg_replace("/{/","{\n",$jBattle);
        //        //    $jBattle = preg_replace("/}/","\n}",$jBattle);
        //        file_put_contents("afile.out", $jBattle);

        $doc->wargame = $battle->save();
        //        var_dump($doc->wargame);
        $doc = $this->wargame_model->setDoc($doc);

        //        var_dump($doc);
        return compact('success');
    }*/

   public function unitInit($game = "MartianCivilWar", $arg = false)
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
//        $wargame = "MainWargame";
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        $this->load->library("battle");
//        $game = "BattleOfMoscow";
        $battle = $this->battle->getBattle($game,null, $arg);
//       $battle = new BattleOfMoscow();
        $doc->wargame = $battle->save();
        $doc->chats = array();
        $doc->gameName = $game;
        $doc = $this->wargame_model->setDoc($doc);
        redirect("wargame/playAs/$game");

        //        	$battle->gameRules->processEvent(SELECT_COUNTER_EVENT, $unit, $battle->force->getUnitHexagon($umit));
        //        $myBattle = $battle->save();
        //        $jBattle = json_encode($myBattle);
        //        //    $jBattle = preg_replace("/{/","{\n",$jBattle);
        //        //    $jBattle = preg_replace("/}/","\n}",$jBattle);
        //        file_put_contents("afile.out", $jBattle);

    }
    function playAs(){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));
//        $wargame = "MainWargame";
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        $this->load->library("battle");
        $game = $doc->gameName;
        $this->load->view("wargame/wargamePlayAs",compact("game","user","wargame"))   ;
    }
    public function createWargame()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }

        $wargame = $this->input->post('wargame');
        if($wargame){
            $this->load->model("wargame/wargame_model");
            $this->wargame_model->createWargame($wargame);
//            $this->unitInit($wargame);
            $this->session->set_userdata(array("wargame" => $wargame));
            redirect("/wargame/play");
//            redirect("/wargame/unitInit");
        }
        $this->load->view("wargame/wargameCreate");
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
