<?php

include "config.php";

/*
 * For all functions $dbh is a database connection
 */

/*
 * @return handle to database connection
 */
function db_connect($MY_HOST, $MY_DB_PORT, $MY_DB, $DB_USER, $PWD) { 
    
    $conn_str = "host=$MY_HOST port=$MY_DB_PORT dbname=$MY_DB user=$DB_USER password=$PWD";
    $conn = pg_connect($conn_str) or die("Error, cannot connect to DB: ". pg_last_error());
    $query = 'SELECT * FROM Users';
    $result = pg_query($conn, $query) or die("Query failed: " . pg_last_error());
    return $conn;

}

/*
 * Close database connection
 */ 
function close_db_connection($dbh) {
     return pg_close($dbh);
}

/*
 * Login if user and password match
 * Return associative array of the form:
 * array(
 *		'status' =>  (1 for success and 0 for failure)
 *		'userID' => '[USER ID]'
 * )
 */
function login($dbh, $user, $pw) {
    $query = "SELECT * FROM users VALUES (username, password) WHERE username = '$user' & password = '$pw';";
    if(username == '$user' && password == '$pw') {
    pg_connect($dbh);
    $result = pg_query($query);
    $row = pg_fetch_array($result);
   if (pg_num_rows($result) > 0)
    {
    session_login($dbh, $user);
    } else die('Login failed. Please try again.');
    return array("status" => 1, "userID" => $user);
    }
    else { return array( 'status' => 0 );
    }
}
        
        
    

/*
 * Register user with given password 
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'userID' => '[USER ID]'
 * )
 */
 
function register($dbh, $user, $pw) {	
    
    /* Hash password, insert into elephant SQL with user and hashed password
    *
    */
    
    $user = pg_escape_string($user);
    $pw = pg_escape_string($pw);
    $q =  "SELECT COUNT(*) as valid FROM Users WHERE username='$user';";
    $result = pg_query($dbh, $q);
    if (!$result) {
        return array("status" => 0, "userID" => $user);
    }

    $num = pg_fetch_array($result, 0, PGSQL_ASSOC);
    if ($num["valid"] === "0") {
        $q =  "INSERT INTO Users (username, password) VALUES ('$user','$pw');";
        $result = pg_query($dbh, $q);
        if (!$result) {
            return array("status" => 1, "userID" => $user);
        }
        return array("status" => 1, "userID" => $user);
    } else {
        return array("status" => 0, "userID" => $user);
    }

}

/*
 * Register user with given password 
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 		'pID' => new post id
 * )
 */
function post_post($dbh, $title, $msg, $coorx, $coory, $me) {

register($dbh, $user, $pw);
$title = pg_escape_string($title);
$msg = pg_escape_string($msg);
$coorx = pg_escape_string($coorx);
$coory = pg_escape_string($coory);
$query = "INSERT INTO Posts (username) VALUES ('$user');";
$result = pg_query($dbh, $query);
$pid = pg_get_pid($me);
if (!$result) {
            return array("status" => 0, "userID" => $user);
        }
        else return array("status" => 1, "pID" => $pid);
     $result2 = pg_fetch_array($result, 0, PGSQL_ASSOC);
     print_r($result2);
    
}

/*
 * Attach a hashtag value to a post
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 * )
 */
function attach_hashtag($dbh, $pid, $tagname) {
$
$query = "SELECT*FROM Hashtags('text') VALUES ('$tagname');";
pg_bind_params($tagname, $pid);
$result = pg_query($dbh, $query);
if (!$result){ create_hashtag($dbh, $tagname); {
    return array("status" => 0, "pID" => $pid);
        }
  } else return array("status" => 1);

}

/*
 * Create a hashtag if not exists 
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 * )
 */
function create_hashtag($dbh, $tagname) {
$tagname = pg_escape_string($tagname);
$query = "INSERT INTO Hashtags values ('$tagname', '$user');";
$result = pg_query($dbh, $query);
if (!$result){ return array("status" => 0, "text" => $tagname);
} else return array("status" => 1);

}

/*
 * Get all posts with a given hashtag
 * Order by time of the post (going backward in time), and break ties by sorting by the username alphabetically
 * Return associative array of the form:
 * array(
 *		'status' => (1 for success and 0 for failure)
 *		'posts' => [ (Array of post objects) ]
 * )
 * Each post should be of the form:
 * array(
 *		'pID' => (INTEGER)
 *		'username' => (USERNAME)
 *		'title' => (TITLE OF POST)
 *      'content' => (CONTENT OF POST)
 *		'time' => (UNIXTIME INTEGER)
 * )
 */
