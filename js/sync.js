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
        if(this.timeBranch){
          travel += "&branch=true";
          this.timeBranch = false;
          this.timeTravel = false;
        }
        this.current = $.ajax(
            {url:this.baseUrl + "/" + last_seq+travel,
                type:"POST",
                data:theArgs,

                error:function(jqXHR, two, three){
                        console.log("error");
//                    jqXHR.abort();
                },
                success:function (data, textstatus, jqXHR) {
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
                    /* bleh ajax will automagically forward on 301's and 302's not letting me know
                     * if we got logged out. The only way I know is the object is no longer an object.
                     * So we redirect then
                     */
                    if(!(typeof data == "object" && data !== null)){
                        /* make em login */
                        window.location = 'login';
                    }
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

                    if (textstatus != "success" && !that.timeTravel){
                        that.fetch(0);
                    }
                }
            });
    }
}