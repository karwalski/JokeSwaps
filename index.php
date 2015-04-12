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


$user = substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], "."));



 $user = mysqli_real_escape_string($conn, $user);

$sql = "SELECT * FROM users WHERE username = '$user'" ;
$result = $conn->query($sql);

for ($userInfo = array (); $row = $result->fetch_assoc(); $userInfo[] = $row);

if(empty($userInfo)) {
echo '<TITLE>
JokeSwaps
</TITLE>
<BODY>';
echo '<IMG SRC="js_pink.jpg" width="400px"><BR /><H1>Coming Soon!</H1>';


}
else
{

if($userInfo[0]["verified"] == "0") {
echo '<TITLE>
JokeSwaps
</TITLE>
<BODY>';
echo '<IMG SRC="js_pink.jpg" width="400px"><BR /><H1>Your parent needs to verify their email address, please click the link in the email we sent out.</H1>" ';


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
}
else
{
document.getElementById("Answer" + jokeID).style.visibility = "visible";
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
        document.getElementById("FlagSelect_" + jokeid).innerHTML = "Notifying the JokeSwaps Robot";
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



</script>
<style>
 body {
 background-color:<?PHP echo $userInfo[0]["theme"]; ?>;
 color:<?PHP

if ($userInfo[0]["theme"] == "darkmagenta") {
echo 'cyan';
}
elseif ($userInfo[0]["theme"] == 'magenta;background-image: url("flower.jpg")') {
echo 'black';
}
elseif ($userInfo[0]["theme"] == "lightblue") {
echo 'black';
}
elseif ($userInfo[0]["theme"] == "black") {
echo 'white';
}
else {
echo 'black';
}

?>;
 }
 

</style>

</HEAD>
<BODY>
<IMG SRC="js_<?PHP

if ($userInfo[0]["theme"] == "darkmagenta") {
echo 'pink';
}
elseif ($userInfo[0]["theme"] == "black") {
echo 'pink';
}
elseif ($userInfo[0]["theme"] == 'magenta;background-image: url("flower.jpg")') {
echo 'purple';
}
elseif ($userInfo[0]["theme"] == "lightblue") {
echo 'blue';
}
else {
echo 'blue';
}

?>.jpg" width="400px"><BR />
<H1><?PHP echo $user; ?>'s Joke Swap </H1>

<STRONG><?PHP echo $userInfo[0]["bio"]; ?></STRONG><BR /><BR />



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



<STRONG>Write a joke on <?PHP echo $user; ?>'s page</STRONG>
<FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>">
<input type="hidden" name="new" id="new" value="joke">
<label for="name">Your Name: </label><input type="text" name="name" id="name" required><BR />
<label for="secret" title="A secret word is a password shared by the page owner only to people they know">The secret word: </label><input type="text" name="secret" id="secret" required><BR />

<label for="name">Is your joke a Knock Knock joke? </label><input type="checkbox" name="knock" id="knock" value="knock" onChange="jokeType()"><BR />
<span id="line1"></span><BR />

<label for="joke"><span id="line2">Joke Question: </span></label><textarea rows="3" cols="50" name="joke" id="joke" required onChange="jokeChange();" onInput="jokeInput();"></textarea><BR />
<label for="answer"><span id="line3">Answer: </span></label><textarea rows="3" cols="50" name="answer" id="answer" required></textarea><BR />

<?php 
echo $funcap; 
?>


<input type="submit" value="Submit">
</FORM>
<BR /><BR />
<STRONG><?PHP echo $user; ?>'s Joke Feed</STRONG><BR />
<?PHP

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = '$user' ORDER BY id DESC" ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<strong>' . $row["fromName"] . ':</strong> ';
		if ($row["type"] == "knock")
		{
			echo 'Knock knock<BR />Who\'s there?<BR />';
		}
		
		echo $row["joke"] . '<BR /><button onClick="showAnswer(' . $row["id"] . ');">';
		if ($row["type"] == "knock")
		{
		echo $row["joke"] . ' who?';
		}
		else
		{
		echo 'Here\'s the answer';
		}
		echo '</button><BR/><div id="Answer' . $row["id"] . '" style="visibility:hidden;">';
        echo $row["answer"] . '<BR /></div>';
		echo '<button onClick="showFlagSelect(' . $row["id"] . ');" id="FlagButton_' . $row["id"] . '">Report joke</button>';
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
		
		echo '<BR /><BR />';
    }
} else {
    echo "No jokes yet, send " . $user . " a joke now";
}



?>

<BR /><BR /><BR />
<a href="http://mahni.jokeswaps.com" target="_top">Mahni's Joke Swap</a><BR />
<a href="http://tammi.jokeswaps.com" target="_top">Tammi's Joke Swap</a><BR />
<a href="http://jack.jokeswaps.com" target="_top">Jack's Joke Swap</a><BR />
<a href="http://jax.jokeswaps.com" target="_top">Jax's Joke Swap</a><BR />
<a href="http://felix.jokeswaps.com" target="_top">Felix's Joke Swap</a><BR />

<BR />
About: When the 7 year old twins Tammi and <?PHP echo $user; ?> created their first web pages, they were quickly dissapointed to discover that only they could add jokes to their personal pages, and not to their sisters page. They need a 'JokeSpace' or 'JokeTime' - a social media site allowing them to swap jokes, a few tips from dad and they were coding away creating a SQL database and writting up a php script to save and display the jokes. Ofcours the dinner table discussion that night quickly turned into a discussion of the sites rules and terms and conditions, which will be also written by kids for kids. Visit back regularly to see the site progress as they choose they style the site and hopefully one day open it for parents to register their kids.




<?PHP


}

}

$conn->close();
?>



</BODY>
</HTML>