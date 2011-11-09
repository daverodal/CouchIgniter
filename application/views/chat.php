<!doctype html>
<html>
<head>
  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>
  <style type="text/css">
    #draggable {position:absolute;z-index:100;width: 50px; height: 50px; background: silver; }
    #city {position:absolute;top:300px;width:100px;height:100px;background:#ccf;}
  </style>
  <script>
  $(document).ready(function() {
	  $("#draggable").draggable({stop:
	  function(event,ui){
		  doit(ui.offset.left,ui.offset.top);

	  }});
  });
function doit(x,y){
	$.ajax({url: "/ci/index.php/welcome/add/Army",
		type: "POST",
		data:{x:x,y:y,
		},
		success:function(data, textstatus){}
	});
	$("#Chat").attr("value","");
}
  
function fetch(last_seq){
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
fetch(0);
function checkCity(lastmove){
city = new Object();
army = new Object();
city.x = parseInt(lastmove.city.x);
army.x = parseInt(lastmove.army.x);
city.y = parseInt(lastmove.city.y);
army.y = parseInt(lastmove.army.y);

if(city && army){
	if(army.x > city.x && army.x < (city.x + 100) && army.y > city.y && army.y < (city.y + 100)){
		$("#city").animate({opacity:"0.2"},1000);
	}else{
		$("#city").animate({opacity:"1.0"},0);
	}
}
}
  </script></head>
 <body class="app">
	<div id="container">
		<div id="content">
		<div id="draggable">The Huge Army!!!!</div>
		<div id="city"> The defenseless city.</div>
		</div>
	</div>
</body>
</html>
<body class="app">
	<div id="container">
		<div id="content">