<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 6/14/14
 * Time: 9:25 PM
 */

class Rest extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/users/login/");
        }
    }

    function purge($stuf){
        if($stuf){
        }
        $seq = $this->couchsag->get("/_design/restFilter/_view/getMaps");
        $rows = $seq->rows;
        foreach($rows as  $key => $val){
            $id = $val->value->_id;
            $rev = $val->value->_rev;
            echo "id $id rev $rev<br>";
            $this->couchsag->delete($id, $rev);
        }

        $seq = $this->couchsag->get("/_design/restFilter/_view/getHexStrs");
        $rows = $seq->rows;
        foreach($rows as  $key => $val){
            $id = $val->value->_id;
            $rev = $val->value->_rev;
            echo "id $id rev $rev<br>";
            $this->couchsag->delete($id, $rev);
        }

    }

    function index(){
    }

    function migrate(){
        define("MAPROOT","/Users/david/maproot");
        $modelName = "maps";
        $singleModel = "map";
        $top = new stdClass();
        $ret = array();
        $dirs = glob(MAPROOT."/$modelName/*");
        if (count($dirs) >= 0) {

            /* This is the correct way to loop over the directory. */
            foreach ($dirs as $entry) {
                var_dump($entry);
                if(preg_match("/\/_id$/",$entry)){
                    continue;
                }
                $model = json_decode(file_get_contents($entry));
                $matches = array();
                unset($model->map->id);
                $hexes = $model->map->hexes;
                unset($model->map->hexes);
                $data = new stdClass();
                $data->docType = "hexMapData";
                $data->map = $model->map;
                $ret = $this->couchsag->create($data);
                $mapId = $ret->body->id;
                $hexStr = new stdClass();
                $hexStr->docType = "hexMapStrs";
                $hexStr->hexStr = new stdClass();
                $hexStr->hexStr->map = $mapId;
                $hexStr->hexStr->hexEncodedStr = $hexes;
                $ret = $this->couchsag->create($hexStr);
                $hexStrId = $ret->body->id;
                $doc = $this->couchsag->get($mapId);
                $doc->map->hexStr = $hexStrId;
                $this->couchsag->update($doc->_id, $doc);
            }
        }
    }

    function initDoc(){

        $views = new stdClass();
        $views->getMaps = new stdClass();
        $views->getMaps->map = "     function(doc) {
            if(doc.docType == 'hexMapData'){

                emit(doc._id, doc);
            }
        }";
        $views->getHexStrs = new stdClass();
        $views->getHexStrs->map = "     function(doc) {
            if(doc.docType == 'hexMapStrs'){

                emit(doc._id, doc);
            }
        }";
        $data = array("_id" => "_design/restFilter", "views" => $views);
        try {
            $doc = $this->couchsag->get("_design/restFilter");
        } catch (Exception $e) {
        };
        if ($doc) {
            echo "Doc Found deleting: _design/restFilter\n";
            $deldoc = $this->couchsag->delete($doc->_id, $doc->_rev);
            if ($deldoc->body) {
                echo "Deleted\n";
            }
        }
        try {
            $this->couchsag->create($data);
        } catch (Exception $e) {
            echo "<pre> EXC";
            var_dump($e);
        }





    }
    function maps($stuf = false ){
        $req = $_SERVER['REQUEST_METHOD'];
        if($req == 'GET'){
            return $this->mapGet($stuf);
        }
        if($req == 'POST'){
            return $this->mapPost($stuf);
        }
        if($req == 'PUT'){
            return $this->mapPut($stuf);
        }
    }

    function mapPost($stuff){
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $data = new stdClass();
        $data->docType = "hexMapData";
        $data->map = $postData->map;
        $ret = $this->couchsag->create($data);
        $ret->body->id;
        $postData->map->id = $ret->body->id;
        echo json_encode($postData);
    }

    function mapPut($stuff){
        $doc = $this->couchsag->get($stuff);
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $doc->map = $postData->map;
        $ret = $this->couchsag->update($doc->_id, $doc);
        $postData->map->id = $stuff;
        echo json_encode($postData);
    }

   function mapGet($stuf){
       if($stuf){
       }
       $seq = $this->couchsag->get("/_design/restFilter/_view/getMaps");
//       var_dump($seq->rows);
       $rows = $seq->rows;
       $maps = [];
       foreach($rows as  $key => $val){
           $map = $val->value->map;
           $map->id = $val->key;
           $maps[] = $map;
       }
       echo json_encode(['maps'=>$maps]);
   }





    function hexStrs($stuf = false ){
        $req = $_SERVER['REQUEST_METHOD'];
        if($req == 'GET'){
            return $this->hexStrGet($stuf);
        }
        if($req == 'POST'){
            return $this->hexStrPost($stuf);
        }
        if($req == 'PUT'){
            return $this->hexStrPut($stuf);
        }
    }

    function hexStrPost($stuff){
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $data = new stdClass();
        $data->docType = "hexMapStrs";
        $data->hexStr = $postData->hexStr;
        $ret = $this->couchsag->create($data);
        $ret->body->id;
        $postData->hexStr->id = $ret->body->id;
        echo json_encode($postData);
    }

    function hexStrPut($stuff){
        $doc = $this->couchsag->get($stuff);
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $doc->hexStr = $postData->hexStr;
        $postData->hexStr->id = $stuff;
        $ret = $this->couchsag->update($doc->_id, $doc);
        echo json_encode($postData);
    }

    function hexStrGet($stuf){
        if($stuf){
            $doc = $this->couchsag->get($stuf);
            $doc->hexStr->id = $stuf;
            echo json_encode(['hexStr'=>$doc->hexStr]);
            return;
        }
        $seq = $this->couchsag->get("/_design/restFilter/_view/getHexStrs");
//       var_dump($seq->rows);
        $rows = $seq->rows;
        $hexStrs = [];
        foreach($rows as  $key => $val){
            $hexStr = $val->value->hexStr;
            $hexStr->id = $val->key;
            $hexStrs[] = $hexStr;
        }
        echo json_encode(['hexStrs'=>$hexStrs]);
    }
}