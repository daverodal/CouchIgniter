<?php

class Rest_model extends CI_Model
{
    private function _setDB()
    {
        $this->prevDB = $this->couchsag->sag->currentDatabase();
        $this->couchsag->sag->setDatabase('rest');

    }

    private function _restoreDB()
    {
        $this->couchsag->sag->setDatabase($this->prevDB);
    }

    public function getMaps()
    {
        $this->_setDB();
        $ret = $this->couchsag->get("/_design/restFilter/_view/getMaps");
        $this->_restoreDB();
        return $ret;
    }

    public function getHexStrs()
    {
        $this->_setDB();
        $ret = $this->couchsag->get("/_design/restFilter/_view/getHexStrs");
        $this->_restoreDB();
        return $ret;
    }

    public function get($docId){
        $this->_setDB();
        $ret = $this->couchsag->get($docId);
        $this->_restoreDB();
        return $ret;
    }

    public function update($doc){
        $this->_setDB();
        $ret = $this->couchsag->update($doc->_id, $doc);
        $this->_restoreDB();
        return $ret;
    }


    public function create($doc){
        $this->_setDB();
        $ret = $this->couchsag->create($doc);
        $this->_restoreDB();
        return $ret;
    }

    public function delete($docId){
        $this->_setDB();
        $doc = $this->couchsag->get($docId);
        $ret = $this->couchsag->delete($doc->_id, $doc->_rev);
        $this->_restoreDB();
        return $ret;
    }

}
