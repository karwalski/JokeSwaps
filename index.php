<HTML>
<HEAD>

<?PHP

require_once("funcaptcha.php");

$servername = "localhost";
 $username = "root";
// $username = "matt";
$password = "YmQCl60qMwe2YpUn34k7";
$dbname = "jokeswaps";







// Create connection
$conn = new mysqli($servername, $username, $password, $dbname );
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check SQL user

 
 $user = strtolower(mysqli_real_escape_string($conn, substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."))));

$sql = "SELECT * FROM users WHERE username = '$user'" ;
$result = $conn->query($sql);

for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

if(empty($userInfo)) {
echo '<TITLE>
JokeSwaps
</TITLE>
<BODY>';
echo '<IMG SRC="images/logo_pink.png" width="400px"><BR /><H1>Coming Soon!</H1>';


}
else
{

if($userInfo[0]["verified"] == "0") {
echo '<TITLE>
JokeSwaps
</TITLE>
<BODY>';
echo '<IMG SRC="images/logo_pink.png" width="400px"><BR /><H1>Your parent needs to verify their email address, please click the link in the email we sent out.</H1>" ';


}
else
{

if (isset($_POST['new']) && $_POST['new'] == "joke")
{
$funcaptcha = new FUNCAPTCHA();
$verified = $funcaptcha->checkResult("E1A7B6DB-4779-5670-933E-464FB325E22D");

if ($verified)
{
if ($_POST['secret'] == $userInfo[0]["secret"])
{


// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$fromName = $_POST['name'];
 $fromName = mysqli_real_escape_string($conn, $fromName);
$joke = $_POST['joke'];
 $joke = mysqli_real_escape_string($conn, $joke);
$answer = $_POST['answer'];
 $answer = mysqli_real_escape_string($conn, $answer);
$fromIP = $_SERVER['REMOTE_ADDR'];
 $fromIP = mysqli_real_escape_string($conn, $fromIP);

if (isset($_POST['knock']))
{
$type = "knock";
}
else
{
	$type = "question";	
}

// Insert new joke
$sql = "INSERT INTO jokes (forUser, fromName, joke, answer, fromIP, type)
VALUES ('$user', '$fromName', '$joke', '$answer', '$fromIP', '$type')";

if ($conn->query($sql) === TRUE) {
    echo "New joke saved successfully";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}



}
else
{
echo 'Error: You got the secret wrong! Are you sure you are meant to be here?';
}

}
else
{
echo 'Error: We think you are a robot! You didn\'t complete the verification';
}

}





$funcaptcha = new FUNCAPTCHA();

 $funcap = $funcaptcha->getFunCaptcha("A372E9C8-4DFC-E5D8-1EFF-C02CBF8FCE35");


?>




<TITLE>
JokeSwaps - <?PHP echo $user; ?>
</TITLE>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <link rel="stylesheet" href="css/960_16_col.css" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/text.css" />
    <link rel="stylesheet" href="css/mainstyle.css" />



<script>


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



function showAnswer(jokeID) {

if (document.getElementById("Answer" + jokeID).style.visibility == "visible"){
	document.getElementById("Answer" + jokeID).style.visibility = "hidden";
	document.getElementById("AnswerButton" + jokeID).style.visibility = "visibile";
}
else
{
document.getElementById("Answer" + jokeID).style.visibility = "visible";
document.getElementById("AnswerButton" + jokeID).style.visibility = "hidden";
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
    }
    }

    // this is here your php happens without page reload. (In the php file)
	
    var queryString = "?jokeid=" + jokeid;
    ajaxRequest.open("GET", "funny.php" + queryString, true);
    ajaxRequest.send(null);
}


</script>


</HEAD>
<BODY>
	
    <div id="wrapper" class="container_16">
        <!--Start of body wrraper-->
        <header class="container_16">
            <!--Start of Header-->
            <div id="logo">
                <img src="images/logo_
				<?PHP 
				if ($userInfo[0]["theme"] == "blue") {
					echo 'blue';
					}
					elseif ($userInfo[0]["theme"] == "pink") {
					echo 'pink';
					}
					elseif ($userInfo[0]["theme"] == "purple") {
					echo 'purple';
					}
					else
					{
						echo 'blue';
						}
				?>
				.png" class="logo" />
            </div>
            <div id="hdr_img">

                <img src="images/header-img.png" />

            </div>

        </header>
        <!--End of Header-->
	
	
    <section id="userBio" class="container_16">
        <!--Start of User Bio-->
        <div id="userInfo" class=" grid_4 alpha">
            <!--Start of User Info-->
            <div id="userBg" class="grid_4">
                <img class="img2" src="images/avatars/<?PHP if (isset($userInfo[0]["avatar"])) { echo $userInfo[0]["avatar"];} else {echo 'avatar';} ?>.png" />
            </div>
            <div id="userName" class="grid_4">
                <p>Name - <span id="name"><?PHP echo ucfirst($user); ?></span>
                </p>
            </div>
        </div>
        <!--End of User Info-->

        <div id="userScore" class="grid_6">
            <!--Start of User Score-->
            <div id="scoreTitle" class="grid_1 suffix_5">
                <p>Scorecard</p>
            </div>
            <div id="scoreProgress" class="grid_6">
                <progress id="progress" value="0.6" max="100%" class="grid_4 omega"> </progress>
                <h4 class="grid_1">60%</h4>

            </div>
            <div id="bio" class="grid_6">
                <h4>About <?PHP echo ucfirst($user); ?>...</h4>
                <p class="grid_4"><?PHP echo $userInfo[0]["bio"]; ?></p>
            </div>
        </div>
        <!--End of User Score-->
	
	

        <div id="userFriend" class="grid_5 omega">
            <!--Start of Friend-->
            <div id="friendTitle">
                <h3>FRIEND LIST</h3>
            </div>
            <form>
                <input id="friendSearch" type="search" name="search" placeholder="Search" />

            </form>
            <!--End of Search form-->

            <div id="friendGrid" class="grid_5">
                <!--Startof Friend Grid-->
                <div class="friend-bio grid_2">
                    <img src="images/avatars/06.png" height="35px" width="35px" />
                    <h6 id="friendName"><a href="http://mahni.jokeswaps.com" target="_top">Mahni</a></h6>
                </div>

                <div class="friend-bio grid_2">
                    <img src="images/avatars/03.png" height="35px" width="35px" />

                    <h6 id="friendName"><a href="http://tammi.jokeswaps.com" target="_top">Tammi</a></h6>
                </div>

                <div class="friend-bio grid_2">
                    <img src="images/avatars/01.png" height="35px" width="35px" />
                    <h6 id="friendName"><a href="http://jack.jokeswaps.com" target="_top">Jack</a></h6>
                </div>

                <div class="friend-bio grid_2">
                    <img src="images/avatars/avatar.png" height="35px" width="35px" />
                    <h6 id="friendName">Peter Parker</h6>
                </div>
            </div>
            <!--End of Friend Grid-->
        </div>
        <!--End of User Friend-->

    </section>
    <!--End of User Bio-->

	
	
	
	    <!-- Need to add in preset joke menu and associated prefill
	<?PHP
	// Print jokes on users page.. need to check suitable column
	$sql = "SELECT * FROM PresetJokes" ;
	$result = $conn->query($sql);
	$count = $result->num_rows;

	$rand1 = rand(1, $count);
	$rand2 = rand(1, $count);
	$rand3 = rand(1, $count);
	$rand4 = rand(1, $count);
	$rand5 = rand(1, $count);
	?>



	<SCRIPT>
	function loadPreset() {

	var selectedJoke = document.getElementById("preset").value;

	var jokes = [];
	var answer = [];

	<?PHP
	$sql = "SELECT * FROM PresetJokes WHERE PresetID IN ('$rand1','$rand2','$rand3','$rand4','$rand5')" ;
	$result = $conn->query($sql);

	while($row = $result->fetch_assoc()) {
	echo 'jokes["id' . $row['PresetID'] . '"] = "' . $row['joke'] . '";';
	echo 'answer["id' . $row['PresetID'] . '"] = "' . $row['answer'] . '";';
	}
	?>

	document.getElementById("joke").value = jokes["id" + selectedJoke];
	document.getElementById("answer").value = answer["id" + selectedJoke];
	}
	</SCRIPT>
	Preset Joke Selection: <SELECT id="preset" name="preset" onChange="loadPreset();">
	<OPTION value=""></OPTION>

	<?PHP


	$sql = "SELECT * FROM PresetJokes WHERE PresetID IN ('$rand1','$rand2','$rand3','$rand4','$rand5')" ;
	$result = $conn->query($sql);

	while($row = $result->fetch_assoc()) {
	echo '<OPTION value="' . $row['PresetID'] . '">' . $row['joke'] . '</OPTION>';

	}
	?>
	</SELECT>
	<BR /><BR />
	-->
	

    <!-- Need to add back in the knock knock switch and associated labels/autocomplete
	<label for="name">Is your joke a Knock Knock joke? </label><input type="checkbox" name="knock" id="knock" value="knock" onChange="jokeType()"><BR />
	<span id="line1"></span><BR />
-->

	
	
	
    <section id="questionSubmit" class="container_16">
        <!--Start of question section-->
        <div class="container_16" id="questionForm">

            <div class="grid_6 alpha" id="logIn">
                <div class="grid_5" id="titleBg">
                </div>
                <h3 class="grid_2" id="logInTitle">Fill Form</h3>
                <FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" class="grid_6">
                    <input type="hidden" name="new" id="new" value="joke">
					<input class="grid_5" type="text" name="name" id="name" placeholder="Name..." />
                    <input class="grid_5" type="text" name="secret" id="secret" placeholder="Secret Word..." />
                    <textarea class="grid_5" name="question" placeholder="Question..." contenteditable="true" rows="3" cols="20" name="joke" id="joke" required onChange="jokeChange();" onInput="jokeInput();"></textarea>
                    <textarea class="grid_5" name="answer" placeholder="Answer..." contenteditable="true" rows="3" cols="20" name="answer" id="answer" required></textarea>

                

            </div>
            <div class="grid_5" id="captcha">
                <div class="grid_5" id="captchaBg">
                    <div class="grid_4 center captchaBtn" id="captchaBtn">
						<?php 
						echo $funcap; 
						?>

                    </div>
                </div>

            </div>

            <div class="grid_4" id="submit">

                <a href="#">
                    <div class="grid_3 omega " id="submitBtn">
                        <img id="submitCheck" src="images/check.png" />
                    </div>
                </a>
                <h2 class="grid_5" id="submitTitle">SUBMIT </h2>
				</form>

            </div>

        </div>

        <div class="container_16" id="questionLine">

            <div class="arrow grid_1 alpha" id="upArrow">
                <a href="#" class="arrow-link"><h1>&#x3008;</h1></a>
            </div>
			

<?PHP

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = '$user' ORDER BY id DESC" ;
$result = $conn->query($sql);
$count = 1;

if ($result->num_rows > 0) {
    // output data of each row

	
    while($row = $result->fetch_assoc()) {
		if ($count < 5) // Temp only show latest 4 until slider is active
		{
        echo '         <div class=" grid_3 section' . $count . ' inline"';
				echo '>

                <div class="box1 grid_3"></div>
                <div class="box2  grid_3">
                    <p class="grid_3 question"><strong>' . $row["fromName"] . ':</strong> ';
		if ($row["type"] == "knock")
		{
			echo 'Knock knock<BR />Who\'s there?<BR />';
		}
		
		echo $row["joke"];
		if ($row["type"] == "knock")
		{
		echo '<BR />' . $row["joke"] . ' who?';
		}
		
		echo '</p>
			                </div>
			                    <button class="grid_3 btn" id="section' . $count . 'Btn" onClick="showAnswer(' . $row["id"] . ');">
			                        <span id="AnswerButton' . $row["id"] . '" style="visibility:visibile;">
									<h2 class="btn.text">
									SHOW ANSWER</h2></span>
			                        <span id="Answer' . $row["id"] . '" style="visibility:hidden;">
									<h2 class="btn.text">' . $row["answer"] . '</h2></span>
									                    </button>';
									          

		echo '<button class="grid_3 btn" style="width:40%;" onClick="funnyButton(' . $row["id"] . ');" id="funnyButton_' . $row["id"] . '">This is funny</button>';
		echo '<button class="grid_3 btn" style="width:40%;" onClick="showFlagSelect(' . $row["id"] . ');" id="FlagButton_' . $row["id"] . '">Report joke</button>';
		echo '<div id="FlagSelect_' . $row["id"] . '" style="visibility:hidden;">Select reason for reporting: ';
		echo '<select name="FlagReason_' . $row["id"] . '" id="FlagReason_' . $row["id"] . '" onChange="setFlag(' . $row["id"] . ')">
<option value="0"></option>
<option value="1">Annoying, not interested or you don\'t understand</option>
<option value="2">It is not a joke</option>
<option value="3">It is bullying</option>
<option value="4">It is rude</option>
<option value="5">I don\'t think it should be on jokeswaps</option>
<option value="6">Spam written by a robot</option>
</select></div>';
		
		echo '  </div>';
		$count = ++$count;
    }
}
} else {
    echo "No jokes yet, send " . $user . " a joke now";
}



?>
            <div class="arrow grid_1 omega" id="downArrow">
                <a href="#" class="arrow-link"><h1>&#x3009;</h1></a>
            </div>
        </div>

    </section>
    <!--End of  question section-->

    </div>
    <!--End of Body wrapper-->
    <footer id="footerBg">
        <form id="contactForm" class="container_16">

            <div class="prefix_8 grid_3 alpha label">

                <label for="name" class="grid_3">Name - </label>

                <label for="emial" class="grid_3">E-mail - </label>

                <label for="Message" class="grid_3">Message - </label>

            </div>


            <div class="grid_5 omega inputs">

                <input type="text" name="name" class="grid_5" id="contactName" />

                <input type="email" name="email" class="grid_5" id="contactEmail" />

                <textarea name="message" id="contactMessage" cols="30" rows="8"></textarea>
                <a href="#"><h1 class="prefix_3 grid_2" id="contactSubmit">GO!</h1></a>

            </div>


        </form>
		    </footer>

<div style="width:100%;">
About: When the 7 year old twins Tammi and Mahni created their first web pages, they were quickly dissapointed to discover that only they could add jokes to their personal pages, and not to their sisters page. They needed a 'JokeSpace' or 'JokeTime' - a social media site allowing them to swap jokes, a few tips from dad and they were coding away creating a SQL database and writting up a php script to save and display the jokes. Ofcourse the dinner table discussion that night quickly turned into a discussion of the sites rules and terms and conditions, which will be also written by kids for kids. Visit back regularly to see the site progress as they choose their style for the site and hopefully one day open it for parents to register their kids.<BR />


<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-sa/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">JokeSwaps</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/karwalski/jokeswaps" property="cc:attributionName" rel="cc:attributionURL">Karwalski</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.
</div>

</body>

</html>








<?PHP


}

}

$conn->close();
?>



</BODY>
</HTML>