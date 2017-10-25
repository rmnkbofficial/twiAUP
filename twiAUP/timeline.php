<?php
session_start();
include "config.php";
include "private_functions.php";
include "functions.php";


if(!isset($_SESSION['auth']) || $_SESSION['auth'] == 0) {
	header('Location: '.$home.'login.php');
}

if(isset($_POST['message'])) {
	$dbh = db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $DB_PW);
	$title = $_POST['title'];
	$coorX = $_POST['coorX'];
	$coorY = $_POST['coorY'];
	$message=$_POST['message'].' ';
	$hashtags = array();
	$num_ht = preg_match_all("/#([^\s]+)\s/", $message, $hashtags);

	error_log($_SESSION['user']." posted:(".$message.") at location:(".strval($coorX).",".strval($coorY).") with ".strval($num_ht)." hashtags");

	$res = post_post($dbh, $title, $message, $coorX, $coorY ,$_SESSION['user']);
	if($res['status'] == 0) {
		$err_msg = "<h4 style='text-align:center;'>Error posting message</h4>";
	}
	else{
		$pid = $res['pid'];
		error_log("Matches are:");
		for($i=0;$i<count($hashtags[0]);$i++){
			error_log($hashtags[1][$i]);
			attach_hashtag($dbh, $pid, $hashtags[1][$i]);
		}
	}
}

?>
<html>
	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>
			$(document).ready(function() {
				var coorX = $("#coorX");
				var coorY = $("#coorY");
				$("#location").change(function() {
					console.log(this.value);
					var choice = this.value;
					switch (choice) {
						case "Pittsburgh":
							coorX.val("0");
							coorY.val("0");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "New York":
							coorX.val("10");
							coorY.val("10");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "Chicago":
							coorX.val("25");
							coorY.val("5");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "Los Angelas":
							coorX.val("15");
							coorY.val("20");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						default:
							coorX.val("");
							coorY.val("");
							coorX.attr("readonly",false);
							coorY.attr("readonly",false);
							break;
					}

				});

			});
		</script>
		<title>AUP Database Course</title>
		<?php html_output_head(); ?>
	</head>
	<body>
		<div class="container">
			<?php html_nav('timeline', $_SESSION['user']); ?>
			<div class="row" style='border-bottom: 1px solid #ccc;'>
				<?php echo $err_msg; ?>

				<form action='timeline.php' method='POST'>
					<div class="row" style='padding-left: 30px; padding-right: 30px'>
						<input class="form-control" name="title" placeholder="title"></textarea>
					</div>
					<div class="row" style='padding-left: 30px; padding-right: 30px'>
						<textarea class="form-control" rows="4" name="message" placeholder="content"></textarea>
					</div>
					<div class="row" style='padding-left: 30px; padding-right: 30px; padding-top: 5px;'>

					<div class="col-md-3">
						<select id="location" >
							<option value="customize">Custom Location</option>
							<option value="Paris">Paris</option>
							<option value="London">London</option>
							<option value="Berlin">Berlin</option>
							<option value="Palaiseau">Palaiseau</option>
							<option value="Antony">Antony</option>
						</select>
          </div>
          <div class="col-md-7">
						<input type="number" name="coorX" id="coorX" placeholder="coordinate X" required>
						<input type="number" name="coorY" id="coorY" placeholder="coordinate Y" required>
					</div>
					<div class="col-md-2"> <button class="btn btn-lg btn-primary btn-block" type="submit">Post</button> </div>


				</form>
			</div>
		</div>
<?php
$num_output = 0;
$dbh = db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $DB_PW);
if(isset($_GET['start'])) {
	$timeline = get_timeline($dbh, $_SESSION['user'], 15, $_GET['start']);
} else {
	$timeline = get_timeline($dbh, $_SESSION['user'], 15);
}
$last_time = -1;
if($timeline['status'] == 1) {
	$posts= $timeline['posts'];
	for($i = 0; $i < count($posts); $i++) {
    	error_log($posts[$i]['time']);
		html_post($dbh, $posts[$i]);
		$num_output++;
		$last_time = $posts[$i]['time'];
	}
}

if($num_output == 0) {
	echo "<div class='row' style='font-size: 21px; padding-left: 30px; padding-right: 30px; padding-top: 5px;'> There appears to be no posts here.</div>";
} else if ($last_time > 0) {
	//print "are we here?";
	$timeline = get_timeline($dbh, $_SESSION['user'], 15, $last_time);
	if($timeline['status'] == 1) {
		if(count($timeline['posts']) > 0) {
			echo "<div class='row' style='text-align: center; font-size: 21px; padding-left: 30px; padding-right: 30px; margin-top: 35px;'> <a href='timeline.php?start=".$last_time."'>More</a></div>";
		}
	}
}

?>
	</body>
</html>
