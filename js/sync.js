function Sync(baseUrl) {
    this.baseUrl = baseUrl
    this.id = "Sync";
    this.callbacks = Object;
    this.lengths = {};
    this.timeTravel = false;
    this.fetchTimes = [];
    this.animate = false;
    this.register = function (name, callback) {
        this.callbacks[name] = callback;
        this.lengths[name] = 0;
    }
    this.fetch = function (last_seq, args) {
        var chatsIndex = 0;
        var theArgs = {};
        if (args) {
            chatsIndex = parseInt(args.chatsIndex);
            theArgs = args;
        }
        that = this;
        var travel = "";
        if(this.timeTravel){
            travel = "?timeTravel="+last_seq;
        }
        this.current = $.ajax(
            {url:this.baseUrl + "/" + last_seq+travel,
                type:"POST",
                data:theArgs,
                error:function(one, two, three){
                    one.abort();
                },
                success:function (data, textstatus) {
                    var now = ((new Date()).getTime()) / 1000;
                    that.fetchTimes.push(now);
                    if (that.fetchTimes.length > 10) {
                        var then = that.fetchTimes.shift();
                        if ((now - then) < 2) {
                            $("#comlink").html("Comlink Down, Try refreshing Page");
                            return;
                        }
                    }
                    fetchArgs = {};
                    for (var i in that.callbacks) {
                        if (data[i]) {
                            if ($.isArray(data[i])) {
                                var lastlength = that.lengths[i];
                                data[i].splice(0, lastlength);
                            }
                            that.callbacks[i](data[i],data);
                            if (data[i + "Index"]) {
                                fetchArgs[i + "Index"] = data[i + "Index"];
                            }
                        }
                    }
                    last_seq = data.last_seq;
                    var msg = '<span title="' + last_seq + '">Working</span>';
                    $("#comlink").html(msg);
                    if(!that.timeTravel){
                        that.fetch(last_seq, fetchArgs);
                    }
                },
                complete:function (jq, textstatus) {
                    var now = ((new Date()).getTime()) / 1000;
                    that.fetchTimes.push(now);
                    if (that.fetchTimes.length > 10) {
                        var then = that.fetchTimes.shift();
                        if ((now - then) < 2) {
                            $("#comlink").html("Comlink Down, Try refreshing Page");
                            return;
                        }
                    }

                    if (textstatus != "success")that.fetch(0);
                }
            });
    }
}