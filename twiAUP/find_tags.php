<?php
session_start();
include "config.php";
include "private_functions.php";
include "functions.php";


$dbh = db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $DB_PW);
?>
<html>
	<head>
		<title>TwiAUP</title>
		<?php html_output_head(); ?>
	</head>
	<body>
 <div class="container">
		<?php html_nav('findTag', $_SESSION['user']); ?>

	  <div class="row" style='border-bottom: 1px solid #ccc;'>
		<form action='find_tags.php' method='GET'>
	  <div class="row" style='padding-left: 30px; padding-right: 30px; padding-top: 5px;'>
			<div class="col-md-10">
				<div class="input-group input-group-lg">
  					<span class="input-group-addon" id="sizing-addon1">#</span>
<?php
	if(isset($_GET['tagname'])){
		$tagname = $_GET['tagname'];
	}
	echo '<input style=\'height: 45px; padding:10px; font-size: 21px\' value="'.$_GET['tagname'].'"
	type="text" class="form-control" name=\'tagname\' placeholder="tagname" required autofocus>';

?>
				</div>
<!-- 				<input style='height: 45px; padding:10px; font-size: 21px' type="text" class="form-control" name='tagname' placeholder="tagname" required autofocus>
 -->		</div>
			<div class="col-md-2"> <button class="btn btn-lg btn-primary btn-block" type="submit">Search</button> </div>
	  </div>
	  </form>
		</div>


<?php
$num_results = 0;
if(isset($_GET['tagname'])) {
	$tagname = $_GET['tagname'];
	if(0 === strpos($tagname, "#")){
		$tagname = substr($tagname, 1);
	}

	$resp = search_post_by_tag($dbh, $tagname);
	if($resp['status'] == 1) {
		$posts = $resp['posts'];
		for($i = 0; $i < count($posts); $i++) {
			html_post($dbh, $posts[$i]);
			$num_results++;
		}
		if($num_results==0){
			echo "<p>There appears to be no posts here</p>";
		}
	} else {
		echo "There was an error with your search";
	}


}

?>


	</body>
</html>
