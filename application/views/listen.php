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
  </style><script>
  $(document).ready(function() {
	  $("#city").draggable({stop:
	  function(event,ui){
		  doit(ui.offset.left,ui.offset.top);

	  }});
  });
function doit(x,y){
	$.ajax({url: "/ci/index.php/welcome/add/City",
		type: "POST",
		data:{x:x,y:y,
		},
		success:function(data, textstatus){}
	});
	$("#Chat").attr("value","");
}
if($.isArray(Array())){alert("Y");}
function Sync(arg){
	this.id = "Sync";
	this.callbacks = Object;
	this.lengths = {};
	this.register = function(name, callback){
		this.callbacks[name] = callback;
		this.lengths[name] = 0;
	}
	this.fetch = function(last_seq){
		that = this;
		  $.ajax(
		    {url: "/ci/index.php/welcome/fetch/"+last_seq,
		    	type:"POST",
		    	success:function(data,textstatus){
			    	for(i in that.callbacks){
						if(data[i]){
							alert(i);
				    		if($.isArray(data[i])){
					    		alert(that.lengths[i]);
					    		var lastlength = that.lengths[i];
					    		that.lengths[i] = data[i].length;
								data[i].splice(0,lastlength);
					    		alert(that.lengths[i]);
					    		alert(data[i].length);
					    	}
							if(true){
								/*var prev = that.cache[i];
								that.cache[i] = data[i].length;
								if(prev)
									data[i] = data[i].splice(0,prev);*/
							}
							that.callbacks[i](data[i]);
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
		  		that.fetch(last_seq);
		  	},
		  	complete:function(jq,textstatus){if(textstatus != "success")that.fetch(0);}
		  });
		}
}
x = new Sync("myArg");
x.register("lastmove",checkCity);
x.register("moves",function(moves){
	if(moves){
	pos = moves[moves.length-1];
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
	}
}
);
x.fetch(0);
function ffetch(last_seq){
  $.ajax(
    {url: "/ci/index.php/welcome/fetch/"+last_seq,
    	type:"POST",
    	success:function(data,textstatus){
    		if(data.moves){
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
  		}else{alert("help"+data);}
  		fetch(last_seq);
  	},
  	complete:function(jq,textstatus){if(textstatus != "success")fetch(0);}
  });
}
//fetch(0);
function checkCity(lastmove){
	city = new Object();
	army = new Object();
	city.x = parseInt(lastmove.city.x);
	army.x = parseInt(lastmove.army.x);
	city.y = parseInt(lastmove.city.y);
	army.y = parseInt(lastmove.army.y);
	if(city && army){
		if(army.x > city.x && army.x < (city.x + 100) && army.y > city.y && army.y < (city.y + 100)){
			$("#city div").animate({backgroundColor:"red",color:"red"},9000,"linear",function(){alert('Game Over');});
			$("#city div span").animate({color:"white"},9000);
		}else{
			$("#city div").stop(true).animate({backgroundColor:"#ccf",color:"black"},0);
			$("#city div span").stop(true).animate({color:"#ccf"},0);
		}
	}
}

  </script>
  
<div id="draggable">The Huge Army!!!!</div>


<div id="city"><div><span style="color:#ccf">HELP</span> The defenseless city.</div></div>