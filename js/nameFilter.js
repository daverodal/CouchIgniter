alert("YHI");lobby = Array();lobby["map"] =
function(doc) {
    if(doc.docType == 'game' || doc.docType == 'lobby'){
    var ret = 0;

    if(doc.users){
    for(var i = 0;i < doc.users.length;i++){
    emit([doc.docType,doc._id,doc.users[i]],1);
    }
    if(doc.users.length == 0){
        emit([doc.docType,doc._id],null,0);
    }
}
emit([doc.docType,doc._id,doc.name],ret);
}
};
alert("A");