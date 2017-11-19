<!DOCTYPE html>
<html>

<head>
    <title>Post Details</title>
    <?php include_once 'scripts.php'; ?>
</head>

<?php include_once 'utils.php'; ?> 
<body>
    <?php include_once 'header.php'; ?>
    <h1>Post Details</h1>
<?php

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
                ":username" => $_COOKIE['username']);

            /** Insert a new comment at the current date, posted by the currently logged in user. */
            $add_comment_results = executeBoundSQL("INSERT INTO Response (postId, content, 
            datePosted, username)
            VALUES (:postId, :response, SYSDATE, :username)", array($commentparams));

            OCICommit($db_conn);
            exit;
        } else if ($type == 'add-like') {
            // Add like to a post
            $likeparams = array(":postId" => $_POST["postid"]);
            /** Update like count on the Post by incrementing it. */
            executeBoundSQL("UPDATE Post
            SET LIKES = LIKES + 1
            WHERE postId = :postId", array($likeparams));
            echo $_POST["postid"];
            OCICommit($db_conn);
            exit;
        } else if ($type == 'delete') {
            // Delete post where the postID is equal to the post being viewed
            // Due to ON DELETE clauses, comments and such are also deleted automatically
            $deleteparams = array(":postId" => $_POST["postid"]);
            executeBoundSQL("DELETE FROM Post WHERE postId = :postId", array($deleteparams));
            OCICommit($db_conn);
            exit;
        }
    }

    $postId = $_GET["id"];
    if (!$postId) {
        echo "<h1>Unknown post id</h1>";
        return;
    }

    // Fetch post - the image/text, the date posted, the user, etc.
    // Since we don't know if it's a Photo or Text, we do a left join on both.
    // There will be some null fields as a result, but we will use these to tell us
    // which type it is.
    $postparams = array(":postid" => $postId);
    $result = executeBoundSQL("SELECT Post.*, P.URL, P.description, P.width, P.height,
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

        // Post info: likes, author, date posted, album, ...
        echo "<div class='post-info'>";
        echo "<span class='likes'><a href='javascript:addLike();' title='Click to like!'><i class='fa fa-heart' aria-hidden='true'></i></a>" . "<span id='numlikes'>" . $row["LIKES"] . "</span></span>";
        echo "<span class='author'><a href='profile.php?name=" . $row["USERNAME"] . "'><i class='fa fa-user' aria-hidden='true'></i>" . $row["USERNAME"] . "</a></span>";
        echo "<span class='date'><i class='fa fa-calendar-o' aria-hidden='true'></i>" . $row["POSTDATE"] . "</span>";
        if (array_key_exists("URL", $row)) {
            echo "<span class='album'><i class='fa fa-book' aria-hidden='true'></i>" . $row["ALBUMNAME"] . "</span>";
            echo "<span class='size'><i class='fa fa-picture-o' aria-hidden='true'></i>" . $row["WIDTH"] . "&#215;" . $row["HEIGHT"] . "</span>";
        }

        if (isset($_COOKIE["username"]) && $_COOKIE["username"] == $row["USERNAME"]) {
            echo "<span class='delete'><a href='javascript:deletePost();' title='Click to delete'><i class='fa fa-trash' aria-hidden='true'></i> Delete</a></span>";
        }
 
        echo "</div></div>";
    }

    // Fetch comments, their authors and the date posted
    // We join the Response table and Post table to get the comments for this particular post
    $comment_results = executeBoundSQL("SELECT R.username, R.content,
    TO_CHAR(R.datePosted, 'fmMonth DD, YYYY') AS DatePosted 
    FROM Response R, Post P
    WHERE P.postId = :postid AND R.postId = P.postId
    ORDER BY R.datePosted ASC", array($postparams));

    echo "<h2>Comments</h2>";
    $has_comments = false;
    while ($row = OCI_Fetch_Array($comment_results, OCI_BOTH)) {
        $has_comments = true;
        echo "<div class='comment-container'><div class='comment'>";
        echo "<div class='comment-content'>" . $row["CONTENT"] . "</div>";
        echo "<div class='comment-details'> - " . $row["USERNAME"] . ", " . 
            $row["DATEPOSTED"] . "</div>";
        echo "</div></div>";
    }

    if (!$has_comments) {
        echo "<span>No comments</span>";
    }

	OCILogoff($db_conn);
} else {
	echo "cannot connect";
	$e = OCI_Error(); // For OCILogon errors pass no handle
	echo htmlentities($e['message']);
}
?>

    <!-- For adding new comments -->
    <?php if ($_COOKIE["username"]) : ?>
    <div class="comment-box">
        <textarea id="comment-contents" type="text" rows="5"></textarea>
        <input type="button" value="Add Comment" onclick="addComment();"></p>
    </div>
    <?php endif ?>
    <script>
        function addLike() {
            $.ajax({
                type: "POST",
                url: "post.php",
                data: {
                    type: 'add-like',
                    postid: <?php echo $_GET["id"]; ?>
                },
                success: function(data) {
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
                success: function(data) {
                    location.reload();
                }
            });
        }

        function deletePost() {
            if (confirm("Are you sure you want to delete the post?")) {
                $.ajax({
                    type: "POST",
                    url: "post.php",
                    data: {
                        type: 'delete',
                        postid: <?php echo $_GET["id"]; ?>
                    },
                    success: function(data) {
                        window.location.href = "index.php";
                    }
                });
            }
        }

    </script>
</body>

</html>
