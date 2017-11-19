<!DOCTYPE html>
<html>

<head>
    <title>Feed</title>
    <?php include_once 'scripts.php'; ?>
</head>

<?php include_once 'utils.php'; ?>
<body>
    <?php include_once 'header.php'; ?>
    <h1>Feed</h1>
<?php
if ($db_conn) {
    /* To obtain the entire contents of a Post, we have to join it to either Photo or TextPost 
    so we know who created it and the number of likes (from Post), 
    but also the textual or visual contents of that Post (from Photo or TextPost).
    
    A left join to Photo and TextPost is used because we don't know what type of Post it is,
    but we want it's unique attributes. That means for a TextPost, Photo-specific attributes will be null,
    and vice versa.*/
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
        echo "<span class='author'><i class='fa fa-user' aria-hidden='true'></i>" . "<a href='profile.php?name=" . $row["USERNAME"] . "'>" . $row["USERNAME"] ."</a></span>";
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
