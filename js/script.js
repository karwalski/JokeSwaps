// Main script file for jokeswaps

function jokeType()
{
	if(document.getElementById("knock").checked)
	{
	document.getElementById("line1").innerHTML = "Knock Knock";
	document.getElementById("line2").innerHTML = "Who's there?: ";
	document.getElementById("line3").innerHTML = ".... who?: ";
}
else
{

	document.getElementById("line1").innerHTML = "";
	document.getElementById("line2").innerHTML = "Joke Question: ";
	document.getElementById("line3").innerHTML = "Answer: ";
	
}
}

function jokeInput()
{
if(document.getElementById("knock").checked)
{
	var who = document.getElementById("joke").value;
	document.getElementById("line3").innerHTML = who + " who?: ";
	
}
}

function jokeChange()
{
	if(document.getElementById("knock").checked)
	{
		var who = document.getElementById("joke").value;
		document.getElementById("line3").innerHTML = who + " who?: ";
		
	if(document.getElementById("answer").value == "")
	{
		document.getElementById("answer").value = who + " ";
	}
}
}



function showAnswer(jokeID, button) {

if (document.getElementById("Answer" + jokeID).style.visibility == "visible"){
	document.getElementById("Answer" + jokeID).style.visibility = "hidden";
	document.getElementById("AnswerButton" + jokeID).style.visibility = "visible";
	button.className = "grid_3 btn"; 
}
else
{
document.getElementById("Answer" + jokeID).style.visibility = "visible";
document.getElementById("AnswerButton" + jokeID).style.visibility = "hidden";
	button.className = "grid_3 btn btnSelect"; 
}
}

function showFlagSelect(jokeid)
{
	if (document.getElementById("FlagSelect_" + jokeid).style.visibility == "visible"){
		document.getElementById("FlagSelect_" + jokeid).style.visibility = "hidden";
	}
	else
	{
	document.getElementById("FlagSelect_" + jokeid).style.visibility = "visible";
	}
}




function setFlag(jokeid){
    var ajaxRequest;  // The variable that makes Ajax possible!
    //Set AjaxRequest for all Major browsers, nothing to do here, this is standard
    try{
        // Opera 8.0+, Firefox, Safari
        ajaxRequest = new XMLHttpRequest();
    } catch (e){
        // Internet Explorer Browsers
        try{
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try{
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e){
                // Something went wrong
                alert("Your browser does not support flagging jokes");
                return false;
            }
        }
    }
    // When the Ajax Request waits for php you get some status codes, everything is done when it reaches 4. Add your javascript events etc here...
    ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState < 4){
        document.getElementById("FlagSelect_" + jokeid).innerHTML = "Telling the JokeSwaps Robot...";
        }
        if(ajaxRequest.readyState == 4){
    // Some Javascript to change your flag colour image
			showFlagSelect(jokeid);
			document.getElementById("FlagButton_" + jokeid).innerHTML = "Reported";
			document.getElementById("FlagButton_" + jokeid).disabled=true;
			document.getElementById("FlagButton_" + jokeid).className = "grid_3 btn btnSelect"; 
    }
    }

    // this is here your php happens without page reload. (In the php file)
	var reason = document.getElementById("FlagReason_" + jokeid).value;
	
    var queryString = "?jokeid=" + jokeid + "&reason=" + reason;
    ajaxRequest.open("GET", "flag.php" + queryString, true);
    ajaxRequest.send(null);
}



function funnyButton(jokeid){
    var ajaxRequest;  // The variable that makes Ajax possible!
    //Set AjaxRequest for all Major browsers, nothing to do here, this is standard
    try{
        // Opera 8.0+, Firefox, Safari
        ajaxRequest = new XMLHttpRequest();
    } catch (e){
        // Internet Explorer Browsers
        try{
            ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            try{
                ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e){
                // Something went wrong
                alert("Your browser does not support voting for funny jokes");
                return false;
            }
        }
    }
    // When the Ajax Request waits for php you get some status codes, everything is done when it reaches 4. Add your javascript events etc here...
    ajaxRequest.onreadystatechange = function(){
        if(ajaxRequest.readyState < 4){
			document.getElementById("funnyButton_" + jokeid).innerHTML = "Telling the JokeSwaps Robot...";
			document.getElementById("funnyButton_" + jokeid).disabled=true;
        }
        if(ajaxRequest.readyState == 4){
    // Some Javascript to change your flag colour image
			document.getElementById("funnyButton_" + jokeid).innerHTML = "FUNNY!";
			document.getElementById("funnyButton_" + jokeid).disabled=true;
			document.getElementById("funnyButton_" + jokeid).className = "grid_3 btn btnSelect"; 
    }
    }

    // this is here your php happens without page reload. (In the php file)
	
    var queryString = "?jokeid=" + jokeid;
    ajaxRequest.open("GET", "funny.php" + queryString, true);
    ajaxRequest.send(null);
}


function SubmitJoke()
{
	document.getElementById("jokeForm").submit();
	
}


// Popup divs
function toggle(div_id) {
	var el = document.getElementById(div_id);
	if ( el.style.display == 'none' ) {	el.style.display = 'block';}
	else {el.style.display = 'none';}
}
function blanket_size(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportheight = window.innerHeight;
	} else {
		viewportheight = document.documentElement.clientHeight;
	}
	if ((viewportheight > document.body.parentNode.scrollHeight) && (viewportheight > document.body.parentNode.clientHeight)) {
		blanket_height = viewportheight;
	} else {
		if (document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight) {
			blanket_height = document.body.parentNode.clientHeight;
		} else {
			blanket_height = document.body.parentNode.scrollHeight;
		}
	}
	var blanket = document.getElementById('blanket');
	blanket.style.height = blanket_height + 'px';
	var popUpDiv = document.getElementById(popUpDivVar);
	popUpDiv_height=blanket_height/2-150;//150 is half popup's height
	popUpDiv.style.top = popUpDiv_height + 'px';
}
function window_pos(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportwidth = window.innerHeight;
	} else {
		viewportwidth = document.documentElement.clientHeight;
	}
	if ((viewportwidth > document.body.parentNode.scrollWidth) && (viewportwidth > document.body.parentNode.clientWidth)) {
		window_width = viewportwidth;
	} else {
		if (document.body.parentNode.clientWidth > document.body.parentNode.scrollWidth) {
			window_width = document.body.parentNode.clientWidth;
		} else {
			window_width = document.body.parentNode.scrollWidth;
		}
	}
	var popUpDiv = document.getElementById(popUpDivVar);
	window_width=window_width/2-150;//150 is half popup's width
	popUpDiv.style.left = window_width + 'px';
}
function popup(windowname) {
	blanket_size(windowname);
	window_pos(windowname);
	toggle('blanket');
	toggle(windowname);		
}


function jokeScroll(command)
{
	alert ('There are ' + numJokes + ' jokes & You are scrolling ' + command);
	
	
}
