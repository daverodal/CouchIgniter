function Sync(baseUrl) {
    this.baseUrl = baseUrl
    this.id = "Sync";
    this.callbacks = Object;
    this.lengths = {};
this.fetchTimes =  [];
this.animate = false;
this.register = function(name, callback) {
    this.callbacks[name] = callback;
    this.lengths[name] = 0;
    }
this.fetch = function(last_seq, args) {
    var chatsIndex = 0;
    var theArgs = {};
if (args) {
    chatsIndex = parseInt(args.chatsIndex);
    theArgs = args;
    }
that = this;
$.ajax(
                    {url: this.baseUrl+"/" + last_seq,
                        type:"POST",
                        data:theArgs,
                        success:function(data, textstatus) {
                        var now = ((new Date()).getTime()) /1000;
                        that.fetchTimes.push(now);
                        if(that.fetchTimes.length > 10){
                        var then = that.fetchTimes.shift();
                        if((now - then) < 2){
                        $("#comlink").html("Comlink Down, Try refreshing Page");
                        return;
                        }
}
fetchArgs = {};
for (var i in that.callbacks) {
    if (data[i]) {
    if ($.isArray(data[i])) {
    var lastlength = that.lengths[i];
    //that.lengths[i] = data[i].length;
    data[i].splice(0, lastlength);
    }
if (true) {
    /*var prev = that.cache[i];
     that.cache[i] = data[i].length;
     if(prev)
     data[i] = data[i].splice(0,prev);*/
    }
that.callbacks[i](data[i]);
if (data[i + "Index"]) {
    fetchArgs[i + "Index"] = data[i + "Index"];
    }
}
}
/*if(data.moves){
    if(last_seq != 0){
    pos = data.moves[data.moves.length-1];
    }else{
    pos = data.lastmove;
    }
if(pos.city){
    x = pos.city.x;
    y = pos.city.y;
    $("#city").animate({
    left:x+"px",
    top:y+"px",
    },0700);
}
if(pos.army){
    x = pos.army.x;
    y = pos.army.y;
    $("#draggable").animate({
    left:x+"px",
    top:y+"px",
    },0700);
}
checkCity(data.lastmove);
last_seq = data.last_seq;
}else{alert("help"+data);}*/
last_seq = data.last_seq;
var msg = '<span title="'+last_seq+'">Working</span>';
$("#comlink").html(msg);
//                            $("#comlink").html("<span title='"+last_seq+">Working</span>");
                            that.fetch(last_seq, fetchArgs);
                        },
                        complete:function(jq, textstatus) {
                            var now = ((new Date()).getTime()) /1000;
                            that.fetchTimes.push(now);
                            if(that.fetchTimes.length > 10){
                                var then = that.fetchTimes.shift();
                                if((now - then) < 2){
                                    $("#comlink").html("Comlink Down, Try refreshing Page");
                                    return;
                                }
                            }

                            if (textstatus != "success")that.fetch(0);
                        }
                    });
        }
    }