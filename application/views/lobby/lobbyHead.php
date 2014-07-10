<head>
    <link rel="shortcut icon" href="/favicon.ico" type="image/icon">
    <link href="<?=base_url("js/jquery-ui-1.11.0.css");?>" rel="stylesheet" type="text/css"/>
    <script src="<?=base_url("js/jquery-1.11.1.min.js");?>"></script>
    <script src="<?=base_url("js/jquery-ui-1.11.0.min.js");?>"></script>
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
            background:#1af;
            list-style: none;
            border-radius: 10px;
            padding:0 10px;
            border: 2px #eee solid;
            color:#333;
        }
        #chats li span{
            color:#333;
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
                    {url: "<?=site_url("lobby/fetch/$lobby") . "/";?>" + last_seq,
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
    x.fetch(0);

    function doit() {
        var mychat = $("#mychat").attr("value");
        $.ajax({url: "<?=site_url("lobby/add/$lobby");?>",
            type: "POST",
            data:{chat:mychat,
            },
            success:function(data, textstatus) {
            }
        });
        $("#mychat").attr("value", "");
    }

    </script>
</head>