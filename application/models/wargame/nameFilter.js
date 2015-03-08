<script type="text/javascript">
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
lobby.map =
function(doc) {
    if(doc.docType == 'game' || doc.docType == 'lobby'){
        var ret = 0;

        if(doc.users){
            for(var i = 0;i < doc.users.length;i++){
                emit([doc.docType,doc._id,doc.users[i]],1);
            }
            if(doc.users.length == 0){
                emit([doc.docType,doc._id,null,0);
            }
        }
        emit([doc.docType,doc._id,doc.name],ret);
    }
};
</script>