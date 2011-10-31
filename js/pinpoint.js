var openForGuesses = false;

function mouseCoords(ev){
	if(ev.pageX || ev.pageY){
		return {x:ev.pageX, y:ev.pageY};
	}
	return {
		x:ev.clientX + document.body.scrollLeft - document.body.clientLeft,
		y:ev.clientY + document.body.scrollTop  - document.body.clientTop
	};
}


function mapCoords(event){
    pos_x = event.offsetX?(event.offsetX):event.pageX-document.getElementById("map").offsetLeft;
    pos_y = event.offsetY?(event.offsetY):event.pageY-document.getElementById("map").offsetTop;
	return {x:pos_x, y:pos_y};
}

function nextQuestion(txt){
	document.getElementById("startscreen").style.display = "none";
	document.getElementById("message").style.display = "none";
	document.getElementById("flag_yellow").style.display = "none";
	document.getElementById("flag_green").style.display = "none";

	var t = txt.split("\n");
	document.getElementById("game_target").innerHTML = t[0];
	document.getElementById("game_info").style.display = "block";
	window.setTimeout(function(){openForGuesses = true;},100);
}

function ajaxNextQuestion(new_game){
	var req = createXMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200) {
			nextQuestion(req.responseText);
			if(new_game)
				document.getElementById("game_score").innerHTML = 0;
		}
		
	};
	req.open('GET', encodeURI('ajax.php?nextQuestion' + (new_game==true?'&newGame':'')), true);
	req.send(null);
}

function createXMLHttpRequest() {
	var types = [
	'Microsoft.XMLHTTP',
	'MSXML2.XMLHTTP.5.0',
	'MSXML2.XMLHTTP.4.0',
	'MSXML2.XMLHTTP.3.0',
	'MSXML2.XMLHTTP'
	];
	
	for (var i = 0; i < types.length; i++) {
		try {
			return new ActiveXObject(types[i]);
		} catch(e) {}
	}
	
	try {
		return new XMLHttpRequest();
	} catch(e) { }
	
	return false; // XMLHttpRequest not supported
}

function clickMap(ev){
	if(openForGuesses){
		var mousePos = mapCoords(ev);
		document.getElementById("flag_yellow").style.left = mousePos.x - 6 + "px";
		document.getElementById("flag_yellow").style.top = mousePos.y - 44 + "px";
		document.getElementById("flag_yellow").style.display = "block";
		openForGuesses = false;
		ajaxSendAnswer(mousePos.x, mousePos.y);
	}
}

function ajaxSendAnswer(ClientX, ClientY){
	var req = createXMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200) {
			var t = req.responseText.split("\n");
			showServerAnswer(ClientX, ClientY, t[0], t[1]);
			document.getElementById("game_score").innerHTML = t[2];
			window.setTimeout(function(){showMessage(t[3]);},1500);
		}
	};
	req.open('GET', encodeURI('ajax.php?getAnswer&x=' + ClientX + '&y=' + ClientY), true);
	req.send(null);
}

function ajaxLoadHighscores(){
	var req = createXMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200) {
			document.getElementById("highscores").innerHTML = req.responseText;
		}
	};
	req.open('GET', encodeURI('ajax.php?highscores'), true);
	req.send(null);
}

function showServerAnswer(ClientX, ClientY, ServerX, ServerY){
	document.getElementById("flag_green").style.left = parseInt(ServerX) - 6 + "px";
	document.getElementById("flag_green").style.top = parseInt(ServerY) - 44 + "px";
	document.getElementById("flag_green").style.display = "block";

	if(ServerY > 350 && ServerY <= 430) {
		document.getElementById("message").style.top = parseInt(ServerY) + 10 + "px";
	} else if(ServerY > 430 && ServerY < 470) {
		document.getElementById("message").style.top = (350 - ((parseInt(ServerY)-80) - 430)) + "px";
	} else {
		document.getElementById("message").style.top = "350px";
	}
}

function showMessage(txt){
	document.getElementById("message").innerHTML = txt;
	document.getElementById("message").style.display = "block";
}

function ajaxSaveScore(){
	var name = document.getElementById("highscore_name").value;
	var req = createXMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200) {
			ajaxLoadHighscores();
			document.getElementById("save_highscore").innerHTML = "Score saved";
		}
	};
	req.open('GET', encodeURI('ajax.php?saveScore&name=' + name), true);
	req.send(null);
}

/*function clickMap(ev){
	var mousePos = mouseCoords(ev);
	alert("x:" + mousePos.x + " y:" + mousePos.y);
}*/