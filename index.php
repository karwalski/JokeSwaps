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
	// Need to also check ring secrets
	
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
JokeSwaps - <?PHP echo ucfirst($user); ?>
</TITLE>

    <meta charset="utf-8" />
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta property="og:image" content="http://jokeswaps.com/images/meta_preview.png"/>
	<meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI']?>" />
	<meta property="og:site_name" content="<?PHP echo ucfirst($user); ?>'s JokeSwaps"/>
	<meta property="og:title" content="<?PHP echo ucfirst($user); ?>'s JokeSwaps" />
	<meta property="og:description" content="Swap jokes with your friends, the social media site for kids." />
	<meta property="og:type" content="profile" />
	<meta property="profile:first_name" content="<?PHP echo ucfirst($user); ?>" />
	<meta property="profile:username" content="<?PHP echo ucfirst($user); ?>" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?PHP echo ucfirst($user); ?>'s JokeSwaps" />
	<meta name="twitter:description" content="Swap jokes with your friends, the social media site for kids." />
	<meta name="twitter:image" content="http://jokeswaps.com/images/meta_preview.png" />
	<meta name="twitter:url" content="<?php echo $_SERVER['REQUEST_URI']?>" />
	<meta name="twitter:site" content="@JokeSwaps" />
	<meta name="description" content="Swap jokes with your friends, the social media site for kids.">
	<meta name="author" content="karwalski">
	<meta name="fb:profile_id" content="104256666573027">

    <link rel="stylesheet" href="css/960_16_col.css" />
    <link rel="stylesheet" href="css/reset.css" />
    <link rel="stylesheet" href="css/text.css" />
    <link rel="stylesheet" href="css/mainstyle.css" />
	
	
    <script src="js/script.js" ></script>