function search_post_by_tag($dbh, $tagname) {
$tagname = pg_escape_string($tagname);
$query = "SELECT*FROM Hashtags('text') VALUES ('$tagname');";
pg_bind_params($tagname, $pid);
$result = pg_query($dbh, $query);
$hsarray = pg_fetch_all($result);
$query2 = "SELECT*FROM Posts ('timestamp') WHERE hashtag='$tagname' GROUP BY timestamp;";
$timestamp = pg_query($dbh, $query2);
$usorder = "SELECT*FROM Posts('timestamp') WHERE hashtag = '$tagname' GROUP BY username;"; 
$tsorder = pg_fetch_all_columns($timestamp);
pg_execute($dbh, $usorder, $tsorder);
if (!$result){ return array("status" => 0, "text" => $tagname);
} else return array("status" => 1, 
                                  'pID' => $pid
                                  'username' => $user
                                  'title' => $title
                                  'content' => $msg
		                          'time' => $timestamp);
print_r($tsorder);


}
/*
 * Get timeline of $count most recent posts that were written before timestamp $start
 * For a user $user, the timeline should include all posts.
 * Order by time of the post (going backward in time), and break ties by sorting by the username alphabetically
 * Return associative array of the form:
 * array(
 *		'status' => (1 for success and 0 for failure)
 *		'posts' => [ (Array of post objects) ]
 * )
 * Each post should be of the form:
 * array(
 *		'pID' => (INTEGER)
 *		'username' => (USERNAME)
 *		'title' => (TITLE OF POST)
 *    'content' => (CONTENT OF POST)
 *		'time' => (UNIXTIME INTEGER)
 * )
 */
function get_timeline($dbh, $user, $count = 10, $start = PHP_INT_MAX) {
$query = "SELECT*FROM Posts(username, timestamp) GROUP BY timestamp, '$user' LIMIT $count;";
$result = pg_query($dbh, $query);
$array = pg_fetch_all($result);
if (!$result){ return array( 'status' => 0, .pg_last_error());
} else return array('status'=> 1,
'pID' => $pid
'username' => $user
'title' => $title
'content' => $msg
'time' => $start
);
print_r($array);
 
}

/*
 * Get list of $count most recent posts that were written by user $user before timestamp $start
 * Order by time of the post (going backward in time)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'posts' => [ (Array of post objects) ]
 * )
 * Each post should be of the form:
 * array(
 *		'pID' => (INTEGER)
 *		'username' => (USERNAME)
 *		'title' => (TITLE)
 *		'content' => (CONTENT)
 *		'time' => (UNIXTIME INTEGER)
 * )
 */
