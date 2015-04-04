<?php

require_once("funcaptcha.php");

$funcaptcha = new FUNCAPTCHA();

 $funcap = $funcaptcha->getFunCaptcha("A372E9C8-4DFC-E5D8-1EFF-C02CBF8FCE35");

// Access the sql database


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



?>



<HTML>
<HEAD>
<TITLE>
JokeSwaps - Felix
</TITLE>
<script>



function showAnswer(jokeID) {

if (document.getElementById("Answer" + jokeID).style.visibility == "visible"){
document.getElementById("Answer" + jokeID).style.visibility = "hidden"
}
else
{
document.getElementById("Answer" + jokeID).style.visibility = "visible";
}
}
</script>

<style>
 body {
 background-color:lightblue;
 color:black;
 }
 
</style>


</HEAD>
<BODY>
<IMG SRC="js_blue.jpg" width="400px"><BR />
<H1>Felix's Joke Swap </H1>

<STRONG>A trial social media site created by kids for kids.</STRONG><BR />




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




<STRONG>Write a joke on Felix's page</STRONG>
<FORM METHOD="POST" ACTION="new_joke.php?for=felix">
<label for="name">Your Name: </label><input type="text" name="name" id="name" required><BR />
<label for="secret" title="A secret word is a password shared by the page owner only to people they know">The secret word: </label><input type="text" name="secret" id="secret" required><BR />
<label for="joke">Joke Question: </label><input type="text" name="joke" id="joke" required><BR />
<label for="answer">Answer: </label><input type="text" name="answer" id="answer" required><BR />

<?php 
echo $funcap; 
?>


<input type="submit" value="Submit">
</FORM>
<BR /><BR />
<STRONG>Felix's Joke Feed</STRONG><BR />
<?PHP

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = 'felix' ORDER BY id DESC" ;
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo '<strong>' . $row["fromName"] . ':</strong> ' . $row["joke"] . '<BR /><button onClick="showAnswer(' . $row["id"] . ');">Here\'s the answer</button><BR/><div id="Answer' . $row["id"] . '" style="visibility:hidden;">';
        echo $row["answer"] . '<BR /></div>';
    }
} else {
    echo "No jokes yet, send " . $user . " a joke now";
}
$conn->close();


?>

<BR /><BR /><BR />
<a href="http://jack.jokeswaps.com" target="_top">Jack's Joke Swap</a><BR />
<a href="http://mahni.jokeswaps.com" target="_top">Mahni's Joke Swap</a><BR />
<a href="http://tammi.jokeswaps.com" target="_top">Tammi's Joke Swap</a><BR />
<a href="http://jax.jokeswaps.com" target="_top">Jax's Joke Swap</a><BR />

<BR />
About: When the 7 year old twins Tammi and Mahni created their first web pages, they were quickly dissapointed to discover that only they could add jokes to their personal pages, and not to their sisters page. They need a 'JokeSpace' or 'JokeTime' - a social media site allowing them to swap jokes, a few tips from dad and they were coding away creating a SQL database and writting up a php script to save and display the jokes. Ofcours the dinner table discussion that night quickly turned into a discussion of the sites rules and terms and conditions, which will be also written by kids for kids. Visit back regularly to see the site progress as they choose they style the site and hopefully one day open it for parents to register their kids.



</BODY>
</HTML>
