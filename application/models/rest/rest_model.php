<?php
/**
 *
 * Copyright 2011-2015 David Rodal
 *
 *  This program is free software; you can redistribute it
 *  and/or modify it under the terms of the GNU General Public License
 *  as published by the Free Software Foundation;
 *  either version 2 of the License, or (at your option) any later version
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class Rest_model extends CI_Model
{
    private function _setDB()
    {
        $this->prevDB = $this->couchsag->currentDatabase();
        $dbName = $this->config->item('rest_db');

        $this->couchsag->setDatabase($dbName);

    }

    private function _restoreDB()
    {
        $this->couchsag->setDatabase($this->prevDB);
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