function get_user_posts($dbh, $user, $count = 10, $start = PHP_INT_MAX) {
  $str = pg_escape_string($user);
    $str2 = pg_escape_string($count);
    $query = "SELECT * FROM Tweets WHERE username = '{$str}' LIMIT '{$count}'"; 
    $result = pg_query($dbh, $query);
    
    $array_initial = pg_fetch_all($result);
    
    var_dump($array_initial);
    
    for($j = 0; $j <= sizeof($result); $j++){
    $row = $results[$j];
    $post = array();
    $post['pid'] = $row['pid'];
    $post['username'] = $row['username'];
    $post['title'] = $row['pid'];
    $post['content'] = $row['pid'];
    $post['time'] = $row['pid'];
    $post['coorX'] = $row['coordX'];
    $post['coorY'] = $row['coordY'];
    $posts[$j] = $post;
    $i += 1;
    $array = ('status' => 1
            'posts' => $pid
    )
    return $array;
}


/*
 * Deletes a post given $user name and $pID.
 * $user must be the one who posted the post $pID.
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success. 0 or 2 for failure)
 * )
 */
function delete_post($dbh, $user, $pID) {
$query = "DELETE FROM Tweet(username) VALUES ('$user') WHERE username='$user' AND pID = '$pID';";
$result = pg_query($dbh, $query);
return $result;
if (!$result){ return array('status' => 0);
} else return array('status' => 1);

}

/*
 * Records a "vote" for a post given logged-in user $me and $pID.
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success. 0 for failure)
 * )
 */
function vote_post($dbh, $me, $pID) {
$query = "INSERT INTO Posts(votedBy) VALUES ('$me') WHERE pID='$pID';";
$result = pg_query($dbh, $query);
$votes = "SELECT*INTO FROM Posts(votes);"
$votes++;
pg_query($dbh, $votes);
if (!$result){ return array('status' => 0);
} else return array('status' => 1);



}


/*
 * Records a "unvote" for a post given logged-in user $me and $pID.
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success. 0 for failure)
 * )
 */
function unvote_post($dbh, $me, $pID) {
$query = "DELETE FROM Posts(votedBy) VALUES ('$me') WHERE pID='$pID';";
$result = pg_query($dbh, $query);
$votes = "SELECT*INTO FROM Posts(votes);"
$votes--;
pg_query($dbh, $votes);
if (!$result){ return array('status' => 0);
} else return array('status' => 1);

}


/*
 * Check if $me has already voted post $pID
 * Return true if user $me has voted for post $pID or false otherwise
 * Otherwise return false
 */
function already_voted($dbh, $me, $pID) {
$query = "SELECT*FROM Posts WHERE votedBy='$me' AND pID='$pID';"
$result = pg_query($dbh, $query);
if(!$result){ return false;
} else return true;

}


/*
 * Find the $count most recent posts that contain the string $key
 * Order by time of the post and break ties by the username (sorted alphabetically A-Z)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'posts' => [ (Array of Post objects) ]
 * )
 */
function search($dbh, $key, $count = 50) {
$key = pg_escape_string($key);
$query = "SELECT*FROM Tweet('body') VALUES ('%$key%') LIMIT '{$count}';";
$result = pg_query($dbh, $query);
$search = print_r($result);
$posts = pg_fetch_assoc($search);
if (!$result){ return array('status' => 0);
} else return array('status' => 1, 'posts' => $posts );



}

/*
 * Find the $count most recent posts that contain the string $key, and is within the range $range of ($coorX, $coorY)
 * Order by time of the post and break ties by the username (sorted alphabetically A-Z)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'posts' => [ (Array of Post objects) ]
 * )
 */
function search_range($dbh, $key, $coorx, $coory, $range, $count = 50) {
$key = pg_escape_string($key);
$query = "SELECT*FROM Tweet('body') VALUES ('%$key%') WHERE SQRT(POW(X('$coorx' - 'xcoordinates'), 2) + POW(Y('$coory' - 'ycoordinates'), 2) LIMIT '{$count}';";
$result = pg_query($dbh, $query);
}


/*
 * Get the number of votes of post $pID
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'count' => (The number of votes)
 * )
 */
function get_num_votes($dbh, $pID) {

}

/*
 * Get the number of posts of user $uID
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'count' => (The number of posts)
 * )
 */
function get_num_posts($dbh, $uID) {

}

/*
 * Get the number of hashtags used by user $uID
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'count' => (The number of hashtags)
 * )
 */
function get_num_tags_of_user($dbh, $uID) {

}

/*
 * Get the number of votes user $uID made
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'count' => (The number of likes)
 * )
 */
function get_num_votes_of_user($dbh, $uID) {

}

/*
 * Get the list of $count users that have posted the most
 * Order by the number of posts (descending), and then by username (A-Z)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'users' => [ (Array of user IDs) ]
 * )
 */
function get_most_active_users($dbh, $count = 10) {

}

/*
 * Get the list of $count posts posted after $from that have the most votes.
 * Order by the number of votes (descending)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'posts' => [ (Array of post objects) ]
 * )
 * Each post should be of the form:
 * array(
 *		'pID' => (INTEGER)
 *		'username' => (USERNAME)
 *		'title' => (TITLE OF POST)
 *    'content' => (CONTENT OF POST)
 *		'time' => (UNIXTIME INTEGER)
 * )
 */
function get_most_popular_posts($dbh, $count = 10, $from = 0) {

}

/*
 * Get the list of $count hashtags that have been used 
 * Order by the number of times being used (descending), and then by tagname (A-Z)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'tags' => [ (Array of tags) ]
 * )
 * Then each tag should have the form
 * array(
 *		'tagname' =>  (tagname)
 *		'occurence' => (number of times that is used)
 * )
 */
function get_most_popular_tags($dbh, $count = 5) {

}

/*
 * Get the list of $count tag pairs that have been used together
 * Order by the number of times being used (descending)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 *		'tags' => [ (Array of tags) ]
 * )
 * Then each tag should have the form
 * array(
 *		'tagname' =>  (tagname)
 *		'occurence' => (number of times that is used)
 * )
 */
function get_most_popular_tag_pairs($dbh, $count = 5) {

}

/*
 * Recommend posts for user $user.
 * A post $p is a recommended post for $user if like minded users of $user also voted for the post,
 * where like minded users are users who voted for the posts $user voted for.
 * Result should not include posts $user voted for.
 * Rank the recommended posts by how many like minded users voted for the posts.
 * The set of like minded users should not include $user self.
 *
 * Return associative array of the form:
 * array(
 *    'status' =>   (1 for success and 0 for failure)
 *    'posts' => [ (Array of post objects) ]
 * )
 * Each post should be of the form:
 * array(
 *		'pID' => (INTEGER)
 *		'username' => (USERNAME)
 *		'title' => (TITLE OF POST)
 *    'content' => (CONTENT OF POST)
 *		'time' => (UNIXTIME INTEGER)
 * )
 */
function get_recommended_posts($dbh, $count = 10, $user) {
	

}

/*
 * Delete all tables in the database and then recreate them (without any data)
 * Return associative array of the form:
 * array(
 *		'status' =>   (1 for success and 0 for failure)
 * )
 */
function reset_database($dbh) {

}
}

?>