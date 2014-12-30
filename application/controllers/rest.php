<?php

/**
 * Created by PhpStorm.
 * User: david
 * Date: 6/14/14
 * Time: 9:25 PM
 */
class Rest extends CI_Controller
{
    private $prevDB;
    private function _setDB()
    {
        $this->prevDB = $this->couchsag->sag->currentDatabase();
        $this->couchsag->sag->setDatabase('rest');

    }

    private function _restoreDB()
    {
        $this->couchsag->sag->setDatabase($this->prevDB);
    }


    public function __construct()
    {
        parent::__construct();
        $user = $this->session->userdata("user");
        if (!$user) {
            redirect("/users/login/");
        }
        $this->load->model('rest/rest_model');
    }

    function purge($stuf)
    {
        return;
        $this->_setDB();
        $ret = $this->couchsag->get("/_design/newFilter/_view/allGames");
        $rows = $ret->rows;
        foreach($rows as $row){
            var_dump($row);
            $doc = $this->couchsag->get($row->id);
            echo "Rev ".$doc->_rev."<br>";
            $this->couchsag->delete($doc->_id, $doc->_rev);
        }
        die('love');
        $seq = $this->rest_model->getMaps();
        $rows = $seq->rows;
        foreach ($rows as $key => $val) {
            $id = $val->value->_id;
            $rev = $val->value->_rev;
            echo "id $id rev $rev<br>";
            $this->couchsag->delete($id, $rev);
        }

        $seq = $this->rest_model->getHexStrs();
        $rows = $seq->rows;
        foreach ($rows as $key => $val) {
            $id = $val->value->_id;
            $rev = $val->value->_rev;
            echo "id $id rev $rev<br>";
            $this->couchsag->delete($id, $rev);
        }

    }

    function index()
    {
    }


    function fixMaps($doFix)
    {


        $seq = $this->rest_model->getMaps();
//       var_dump($seq->rows);
        $rows = $seq->rows;
        $maps = [];
        foreach ($rows as $key => $val) {
            $map = $val->value->map;
            echo "checking ".$val->value->_id." ";
            $hexStrDoc = $this->rest_model->get($map->hexStr);

            if($hexStrDoc->hexStr->map !== $val->id){
                echo "BAD BAD BAD ";
                $hexStrDoc->hexStr->map = $val->id;
                if($doFix){
                    $ret = $this->rest_model->update($hexStrDoc);
                    echo "Fixing ";
                    if($ret->ok == true){
                        echo "Fixed ";
                    }
                }
                echo "<br>";
            }else{
                echo "okay<br>";
            }
        }
    }

