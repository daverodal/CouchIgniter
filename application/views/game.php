<!doctype html>
<html>
<head>
<style type="text/css">
    @font-face {
        font-family: Military;
        src: url('/~drodal/ci/Maparmy.ttf');
    }

    .mil {
        font-family: Military;
        font-size: 22px;
    }
</style>
<link href="<?php echo base_url();?>js/jquery-ui.css" rel="stylesheet" type="text/css"/>
<script src="<?php echo base_url();?>js/jquery.min.js"></script>
<script src="<?php echo base_url();?>js/jquery-ui.min.js"></script>
<style type="text/css">
    #yourside {
        width: 33%;
        float: left;
    }

    #themap {
        width: 33%;
        border: 1px solid blue;
        height: 400px;
        float: left;
        position: relative;
    }

    #themap div {
        position: absolute;
        width: 50%;
        height: 100%;
        top: 0px;
    }

    #themap #battle0 {
        left: 0px;
        text-align: right;
    }

    #themap #battle1 {
        right: 0px;
        text-align: left;
    }

    #draggable {
        border: 0px solid red;
        z-index: 100;
        position: absolute;
        width: 50px;
        height: 50px;
        background: silver;
    }

    #city {
        background-color: red;
        border: 0px solid red;
        position: absolute;
        top: 300px;
        width: 100px;
        height: 100px;
    }

    #city div {
        background: #ccf;
        height: 100px;
    }

    #army li, #enemy li, #building .bui {
        float: left;
    }

    #building .bui, #battle .bui {
        position: relative;
        width: 30px;
        text-align: center;
    }

    #clock {
        font-size: 120px;
    }
</style>
<script>function Sync(arg) {
    this.id = "Sync";
    this.callbacks = Object;
    this.lengths = {};
    this.register = function(name, callback) {
        this.callbacks[name] = callback;
        this.lengths[name] = 0;
    }
    this.fetch = function(last_seq, args) {
        var chatsIndex = 0;
        var theArgs = {}
        if (args) {
            chatsIndex = parseInt(args.chatsIndex);
            theArgs = args;
        }
        that = this;
        $.ajax(
                {url: "/~drodal/ci/index.php/game/fetch/" + last_seq,
                    type:"POST",
                    data:theArgs,
                    success:function(data, textstatus) {
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
                        that.fetch(last_seq, fetchArgs);
                    },
                    complete:function(jq, textstatus) {
                        if (textstatus != "success")that.fetch(0);
                    }
                });
    }
}
x = new Sync("myArg");
x.register("chats", function(chats) {
    var str;
    for (i in chats) {
        str = "<li>" + chats[i] + "</li>";
        $("#chats").append(str);
    }
});
x.register("lose", function(lose) {
    alert("You have been defeated.");
});
x.register("win", function(win) {
    alert("You are Victorious!");
});
x.register("army", function(army) {
    var str;
    $("#army").html("");
    for (i in army) {
        str = "<li>!</li>";
        $("#army").append(str);
    }
});
x.register("enemy", function(enemy) {
    var str;
    $("#enemy").html("");
    for (i in enemy) {
        str = "<li>!</li>";
        $("#enemy").append(str);
    }
});
x.register("building", function(building) {
    var str;
    $("#building").html("");
    for (i in building) {
        //str = "<li ><span class='mil' >!</span><span style='display:block;background:red;width:"+building[i].hp+"'></span></li><span> </span>";
        str = "<div class='bui'><div class='mil' >!</div><div style='position:absolute;top:0px;left:0px;background:rgba(0,0,255,.3);height:1.2em;width:" + building[i].hp + "'></div></div>";
        $("#building").append(str);
    }
});
x.register("battle", function(battle) {
    var str;
    $("#battle").html("");
    for (i in battle) {
        for (j in battle[i]) {
            //str = "<li ><span class='mil' >!</span><span style='display:block;background:red;width:"+battle[i].hp+"'></span></li><span> </span>";
            str = "<div class='bui'><div class='mil' >!</div><div style='position:absolute;top:0px;left:0px;background:rgba(255,0,0,.3);height:1.2em;width:" + battle[i][j].hp + "'></div></div>";
            $("#battle" + i).append(str);
        }
    }
});
x.register("gold", function(gold) {
    $("#gold").html(gold);
});
x.register("mines", function(mines) {
    $("#mines").html(mines);
});
x.register("factories", function(factories) {
    $("#factories").html(factories);
});
x.register("clock", function(clock) {
    $("#clock").html(clock);
});
x.fetch(0);

function doit(type) {
    if (!type) {
        var mychat = $("#mychat").attr("value");
        $.ajax({url: "/~drodal/ci/index.php/game/add/",
            type: "POST",
            data:{chats:mychat},
            success:function(data, textstatus) {
            }
        });
    } else {
        $.ajax({url: "/~drodal/ci/index.php/game/add/",
            type: "POST",
            data:type,
            success:function(data, textstatus) {
            }
        });
    }
    $("#mychat").attr("value", "");
}

</script>
</head>
<body>
    <fieldset>
        <legend>Time
        </legend>
        <div id="clock"></div>
    </fieldset>
    <span class="mil">!"</span>
    <a href="/~drodal/ci/index.php/game/logout"/>logout</a>
    <button onclick="doit({army:true});return false;">Army</button>
    <button onclick="doit({mines:true});return false;">Mine</button>
    <button onclick="doit({factories:true});return false;">Factory</button>

    <form onsubmit="doit({chats:$('#mychat').attr('value')});return false;" id="chatform" method="post">
        <input id="mychat" name="chats" type="text">
        <input name="submit" type="submit">
    </form>
    <div id="yourside">
        <fieldset>
            <legend>Army
            </legend>
            <div id="army" class='mil'></div>
        </fieldset>
        <fieldset>
            <legend>Building
            </legend>
            <div id="building"></div>
        </fieldset>
        <fieldset>
            <legend>Chats
            </legend>
            <div id="chats"></div>
        </fieldset>
        <fieldset>
            <legend>Gold
            </legend>
            <div id="gold"></div>
        </fieldset>
        <fieldset>
            <legend>Mines
            </legend>
            <div id="mines"></div>
        </fieldset>
        <fieldset>
            <legend>Factories
            </legend>
            <div id="factories"></div>
        </fieldset>
    </div>
    <div id="themap">
        <div id="battle0"></div>
        <div id="battle1"></div>
    </div>
    <div id="theirside">
        <fieldset>
            <legend>Enemy
            </legend>
            <div id="enemy" class='mil'></div>
        </fieldset>
    </div>
</body>
