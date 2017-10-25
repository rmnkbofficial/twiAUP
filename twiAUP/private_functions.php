<?php

include "config.php";

function session_login($user) {
	$_SESSION['auth'] = 1;
	$_SESSION['user'] = $user;
}

function hash_pw($pw) {
	global $salt;
	return md5(md5(md5($pw.$salt)));
}

function user_link($user) {
	return '<a href="user.php?user='.$user.'">'.htmlentities($user).'</a>';
}

function html_output_head() {
	   echo '<link rel="icon" href="https://pbs.twimg.com/profile_images/507052784/aulogo_color_twitter.jpg">';
	   echo '<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>';
	   echo '<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>';
	   echo '<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">';
	   echo '<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>';
	   echo '<link rel="stylesheet" href="template.css">';
	   echo '<script src="my.js"></script>';
	   echo '<script src="api.js"></script>';
}

function html_nav($page, $user = "") {
  if(isset($_SESSION['auth']) && $_SESSION['auth'] == 1) {
		if($page == "timeline") {
			$timelineActive = " class='active' ";
		} else if ($page == "search") {
			$searchActive = " class='active' ";
		} else if ($page == "findTag") {
			$findTagActive = " class='active' ";
		} else if ($page == "stats") {
			$statsActive = " class='active' ";
		}
		echo '
			<div class="header">
        <ul class="nav nav-pills pull-right">
          <li '.$timelineActive.'><a href="timeline.php">Timeline</a></li>
          <li '.$searchActive.'><a href="search.php">Search</a></li>
          <li '.$findTagActive.'><a href="find_tags.php">Find Tag</a></li>
          <li '.$statsActive.'><a href="stats.php">Stats</a></li>
          <li><a href="logout.php">Logout</a></li>
        </ul>';

		if($user != "") {
			echo '<h3 class="text-muted">TwiAUP : '.user_link($user).'</h3>';
		} else {
			echo '<h3 class="text-muted">TwiAUP</h3>';
		}
      echo '</div>';

	} else {
		if($page == "login") {
			$loginActive = " class='active' ";
		} else if ($page == "register") {
			$registerActive = " class='active' ";
		}
		echo '
			<div class="header">
        <ul class="nav nav-pills pull-right">
          <li '.$loginActive.'><a href="login.php">Login</a></li>
          <li '.$registerActive.'><a href="register.php">Register</a></li>
        </ul>
        <h3 class="text-muted">TwiAUP</h3>
      </div>';

	}
}

date_default_timezone_set('Europe/Paris');

function format_post_time($time) {
	$date = date('m/d/Y h:i:s', $time);
	return $date;
}

function html_post($dbh, $post) {
  $votes = get_num_votes($dbh, $post['pID']);
  $num_votes = $votes['count'];
	echo '<div id="post-'.$post['pID'].'" class="row post">
			<div class="col-md-3">'.user_link($post['username']).'</div>
			<div class="col-md-9">'.htmlentities($post['title']).'<span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">'.format_post_time($post['time']).'</span>
      <span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">'.$num_votes.' votes</span>
      <button class="btn btn-xs btn-primary" onclick="vote(\''.$post['pID'].'\', this);" type="button">vote</button>
      <button class="btn btn-xs btn-primary" onclick="unvote(\''.$post['pID'].'\', this);" type="button">unvote</button>
      </div>

      		<div class="col-md-3"><span style="font-size:16px;">Loc:('.htmlentities($post['coorX']).','.htmlentities($post['coorY']).')</span></div>
			<div class="col-md-9"><span style="font-size:16px;">'.htmlentities($post['content']).'</span><span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;"></span></div>
		</div>';
}

function html_user($dbh, $user, $me = "") {
  $votes = get_num_votes_of_user($dbh, $user);
  $num_votes = $votes['count'];
  $posts = get_num_posts($dbh, $user);
  $num_posts = $posts['count'];
  $hashtags = get_num_tags_of_user($dbh, $user);
  $num_hashtags = $hashtags['count'];
	if ( preg_match('/^[A-Za-z0-9]+$/', $user) ) {
		echo '
			<div class="row user" id="user-'.$user.'">
			<div class="col-md-4">'.user_link($user).'</div>

      <span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">voted for '.$num_votes.' posts.</span>
      <span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">has '.$num_posts.' posts.</span>
      <span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">used '.$num_hashtags.' hashtags.</span>
      ';
		echo '</div>';
	}
}

function html_tag($dbh, $tag) {

  $tagname = $tag['tagname'];
  $occurence = $tag['occurence'];

  echo '
		<div class="row tag" id="tag-'.$tagname.'">
		<div class="col-md-4">'.tag_link($tagname).'</div>
  <span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">occurs '.$occurence.' times.</span>
  ';
  echo '</div>';

}

function html_tag_pair($dbh, $tagpair) {

  $tagname1 = $tagpair['tagname1'];
  $tagname2 = $tagpair['tagname2'];
  $occurence = $tagpair['occurence'];

  echo '
		<div class="row tag" id="tag-pair">
		<div class="col-md-4">'.tag_link($tagname1).'</div>
		<div class="col-md-4">'.tag_link($tagname2).'</div>
  		<span style="font-size:12px; color: #888; padding:5px; padding-left: 10px;">occurs '.$occurence.' times.</span>'
  ;
  echo '</div>';

}

function tag_link($tagname) {
	return '<a href="find_tags.php?tagname='.$tagname.'">#'.htmlentities($tagname).'</a>';
}

?>