    function migrate()
    {
        return;
        define("MAPROOT", "/Users/david/maproot");
        $modelName = "maps";
        $singleModel = "map";
        $top = new stdClass();
        $ret = array();
        $dirs = glob(MAPROOT . "/$modelName/*");
        if (count($dirs) >= 0) {

            /* This is the correct way to loop over the directory. */
            foreach ($dirs as $entry) {
                var_dump($entry);
                if (preg_match("/\/_id$/", $entry)) {
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

    function initDoc()
    {
        $this->_setDB();

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
            $this->rest_model->create($data);
        } catch (Exception $e) {
        }


        $this->_restoreDB();

    }

    function maps($stuf = false)
    {
        $req = $_SERVER['REQUEST_METHOD'];
        if ($req == 'GET') {
            $this->_mapGet($stuf);
        }
        if ($req == 'POST') {
            $this->_mapPost($stuf);
        }
        if ($req == 'PUT') {
            $this->_mapPut($stuf);
        }
        if ($req == 'DELETE') {
            $this->_mapDelete($stuf);
        }
    }

    function _mapPost($stuff)
    {
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $data = new stdClass();
        $data->docType = "hexMapData";
        $data->map = $postData->map;
        $ret = $this->rest_model->create($data);
        $ret->body->id;
        $postData->map->id = $ret->body->id;
        echo json_encode($postData);
    }

    function _mapPut($stuff)
    {
        $doc = $this->rest_model->get($stuff);
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $doc->map = $postData->map;
        $ret = $this->rest_model->update($doc);
        $postData->map->id = $stuff;
        echo json_encode($postData);
    }


    function _mapDelete($docId)
    {
        $this->rest_model->delete($docId);
        echo json_encode(new stdClass());
    }

    function _mapGet()
    {

        $seq = $this->rest_model->get("/_design/restFilter/_view/getMaps");
//       var_dump($seq->rows);
        $rows = $seq->rows;
        $maps = [];
        foreach ($rows as $key => $val) {
            $map = $val->value->map;
            $map->id = $val->key;
            $maps[] = $map;
        }
        echo json_encode(['maps' => $maps]);
    }

    function cloneFile($stuff)
    {
        echo "Yo! ";
        $doc = $this->rest_model->get($stuff);
        var_dump($doc);
        $this->_setDB();
        if ($doc->docType == "hexMapData") {
            echo "ok";
            unset($doc->_id);
            unset($doc->_rev);
            $hexStr = $doc->map->hexStr;
            $doc->map->hexStr = "";
            $ret = $this->couchsag->create($doc);
            $mapId = $ret->body->id;
            $mapRev = $ret->body->rev;
            if ($ret->body->ok === true) {
                echo "good ";
                if ($hexStr) {
                    echo "better '";
                    $hexDoc = $this->rest_model->get($hexStr);
                    unset($hexDoc->_id);
                    unset($hexDoc->_rev);
                    $hexDoc->hexStr->map = $mapId;
                    $hexRet = $this->couchsag->create($hexDoc);
                    if ($hexRet->body->ok) {
                        echo "Best";
                        $doc->_id = $mapId;
                        $doc->_rev = $mapRev;
                        $doc->map->hexStr = $hexRet->body->id;
                        var_dump($this->couchsag->update($doc->_id, $doc));
                        echo "BFF forever ";
                    }
                }
            }
        }
        $this->_restoreDB();
    }


    function hexStrs($stuf = false)
    {
        $req = $_SERVER['REQUEST_METHOD'];
        if ($req == 'GET') {
            $this->hexStrGet($stuf);
        }
        if ($req == 'POST') {
            $this->hexStrPost($stuf);
        }
        if ($req == 'PUT') {
            $this->hexStrPut($stuf);
        }
        if ($req == 'DELETE') {
            $this->hexStrDelete($stuf);
        }
    }

    function hexStrPost($stuff)
    {
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $data = new stdClass();
        $data->docType = "hexMapStrs";
        $data->hexStr = $postData->hexStr;
        $ret = $this->rest_model->create($data);
        $ret->body->id;
        $postData->hexStr->id = $ret->body->id;
        echo json_encode($postData);
    }

    function hexStrPut($stuff)
    {
        $doc = $this->rest_model->get($stuff);
        $putdata = file_get_contents("php://input", "r");

        $postData = json_decode($putdata);
        $doc->hexStr = $postData->hexStr;
        $postData->hexStr->id = $stuff;
        $ret = $this->rest_model->update($doc);
        echo json_encode($postData);
    }

    function hexStrGet($stuf)
    {
        if ($stuf) {
            $doc = $this->rest_model->get($stuf);
            $doc->hexStr->id = $stuf;
            echo json_encode(['hexStr' => $doc->hexStr]);
            return;
        }
        $seq = $this->rest_model->getHexStrs();
        $rows = $seq->rows;
        $hexStrs = [];
        foreach ($rows as $key => $val) {
            $hexStr = $val->value->hexStr;
            $hexStr->id = $val->key;
            $hexStrs[] = $hexStr;
        }
        echo json_encode(['hexStrs' => $hexStrs]);
    }

    function hexStrDelete($docId)
    {
        $this->rest_model->delete($docId);
        echo json_encode(new stdClass());
    }
}