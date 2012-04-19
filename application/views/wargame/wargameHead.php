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
            float: left;
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
                    {url: "<?=site_url("wargame/fetch/") . "/";?>" + last_seq,
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
    x.register("gameRules", function(gameRules) {
//            alert(gameRules.turn);
            turn = gameRules.turn;
            var pix = turn  + (turn - 1) * 36 + 1;
            $("#turnCounter").css("left",pix+"px");
        if(gameRules.attackingForceId == 1){
            $("#turnCounter").css("background","#9ff");
        }else{
            $("#turnCounter").css("background","rgb(255,204,153)");

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
    x.register("force", function(force) {
//        if(this.animate !== false){
//            self.clearInterval(this.animate);
//            this.animate = false;
//            $("#"+this.animateId).stop(true);
//        }
        var units = force.units;

        for (i in units) {
            color = "transparent";
            switch(units[i].status){
                case 1:
                case 2:
                    if(units[i].forceId === force.attackingForceId){

                    color = "green";
            }
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
            $(".col"+combatCol).css({background:"transparent"});
//            $(".odd .col"+combatCol).css({color:"white"});
//            $(".even .col"+combatCol).css({color:"black"});

        }
        var title = "Combat Results ";
        str = ""
        if(combatRules ){
            cD = combatRules.currentDefender;
            if(cD !== false){
                if(combatRules.combats){

                    $("#"+cD).css({borderColor: "#333"});
//                    $("#"+cD+"").animate({borderColor: "#333"}, 1400).animate({borderColor: "white"}, 1400);

//                   this.animate =self.setInterval(function(){
//                           this.animateid = cD;
//                            $("#"+cD+"").animate({borderColor: "#333"}, 1400).animate({borderColor: "white"}, 1400);
//
//                        }
//
//                        ,3000);

//                $("#"+cD).everyTime(3,function(){
//                        alert("hi");
//                    }
//                );
                if(Object.keys(combatRules.combats[cD].attackers).length != 0){
            combatCol = combatRules.combats[cD].index + 1;
            $(".col"+combatCol).css('background-color',"rgba(255,255,1,.6)");
                    if(combatRules.combats[cD].Die !== false){
                        $(".row"+combatRules.combats[cD].Die+" .col"+combatCol).css('font-size',"110%");
                        $(".row"+combatRules.combats[cD].Die+" .col"+combatCol).css('background',"#eee");
                    }

//                $(".odd .col"+combatCol).css('color',"white");
//                $(".even .col"+combatCol).css('color',"black");
                      for(i in combatRules.combats){
                        if(combatRules.combats[i].Die){
                            str += " Die "+combatRules.combats[i].Die + " result "+combatRules.combats[i].combatResult;
                        }
                        if(combatRules.combats[i].index !== null){
                            str += "Defendeer "+i+" A "+combatRules.combats[i].attackStrength+" - D "+combatRules.combats[i].defenseStrength+ " - T "+combatRules.combats[i].terrainCombatEffect+ " = "+combatRules.combats[i].index;
                            str += "<br>";
                        }

                    }
                    attackers = combatRules.combats[cD].attackers;
                for(var i in attackers){
                    $("#"+i).css({borderColor: "crimson"});

                }
            }
 
            }
             }
            if(combatRules.combatsToResolve){
                if(combatRules.lastResolvedCombat){
                    title += "<strong style='margin-left:20px;font-size:150%'>"+combatRules.lastResolvedCombat.combatResult+"</strong>";
                }
                str += "Combats to Resolve<br>";
                if(Object.keys(combatRules.combatsToResolve) == 0){
                    str += "there are no combats to resolve<br>";
                }
                for(i in combatRules.combatsToResolve){
                    if(combatRules.combatsToResolve[i].Die){
                        str += " Die "+combatRules.combatsToResolve[i].Die + " result "+combatRules.combatsToResolve[i].combatResult;
                    }
                    if(combatRules.combatsToResolve[i].index !== null){
                        str += "Defendeer "+i+" A "+combatRules.combatsToResolve[i].attackStrength+" - D "+combatRules.combatsToResolve[i].defenseStrength+ " - T "+combatRules.combatsToResolve[i].terrainCombatEffect+ " = "+combatRules.combatsToResolve[i].index;
                        str += "<br>";
                    }

                }
                str += "Resolved Combats<br>";
              for(i in combatRules.resolvedCombats){
                    if(combatRules.resolvedCombats[i].Die){
                        str += " Die "+combatRules.resolvedCombats[i].Die + " result "+combatRules.resolvedCombats[i].combatResult;
                    }
                    if(combatRules.resolvedCombats[i].index !== null){
                        str += "Defendeer "+i+" A "+combatRules.resolvedCombats[i].attackStrength+" - D "+combatRules.resolvedCombats[i].defenseStrength+ " - T "+combatRules.resolvedCombats[i].terrainCombatEffect+ " = "+combatRules.resolvedCombats[i].index;
                        str += "<br>";
                    }

                }
            }

            $("#status").html(str);
//            alert(attackers);

        }
        $("#crt h3").html(title);
    });

    x.fetch(0);

    function doit() {
        var mychat = $("#mychat").attr("value");
        $.ajax({url: "<?=site_url("wargame/add/");?>",
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
        $.ajax({url: "<?=site_url("wargame/unit");?>/"+id,
            type: "POST",
            data:{unit:id,
            },
            success:function(data, textstatus) {
            }
        });
        $("#mychat").attr("value", "");
    }
    function doitMap(x,y) {
        $.ajax({url: "<?=site_url("wargame/map/");?>/",
            type: "POST",
            data:{x:x,
                y:y
            },
            success:function(data, textstatus) {
            }
        });

    }
    function doitNext() {
        $.ajax({url: "<?=site_url("wargame/phase/");?>/",
            type: "POST",

            success:function(data, textstatus) {
            }
        });

    }

    </script>
</head>