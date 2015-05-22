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
        if(this.timeFork){
            travel += "&fork=true";
            this.timeFork = false;
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
                        /* get way out of app. to root */
                        window.location = '/';
                    }
                    /* detect if logged out and return forward data packet */
                    if(data.forward){
                        window.location = data.forward;
                        return;
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