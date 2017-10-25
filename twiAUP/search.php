<?php
session_start();
include "config.php";
include "private_functions.php";
include "functions.php";


$dbh = db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $DB_PW);
?>
<html>
	<head>
		<style type="text/css">
			input {
				padding: 5px;
				font-size: 14px;
			}

			table {
				border-collapse: collapse;
				width: 100%;
			}

			td {
				padding: 10px;
			}
		</style>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
		<script>
			$(document).ready(function() {
				var coorX = $("#coorX");
				var coorY = $("#coorY");
				var distance = $("#distance");
				var location = $("#location");
				$("#location").change(function() {
					console.log(this.value);
					var choice = this.value;
					switch (choice) {
						case "Paris":
							coorX.val("48.8566");
							coorY.val("-2.3522");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "London":
							coorX.val("51.5074");
							coorY.val("0.1278");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "Berlin":
							coorX.val("52.5200");
							coorY.val("-13.4050");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "Palaiseau":
							coorX.val("48.7145");
							coorY.val("-2.2457");
							coorX.attr("readonly",true);
							coorY.attr("readonly",true);
							break;
						case "Antony":
							coorX.val("48.7593");
							coorY.val("-2.3026");
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

				$("#includeLoc").change(function() {
			        if($(this).is(":checked")) {
			        	coorX.attr("required", true);
			        	coorY.attr("required", true);
			        	distance.attr("required", true);
			        	location.attr("disabled", false);
			        	coorX.attr("readonly", false);
			        	coorY.attr("readonly", false);
			        	distance.attr("readonly", false);

			        }
					else{
			        	coorX.attr("required", false);
			        	coorY.attr("required", false);
			        	distance.attr("required", false);
			        	coorX.attr("readonly", true);
			        	coorY.attr("readonly", true);
			        	distance.attr("readonly", true);
			        	location.attr("disabled", true);
					}

				});
			});
		</script>
		<title>TwiAUP</title>
		<?php html_output_head(); ?>
	</head>
	<body>
 		<div class="container">
			<?php html_nav('search', $_SESSION['user']); ?>

			<div class="row" style='border-bottom: 1px solid #ccc;'>
				<form action='search.php' method='POST'>
				<div class="row" style='padding-left: 30px; padding-right: 30px; padding-top: 5px;'>
					<div class="col-md-3"> <b>Keyword</b><br>(required *): </div>
					<div class="col-md-9">
<?php

					echo '<input type="text" class="form-control" value="'.$_POST['keyword'].'"
						  name="keyword" placeholder="keyword of post" required autofocus>';
?>
					</div>
				</div>
				<hr>
				<div class="row" style='padding-left: 30px; padding-right: 30px; padding-top: 5px;'>
					<div class="col-md-3">
						<div class="checkbox">
							<label>
								<input type="checkbox" name="includeLoc" id="includeLoc"><b>Location</b>
							</label>
						</div>
						(optional - Include range in search)
					</div>
          <div class="col-md-9">
            <div class="row">
						<select id="location" class="form-control" disabled>
							<option value="customize">Custom Location</option>
							<option value="Paris">Paris</option>
							<option value="London">London</option>
							<option value="Berlin">Berlin</option>
							<option value="Palaiseau">Palaiseau</option>
							<option value="Antony">Antony</option>
            </select>

            <input type="number" class="form-control" name="coorX" id="coorX" placeholder="coordinate X" readonly>
						<input type="number" class="form-control" name="coorY" id="coorY" placeholder="coordinate Y" readonly>
						<br>Distance:<input type="number" class="form-control" id="distance" name='range' min="0" placeholder="distance" readonly>
          </div>
				<div class="col-md-5"> <button class="btn btn btn-primary" type="submit">Search</button> </div>
				</form>
			</div>
		</div>


<?php

$num_results = 0;
if(isset($_POST['keyword'])) {
	$coorX = $_POST['coorX'];
	$coorY = $_POST['coorY'];
	$locRange = $_POST['range'];
	$numHashTag = $_POST['numHashTag'];
	$keyword = $_POST['keyword'];
	error_log("Key:".$keyword.", location:".strval($coorX).",".strval($coorY).",".strval($locRange).", No hashtag:".strval($numHashTag));
	$include_range = $_POST['includeLoc'];
	if(!isset($_POST['includeLoc'])){
		error_log("A regular search on keyword (".$_POST['keyword'].")");
		$resp = search($dbh, $_POST['keyword'], 100);
	}
	else{
		error_log("Should call another search function!");
		$resp = search_range($dbh, $keyword, $coorX, $coorY, $locRange, 100);
	}

	if($resp['status'] == 1) {
		$posts = $resp['posts'];
		for($i = 0; $i < count($posts); $i++) {
			html_post($dbh, $posts[$i]);
			$num_results++;
		}
		if($num_results == 0){
			echo '<p>There appears to be no posts here</p>';
		}
	} else {
		echo "There was an error with your search";
	}
}

?>
	</body>
</html>
