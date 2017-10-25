function vote(pID, btn) {
	var jqxhr = $.ajax( "api/vote.php?pID="+pID)
	.done(function(resp) {
		var json = $.parseJSON(resp);
		//alert("here????"+json['status']);
		if(json['status'] == 1) {
			updateVotes(pID);
		} 
		else if(json['status'] == -2) {
      		alert("You've already voted for this post!");
    	} 
    	else {
			alert("You cannot vote for your own post!");
		}
	})
}

function unvote(pID, btn) {
	var jqxhr = $.ajax( "api/unvote.php?pID="+pID)
	.done(function(resp) {
		var json = $.parseJSON(resp);
		//alert("here????"+json['status']);
		if(json['status'] == 1) {
			updateVotes(pID);
		} 
		else if(json['status'] == -2) {
      		alert("You haven't vote for this post!");
    	} 
    	else {
			alert("You cannot vote for your own post!");
		}
	})
}

function updateVotes(pID) {
	var jqxhr = $.ajax( "api/get_num_votes.php?pID="+pID+"&r="+(new Date()).getTime() )
	.done(function(resp) {
		var json = $.parseJSON(resp);
    console.log(json);
		if(json['status'] == 1) {
			postRow = document.getElementById("post-"+pID);
      console.log(postRow.children);
			postRow.children[1].children[1].innerHTML = json['count'] + " votes";
		} 
	})
}
