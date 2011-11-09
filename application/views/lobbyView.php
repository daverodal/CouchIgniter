<!doctype html>
<html>
<head>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
  <style type="text/css">
    #draggable {border:0px solid red;z-index:100;position:absolute; width: 50px; height: 50px; background: silver; }
    #city {background-color:red;border:0px solid red;position:absolute;top:300px;width:100px;height:100px;}
	#city div{background:#ccf;height:100px;}
	#clock {font-size:16px;float:left}
  #users { float:left;}
  </style>
<script>function Sync(arg){
	this.id = "Sync";
	this.callbacks = Object;
	this.lengths = {};
	this.register = function(name, callback){
		this.callbacks[name] = callback;
		this.lengths[name] = 0;
	}
	this.fetch = function(last_seq, args){
	var chatsIndex = 0;
	var theArgs = {}
		if(args){
			 chatsIndex = parseInt(args.chatsIndex);
			theArgs = args;
		}
		that = this;
		  $.ajax(
		    {url: "/~drodal/ci/index.php/lobby/fetch/"+last_seq,
		    	type:"POST",
		    	data:theArgs,
		    	success:function(data,textstatus){
			    	fetchArgs = {};
			    	for(var i in that.callbacks){
						if(data[i]){
				    		if($.isArray(data[i])){
					    		var lastlength = that.lengths[i];
					    		//that.lengths[i] = data[i].length;
								data[i].splice(0,lastlength);
					    	}
							if(true){
								/*var prev = that.cache[i];
								that.cache[i] = data[i].length;
								if(prev)
									data[i] = data[i].splice(0,prev);*/
							}
							that.callbacks[i](data[i]);
							if(data[i+"Index"]){
								fetchArgs[i+"Index"] = data[i+"Index"];
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
		  	complete:function(jq,textstatus){if(textstatus != "success")that.fetch(0);}
		  });
		}
}
x = new Sync("myArg");
x.register("chats",function(chats)
		{
	var str;
			for(i in chats){
			str = "<li>"+chats[i]+"</li>";
				$("#chats").prepend(str);
				}
		});
x.register("users",function(users)
		{
	var str;
	$("#users").html("");
			for(i in users){
			str = "<li>"+users[i]+"</li>";
				$("#users").append(str);
				}
		});
x.register("games",function(games)
		{
	var str;
	$("#games").html("");
			for(i in games){
			str = "<li>"+games[i]+"</li>";
				$("#games").append(str);
				}
		});
x.register("clock",function(clock)
{
	$("#clock").html(clock);
});
x.fetch(0);

function doit(){
	var mychat = $("#mychat").attr("value");
	$.ajax({url: "/~drodal/ci/index.php/lobby/add/",
		type: "POST",
		data:{chat:mychat,
		},
		success:function(data, textstatus){}
	});
	$("#mychat").attr("value","");
}

</script>
<body>
<a href="/~drodal/ci/index.php/lobby/logout"/>logout</a>
<form onsubmit="doit();return false;" id="chatform" method="post">
<fieldset style="float:left;">
<legend>Time
</legend><div id="clock"></div></fieldset>
  <fieldset style="float:right;">
<legend>Users
</legend><div id="users" ></div></fieldset>
  <div style="clear:both;"></div>
<input  id="mychat" name="chats" type="text">
<input name="submit" type="submit"><fieldset>
<legend>Chats
</legend><div id="chats"></div></fieldset>
<fieldset>
<legend>Games
</legend><div id="games"></div></fieldset>


</form>
<div style="height:100px;width:100px;background:pink;position:relative">
<div style="position:absolute;top:14px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
<div style="position:absolute;top:40px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
<div style="position:absolute;top:64px;left:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
<div style="position:absolute;top:14px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
<div style="position:absolute;top:40px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
<div style="position:absolute;top:64px;right:20px;height:16px;width:16px;border-radius:8px;background:black;"></div>
</div>
</body>