</HEAD>
<BODY onLoad="checkCookie();" >
	
	<div id="blanket" style="display:none;"></div>
	<div id="rulesDiv" style="display:none;">
	<button onclick="popup('rulesDiv')" style="float:right;">Close (X)</button><BR />
	<H1>JokeSwaps Rules</H1>
	1. Parents Must signup for the kids<BR />
	2. Jokes only, no comments or messages<BR />
	3. Don't be rude or offensive<BR />
	4. Don't share personal information<BR />
	5. Don't be a bully<BR />
	6. Only share your secret word with friends<BR /><BR />

	<button onclick="popup('rulesDiv')">Go to JokeSwaps</button><BR />
	</div>
	
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
                <img class="img2" src="images/avatars/<?PHP if (isset($userInfo[0]["avatar"]) && $userInfo[0]["avatar"] != "" && $userInfo[0]["avatar"] != "00") { echo $userInfo[0]["avatar"];} else {echo 'avatar';} ?>.png" />
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
	
	

        


				<?PHP $sql = "SELECT * FROM rings WHERE username = '$user' ORDER BY RAND()" ;
				$result = $conn->query($sql);
				$count = 1;

				if ($result->num_rows > 0) {
		            echo '<div id="userFriend" class="grid_5 omega">
            <!--Start of Friend-->
            <div id="friendTitle">
                <h3>FRIENDS</h3>
            </div>
            <!--End of Search form-->
			<div id="friendGrid" class="grid_5">
		                <!--Startof Friend Grid-->';
					
					while($row = $result->fetch_assoc()) {
						//How many friends to list
						$maxList = 6;
						//even number of friends from each ring
						$limit = floor($maxList / $result->num_rows);
						if ($count == 1)
						{
							//Check if remainder in limit not equal to maxList
							if (($limit * $result->num_rows) != $maxList)
							{
								$limit = $limit + ( $maxList - ($limit * $result->num_rows));
							}
						
						}
						
						$ringID = $row["RingID"];
						
						$sql = "SELECT * FROM rings WHERE RingID = '$ringID' ORDER BY RAND() LIMIT $limit  " ;
						$ringresult = $conn->query($sql);
						while($usersrow = $ringresult->fetch_assoc()) {
							
				            
							
							$friend = $usersrow["username"];
							
							if ($friend == $user)
							{
								// Is self - do nothing
							}
							else
							{
							// lookup friends avatar
							$sql = "SELECT * FROM users WHERE username = '$friend'" ;
							$result = $conn->query($sql);

							for ($friendInfo = array (); $friendrow = $result->fetch_assoc(); $friendInfo[] = $friendrow);
							if (isset($friendInfo[0]["avatar"]) && $friendInfo[0]["avatar"] != "" && $friendInfo[0]["avatar"] != "00")
								 { $avatar = $friendInfo[0]["avatar"];}
							else { $avatar = 'avatar';}
							 
							echo '<div class="friend-bio grid_2">
			                    <img src="images/avatars/' . $avatar . '.png" height="35px" width="35px" />
			                    <h6 id="friendName"><a href="http://' . $friend . '.jokeswaps.com" target="_top">' . ucfirst($friend) . '</a></h6></div>';
							
								
							}
							$count = $count++;
						}
					}
					echo '            </div>
            <!--End of Friend Grid-->
        </div>
        <!--End of User Friend-->';
				}
				else
				{
					// Do not display friends grid if not part of a ring
				}
					
					?>
				



    </section>
    <!--End of User Bio-->

	
	
	

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

	
	
    <section id="questionSubmit" class="container_16">
        <!--Start of question section-->
        <div class="container_16" id="questionForm">

            <div class="grid_6 alpha" id="logIn">
                <div class="grid_5" id="titleBg">
                </div>
                <h3 id="logInTitle">Post a joke to <?PHP echo ucfirst($user); ?></h3>
                <FORM METHOD="POST" ACTION="<?php echo $_SERVER['REQUEST_URI']?>" class="grid_6" name="jokeForm" id="jokeForm">
                    <input type="hidden" name="new" id="new" value="joke">
					<input class="grid_5" type="text" name="name" id="name" placeholder="Name..." />
                    <input class="grid_5" type="text" name="secret" id="secret" placeholder="Secret Word..." />
					<SELECT id="preset" name="preset" onChange="loadPreset();" class="grid_5">
					<OPTION value="" disabled selected >JokeSwaps Robot's Jokes</OPTION>

					<?PHP


					$sql = "SELECT * FROM PresetJokes WHERE PresetID IN ('$rand1','$rand2','$rand3','$rand4','$rand5')" ;
					$result = $conn->query($sql);

					while($row = $result->fetch_assoc()) {
					echo '<OPTION value="' . $row['PresetID'] . '">' . $row['joke'] . '</OPTION>';

					}
					?>
					</SELECT>
					<label for="name" class="grid_5">Is your joke a Knock Knock joke? <input type="checkbox" name="knock" id="knock" value="knock" onChange="jokeType()"></label>
					<span id="line1" class="grid_5"></span>
					<span id="line2" class="grid_5"></span>
                    <textarea class="grid_5" placeholder="Question..." contenteditable="true" rows="3" cols="20" name="joke" id="joke" required onChange="jokeChange();" onInput="jokeInput();"></textarea>
					<span id="line3" class="grid_5"></span>
                    <textarea class="grid_5" placeholder="Answer..." contenteditable="true" rows="3" cols="20" name="answer" id="answer" required></textarea>

                

            </div>
            <div class="grid_5" id="captcha" onClick="popup('captchaPopDiv')">
                <div class="grid_5" id="captchaBg">
                    <div class="grid_4 center captchaBtn" id="captchaBtn">
                
		                    <div class="grid_3 omega " id="submitBtn" >
		                        <img id="submitCheck" src="images/check.png" />
		                    </div>
                
		                <h2 class="grid_5" id="submitTitle">PLAY </h2>
						<div id="captchaPopDiv" style="display:none;">
						<button onclick="popup('captchaPopDiv')" style="float:right;">Close (X)</button><BR />
						<?php 
						echo $funcap; 
						?>
					</div>

                    </div>
                </div>

            </div>

            <div class="grid_4" id="submit" onClick="SubmitJoke();">

                
                    <div class="grid_3 omega " id="submitBtn">
                        <img id="submitCheck" src="images/check.png" />
                    </div>
                
                <h2 class="grid_5" id="submitTitle">SUBMIT </h2>
				</form>

            </div>

        </div>

        <div class="container_16" id="questionLine">

            <div class="arrow grid_1 alpha inactive" id="upArrow" onClick="jokeScroll('left');">
                <h1>&#x3008;</h1>
            </div>
			

