<!DOCTYPE html>
<html>

<head>
    <title>Feed</title>
    <?php include_once 'scripts.php'; ?>
</head>

<body>
    <?php include_once 'header.php'; ?>
    <h1>Feed</h1>
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
if ($db_conn) {
    // Fetches all posts by all users
    $result = executePlainSQL("SELECT Post.*, P.URL, P.description,
        T.contents, TO_CHAR(createdat, 'fmMonth DD, YYYY') AS PostDate
    FROM Post 
    LEFT JOIN Photo P ON  P.postId = Post.postId 
    LEFT JOIN TextPost T ON T.postId = Post.postId
    ORDER BY Post.createdAt desc");
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<div class='post'>";
        if (array_key_exists("URL", $row)) {
            // Photo Post
            echo "<a href='post.php?id=" . $row["POSTID"] . "'>";
            echo "<img src='" . $row["URL"] . "' width=600 height=600>";
            echo "</a>";
            echo "<p>" . $row["DESCRIPTION"] . "</p>";       
        } else {
            // Text Post
            echo "<a href='post.php?id=" . $row["POSTID"] . "'>";
            echo "<p>" . $row["CONTENTS"] . "</p>";
            echo "</a>";            
        }

        echo "<div class='post-info'>";
        echo "<span class='likes'><i class='fa fa-heart' aria-hidden='true'></i>" . $row["LIKES"] . "</span>";
        echo "<a href='profile.php?name=" . $row["USERNAME"] . "'>";
        echo "<span class='author'><i class='fa fa-user' aria-hidden='true'></i>" . $row["USERNAME"] . "</span>";
        echo "</a>";
        echo "<span class='date'><i class='fa fa-calendar-o' aria-hidden='true'></i>" . $row["POSTDATE"] . "</span>";
        echo "</div></div>";
    }

	//Commit to save changes...
	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>
</body>

</html>