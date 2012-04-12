<head>
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
    <link href="<?=base_url("js/jquery-ui.css");?>" rel="stylesheet" type="text/css"/>
    <script src="<?=base_url("js/jquery.min.js");?>"></script>
    <script src="<?=base_url("js/jquery-ui.min.js");?>"></script>
    <script src="<?=base_url("js/form.js");?>"></script>
    <style type="text/css">
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

        #clock {
            font-size: 16px;
            float: left
        }

        #users {
            float: left;
        }
        #chats li{
            float:left;
            clear:both;
            background:pink;
            list-style: none;
            border-radius: 10px;
            padding:0 10px;
            border: 2px #eee solid;
            color:#999;
        }
        #chats li span{
            color:#333;
        }
        .unit {border:2px solid blue;}
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
                    {url: "<?=site_url("wargame/fetch/$wargame") . "/";?>" + last_seq,
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
            str = str.replace(/:/,":<span>");
            str = str.replace(/$/,"</span>");
            $("#chats").prepend(str);
        }
    });
    x.register("users", function(users) {
        var str;
        $("#users").html("");
        for (i in users) {
            str = "<li>" + users[i] + "</li>";
            $("#users").append(str);
        }
    });
    x.register("games", function(games) {
        var str;
        $("#games").html("");
        for (i in games) {
            str = "<li>" + games[i] + "</li>";
            $("#games").append(str);
        }
    });
    x.register("clock", function(clock) {
        $("#clock").html(clock);
    });
    x.register("mapUnits", function(mapUnits) {
        var str;
        for (i in mapUnits) {
            width = $("#"+i).width();
            height = $("#"+i).height();
            $("#"+i).css({left: -1+mapUnits[i].x-width/2+"px",top:-1+mapUnits[i].y-height/2+"px"});
        }
    });
    x.register("moveRules", function(moveRules) {
        var str;
        $("#status").html("");
        if(moveRules.anyUnitIsMoving){
            $("#status").html("Unit #:"+moveRules.movingUnitId+" is currently moving");
//            alert($("#"+moveRules.movingUnitId).css('opacity',.5));
        }
    });
    x.register("units", function(units) {
        for (i in units) {
            color = "green";
            switch(units[i].status){
                case 1:
                case 2:
                    color = "green";
                    break;
                case 3:
                case 4:
                    color = "orange";
                    break;
                case 6:
                    color = "black";
                    break;
//                case 8:
                case 9:
                    color = "DarkRed";
                        break;
                case 13:
                    color = "purple";
                    break;
                case 14:
                    color = "yellow";
                    break;
                case 16:
                    color = "pink";
                    break;
                case 17:
                    color = "cyan";
                    break;
            }
            $("#"+i).css({borderColor: color});
        }
    });
    x.register("combatRules", function(combatRules) {
        for(var combatCol = 1;combatCol <= 6;combatCol++){
            $(".col"+combatCol).css({background:"#333"});

        }
        if(combatRules && combatRules.currentDefender !== false){
            cD = combatRules.currentDefender;
            if(cD !== false && Object.keys(combatRules.combats[cD].attackers).length != 0){
            combatCol = combatRules.combats[cD].index + 1;
            $(".col"+combatCol).css({background:"#666"});
            }
            $("#"+cD).css({borderColor: "white"});
            attackers = combatRules.combats[cD].attackers;
            for(var i in attackers){
//                alert(i);
//                alert(attackers[i]);
                $("#"+i).css({borderColor: "crimson"});

            }
            str = ""
            for(i in combatRules.combats){
                str += "Combat "+i+" has "+combatRules.combats[i].index+"<br>";
            }
            $("#status").html(str);
//            alert(attackers);

        }
    });

    x.fetch(0);

    function doit() {
        var mychat = $("#mychat").attr("value");
        $.ajax({url: "<?=site_url("wargame/add/$wargame");?>",
            type: "POST",
            data:{chat:mychat,
            },
            success:function(data, textstatus) {
            }
        });
        $("#mychat").attr("value", "");
    }
    function doitUnit(id) {
        var mychat = $("#mychat").attr("value");
        $.ajax({url: "<?=site_url("wargame/unit/MainWargame");?>/"+id,
            type: "POST",
            data:{unit:id,
            },
            success:function(data, textstatus) {
            }
        });
        $("#mychat").attr("value", "");
    }
    function doitMap(x,y) {
        $.ajax({url: "<?=site_url("wargame/map/MainWargame");?>/",
            type: "POST",
            data:{x:x,
                y:y
            },
            success:function(data, textstatus) {
            }
        });

    }
    function doitNext() {
        $.ajax({url: "<?=site_url("wargame/phase/MainWargame");?>/",
            type: "POST",

            success:function(data, textstatus) {
            }
        });

    }

    </script>
</head>