<?PHP

// Print jokes on users page
$sql = "SELECT * FROM jokes WHERE forUser = '$user' ORDER BY id DESC" ;
$result = $conn->query($sql);
$count = 1;

echo '<script> var numJokes = ' . $result->num_rows . '; </script>';

if ($result->num_rows > 0) {
    // output data of each row

	
    while($row = $result->fetch_assoc()) {

        echo '         <div class="grid_3 section' . $count . ' inline" id="jokeSection' . $count . '"';
		if ($count > 4) // Do not display more than 5, however they are built for JS scroll
		{
			echo ' style="display:none;"';
		}
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
			                    <button class="grid_3 btn" id="section' . $count . 'Btn" onClick="showAnswer(' . $row["id"] . ', this);">
			                        <span id="AnswerButton' . $row["id"] . '" style="visibility:visibile;">
									<h2 class="btn.text">
									SHOW ANSWER</h2></span>
			                        <span id="Answer' . $row["id"] . '" style="visibility:hidden;">
									<h2 class="btn.text"style="margin-top: -23px;">' . $row["answer"] . '</h2></span>
									                    </button>';
									          

		echo '<button class="grid_3 btn" style="width: 45%; margin-top: 5px;" onClick="funnyButton(' . $row["id"] . ');" id="funnyButton_' . $row["id"] . '">This is funny</button>';
		echo '<button class="grid_3 btn" style="width: 45%;margin-top: -40px;margin-right: -20px;float: right;" onClick="showFlagSelect(' . $row["id"] . ');" id="FlagButton_' . $row["id"] . '">Report joke</button>';
		echo '<div id="FlagSelect_' . $row["id"] . '" style="visibility:hidden;  display: inline-block; margin-top: 10px; margin-left: 20px; padding: 10px;border-radius: 15px; color: #ffffff; background-color: #d80404;">Select reason for reporting: ';
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

} else {
    echo "No jokes yet, send " . $user . " a joke now";
}



?>
            <div class="arrow grid_1 omega" id="downArrow" onClick="jokeScroll('right');">
                <h1>&#x3009;</h1>
            </div>
        </div>
<input type="hidden" name="scrollPos" id="scrollPos" value="1"> 
    </section>
    <!--End of  question section-->

    </div>
    <!--End of Body wrapper-->
    <footer id="footerBg_main">



<div class="footerblurb">
About: When the 7 year old twins Tammi and Mahni created their first web pages, they were quickly dissapointed to discover that only they could add jokes to their personal pages, and not to their sisters page. They needed a 'JokeSpace' or 'JokeTime' - a social media site allowing them to swap jokes, a few tips from dad and they were coding away creating a SQL database and writting up a php script to save and display the jokes. Ofcourse the dinner table discussion that night quickly turned into a discussion of the sites rules and terms and conditions, which will be also written by kids for kids. Visit back regularly to see the site progress as they choose their style for the site and hopefully one day open it for parents to register their kids.<BR /><BR /><BR />

<a href="#" onclick="popup('rulesDiv')">Rules</a> - <a href="tac.php">Terms and Conditions</a> - <a href="privacy.php">Privacy Policy</a> - <a href="parents.php">Parents Console</a><BR /><BR /><BR />


<a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="images/cc.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">JokeSwaps</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/karwalski/jokeswaps" property="cc:attributionName" rel="cc:attributionURL">Karwalski</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a>.
</div>



		    </footer>
</body>

</html>








<?PHP


}

}

$conn->close();
?>



</BODY>
</HTML>