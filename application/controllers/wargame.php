<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/* wargame.php */

class Wargame extends CI_Controller
{
    /* @var Wargame_model $wargame_model */

    function index($wargame = "MainWargame")
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        redirect("/wargame/chat");
        $this->load->view("wargame/wargameView",compact("wargame"));

    }

    function chat()
    {
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = urldecode($this->session->userdata("wargame"));

        $seq = $this->couchsag->get("/_design/newFilter/_view/getLobbies");
        foreach($seq->rows as $row){
            $lobbies[] =  array("name"=>$row->value, "id"=>$row->id);
        }
        //echo "Welcome $user";
        //echo $this->twig->render("wargame/wargameView.php",compact("wargame","lobbies"));
        $this->parser->parse("wargame/wargameView",compact("wargame","lobbies","user"));

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

    function login()
    {
        $user = $this->session->userdata("user");
        $data = $this->input->post();
        if (!$user && $data) {
            $user = $data['name'];
            $this->session->set_userdata(array("user" => $user));
            $this->session->set_userdata(array("wargame" => "MainWargame"));
            $this->load->model('wargame/wargame_model');
            $this->wargame_model->enterWargame($user, "MainWargame");
            redirect("/wargame/");
        }
        $this->load->view("login");

    }

    function addchat(){
        echo "Addingchat";
        $chat['chat'] = "hi there!";
        $this->couchsag->update("_design/newFilter/_update/addchat/tempwargame?chat=th jjjg iseeee bites","");
        echo "addedchat";
    }
    function changeWargame($newWargame = "MainWargame"){
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/wargame/login/");
        }
        $wargame = $this->session->userdata("wargame");

        $this->load->model("wargame/wargame_model");
        $this->wargame_model->leaveWargame($user,$wargame);
        $this->wargame_model->enterWargame($user,$newWargame);

        $this->session->set_userdata(array("wargame" => $newWargame));
        redirect("/wargame/");
    }

    public function initDoc(){
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->initDoc();
    }
    public function fetch($wargame = "MainWargame", $last_seq = '')
    {


        header("Content-Type: application/json");
        $this->load->model("wargame/wargame_model");
        $chatsIndex = $this->input->post('chatsIndex');
        $ret = $this->wargame_model->getChanges($wargame, $last_seq,$chatsIndex);
        echo json_encode($ret);
    }

    public function add($wargame = "MainWargame")
    {
        $user = $this->session->userdata("user");
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $this->wargame_model->addChat($chat,$user,urldecode($wargame));
        return compact('success');
    }
    public function unit($wargame = "MainWargame",$unit = null)
    {
        $user = $this->session->userdata("user");
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        require_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleForAllenCreek.php");
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
//        $myBattle = $battle->save();
//        $jBattle = json_encode($myBattle);
//        //    $jBattle = preg_replace("/{/","{\n",$jBattle);
//        //    $jBattle = preg_replace("/}/","\n}",$jBattle);
//        file_put_contents("afile.out", $jBattle);
        $doc->wargame = $battle->save();
        $doc->wargame->combats[] = $combats;
//        var_dump($doc->wargame);
        $succ = $this->wargame_model->setDoc($doc);
        return compact('success');
    }
    public function map($wargame = "MainWargame")
    {
        $user = $this->session->userdata("user");
        $x = $this->input->post('x',FALSE);
        $y = $this->input->post('y',FALSE);
        echo "X: $x Y: $y";
        $this->load->model("wargame/wargame_model");
        echo "loaded";
        $doc = $this->wargame_model->getDoc(urldecode($wargame));

        require_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleForAllenCreek.php");
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
//        $prompt = $battle->prompt->getPrompt(OVER_MAP_EVENT, MAP, $mapGrid->getHexagon());


        //        $myBattle = $battle->save();
        //        $jBattle = json_encode($myBattle);
        //        //    $jBattle = preg_replace("/{/","{\n",$jBattle);
        //        //    $jBattle = preg_replace("/}/","\n}",$jBattle);
        //        file_put_contents("afile.out", $jBattle);

        $doc->wargame = $battle->save();
        $doc->wargame->combats = $combats;
//        var_dump($doc->wargame);
        $doc = $this->wargame_model->setDoc($doc);

//        var_dump($doc);
        return compact('success');
    }
    public function phase($wargame = "MainWargame")
    {
        $user = $this->session->userdata("user");
        $x = $this->input->post('x',FALSE);
        $y = $this->input->post('y',FALSE);
        echo "X: $x Y: $y";
        $this->load->model("wargame/wargame_model");
        echo "loaded";
        $doc = $this->wargame_model->getDoc(urldecode($wargame));

        require_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleForAllenCreek.php");
        $battle = new BattleForAllenCreek($doc->wargame);
        var_dump($battle->force->units[5]);
        echo "kkk";
        $mapGrid = new MapGrid($battle->mapData);
        $mapGrid->setPixels($x, $y);
        echo "HIeeI $x $y ";var_dump($mapGrid->getHexagon()->number);echo "Hexed";
        $battle->gameRules->processEvent(SELECT_BUTTON_EVENT, "next_phase", 0,0 );

        echo "jjjjjjjwwwwjjjjjjj";
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
    }

   public function unitInit($wargame = "MainWargame",$unit = null)
    {
        $user = $this->session->userdata("user");
        $chat = $this->input->post('chat',TRUE);
        $this->load->model("wargame/wargame_model");
        $doc = $this->wargame_model->getDoc(urldecode($wargame));
        require_once("/Documents and Settings/Owner/Desktop/webwargaming/BattleForAllenCreek.php");
        $battle = new BattleForAllenCreek();
        $doc->wargame = $battle->save();
        $doc = $this->wargame_model->setDoc($doc);

        echo "HII $unit";
        //        	$battle->gameRules->processEvent(SELECT_COUNTER_EVENT, $unit, $battle->force->getUnitHexagon($umit));
        //        $myBattle = $battle->save();
        //        $jBattle = json_encode($myBattle);
        //        //    $jBattle = preg_replace("/{/","{\n",$jBattle);
        //        //    $jBattle = preg_replace("/}/","\n}",$jBattle);
        //        file_put_contents("afile.out", $jBattle);

        return compact('success');
    }
    public function createWargame()
    {
        $wargame = $this->input->post('wargame');
        if($wargame){
            $this->load->model("wargame/wargame_model");
            $this->wargame_model->createWargame($wargame);
            redirect("/wargame/changewargame/$wargame");
        }
        $this->load->view("wargame/wargameCreate");
    }
    public function clock()
    {

        while (true) {
            $date = date("h:i:s A");
            echo "HI";
            $doc = $this->couchsag->get("MainWargame");
            $doc->clock = $date;
            $success = $this->couchsag->update($doc->_id, $doc);
            sleep(1);die();
        }
    }
}
