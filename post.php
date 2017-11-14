<!DOCTYPE html>
<html>
    <head>
    <title>Post Details</title>
    </head>
    <style>
        html, body {
            width: 800px;
            margin: 0 auto;
            text-align: center;
            font-family: 'Barlow', sans-serif;
        }

        img {
            display: block;
            margin: auto;
            box-shadow: 2px 2px 2px 0px #7d7d7d96;
        }

        .post {
            border: 1px solid lightgray;
            padding: 10px 30px;
            margin: 10px;
        }

        .post-info {
            color: #4e4e4e;
        }

        .post-info > span {
            margin-right: 1em;
        }

        .post-info .fa {
            margin-right: 0.4em;
            color: black;
        }

        .post-info .fa-heart {
            color: red;
        }

        .comment-container {
            margin: 5px;
            padding: 5px;
            border-bottom: 1px solid #80808099;        
        }

        .comment {
            position: relative;
            padding: 10px;
        }

        .comment-details {
            text-align: right;
        }

        .comment-box {
            width: 400px;
            margin: auto;
            margin-top: 15px;
            margin-bottom: 50px;
        }

        .comment-box textarea {
            width: 100%;
        }

        .comment-box input[type=button] {
            display: block;
            float: right;
            margin-top: 10px;
        }
    </style>
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Barlow" rel="stylesheet">
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<body>
<h1>Post Details</h1>
<?php
$success = True;
$config = include('config.php');
$db_conn = OCILogon($config["db_username"], $config["db_password"], "dbhost.ugrad.cs.ubc.ca:1522/ug"); // Change this!

/**
 * Executes a plain SQL commands.
 * Adapted from the CPSC304 PHP tutorial.
 */
function executePlainSQL($cmdstr) { 
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	$r = OCIExecute($statement, OCI_DEFAULT);
	if (!$r) {
		echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
		$e = oci_error($statement); // For OCIExecute errors pass the statementhandle
		echo htmlentities($e['message']);
		$success = False;
    }

	return $statement;
}

/**
 * Executes a bound SQL command.
 */
function executeBoundSQL($cmdstr, $list) {
	global $db_conn, $success;
	$statement = OCIParse($db_conn, $cmdstr);

	if (!$statement) {
		echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
		$e = OCI_Error($db_conn);
		echo htmlentities($e['message']);
		$success = False;
	}

	foreach ($list as $tuple) {
		foreach ($tuple as $bind => $val) {
			//echo $val;
			//echo "<br>".$bind."<br>";
			OCIBindByName($statement, $bind, $val);
			unset ($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype

		}
		$r = OCIExecute($statement, OCI_DEFAULT);
		if (!$r) {
			echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
			$e = OCI_Error($statement); // For OCIExecute errors pass the statement handle
			echo htmlentities($e['message']);
			echo "<br>";
			$success = False;
		}
    }
    
    return $statement;
}

if ($db_conn) {
    if (isset($_POST['type'])) {
        $type = $_POST['type'];

        if ($type == 'add-comment') {
            // Add new comment to post
            $comment = trim($_POST["comment"]);
            if (!$comment) {
                return;
            }

            $commentparams = array(
                ":postId" => $_POST["postid"], 
                ":response" => $comment,
                ":username" => 'Dominic'); // TODO: Until we have login...
            $add_comment_results = executeBoundSQL("INSERT INTO Response (postId, content, 
            datePosted, username)
            VALUES (:postId, :response, SYSDATE, :username)", array($commentparams));

            OCICommit($db_conn);
            exit;
        } else if ($type == 'add-like') {
            $likeparams = array(":postId" => $_POST["postid"]);
            executeBoundSQL("UPDATE Post
            SET LIKES = LIKES + 1
            WHERE postId = :postId", array($likeparams));
            echo $_POST["postid"];
            OCICommit($db_conn);
            exit;
        }
    }

    $postId = $_GET["id"];
    if (!$postId) {
        echo "<h1>Unknown post id</h1>";
        return;
    }

    // Fetch post
    $postparams = array(":postid" => $postId);
    $result = executeBoundSQL("SELECT Post.*, P.URL, P.description,
        T.contents, A.name AS AlbumName, TO_CHAR(createdat, 'fmMonth DD, YYYY') AS PostDate
    FROM Post 
    LEFT JOIN Photo P ON  P.postId = Post.postId 
    LEFT JOIN TextPost T ON T.postId = Post.postId
    LEFT JOIN Album A ON P.albId = A.albId
    WHERE Post.postId = :postid", array($postparams));

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<div class='post'>";
        if (array_key_exists("URL", $row)) {
            // Photo Post
            echo "<img src='" . $row["URL"] . "' width=600 height=600>";
            echo "<p>" . $row["DESCRIPTION"] . "</p>";       
        } else {
            // Text Post
            echo "<p>" . $row["CONTENTS"] . "</p>";            
        }

        echo "<div class='post-info'>";
        echo "<span class='likes'><a href='javascript:addLike();' title='Click to like!'><i class='fa fa-heart' aria-hidden='true'></i></a>" . "<span id='numlikes'>" . $row["LIKES"] . "</span></span>";
        echo "<span class='author'><i class='fa fa-user' aria-hidden='true'></i>" . $row["USERNAME"] . "</span>";
        echo "<span class='date'><i class='fa fa-calendar-o' aria-hidden='true'></i>" . $row["POSTDATE"] . "</span>";
        if (array_key_exists("URL", $row)) {
            echo "<span class='album'><i class='fa fa-book' aria-hidden='true'></i>" . $row["ALBUMNAME"] . "</span>";
        }
        echo "</div></div>";
    }

    // Fetch comments
    $comment_results = executeBoundSQL("SELECT R.username, R.content,
    TO_CHAR(R.datePosted, 'fmMonth DD, YYYY') AS DatePosted 
    FROM Response R, Post P
    WHERE P.postId = :postid AND R.postId = P.postId
    ORDER BY R.datePosted ASC", array($postparams));

    echo "<h2>Comments</h2>";
    while ($row = OCI_Fetch_Array($comment_results, OCI_BOTH)) {
        echo "<div class='comment-container'><div class='comment'>";
        echo "<div class='comment-content'>" . $row["CONTENT"] . "</div>";
        echo "<div class='comment-details'> - " . $row["USERNAME"] . ", " . 
            $row["DATEPOSTED"] . "</div>";
        echo "</div></div>";
    }

	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>

<!-- For adding new comments -->
<div class="comment-box">
    <textarea id="comment-contents" type="text" rows="5"></textarea>
    <input type="button" value="Add Comment" onclick="addComment();"></p>
</div>

<script>
function addLike() {
    $.ajax({
        type: "POST",
        url: "post.php",
        data: { 
            type: 'add-like', 
            postid: <?php echo $_GET["id"]; ?> 
        }, 
        success: function(data){
            location.reload();
        }
    });
}

function addComment() {
    $.ajax({
        type: "POST",
        url: "post.php",
        data: { 
            type: 'add-comment', 
            postid: <?php echo $_GET["id"]; ?>,
            comment: document.getElementById("comment-contents").value
        }, 
        success: function(data){
            location.reload();
        }
    });
}
</script>
</body>
</html>
