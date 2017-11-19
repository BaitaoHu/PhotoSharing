<!DOCTYPE html>
<html>

<head>
    <title>User Profile</title>
    <?php include_once 'scripts.php'; ?>
</head>

<?php include_once 'utils.php'; ?>
<body>
    <?php include_once 'header.php'; ?>
    <h1>User Profile</h1>
    
<?php

if ($db_conn) {
    // Fetches all posts by all users
    $userName = $_GET["name"];
    if(!$userName){
        echo "<h1>Unknown username</h1>";
        return;
    }

    // Get basic user info. Only ProUsers have profiles,
    // so we can join the NormalUser table with the ProUser table
    // to obtain all the data we need.
    $postparams = array(":username" => $userName);
    $result = executeBoundSQL("SELECT ProUser.*, N.birthday, N.email
    FROM ProUser, NormalUser N
    WHERE ProUser.username = :username and ProUser.username = N.username ", array($postparams));

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
        echo "<h1>".$row["USERNAME"]."</h1>";
        echo "<a href='https://".$row["PROFILEURL"]."'>";
        echo "<p>".$row["PROFILEURL"]."</p>";
        echo "</a>";
        echo "<p>" . $row["SIGNATURE"] . "</p>";    
        echo "<p>Birthday:" . $row["BIRTHDAY"] . "</p>";   
        echo "<p>Email address:" . $row["EMAIL"] . "</p>";    
    }

    // List all the user's albums and the sum of the number of likes within them.
    // We have to join the Post, Album, Photo and ProUser tables together to get all the data we need.
    // We GROUP BY album then use the aggregate operation SUM to sum the number of likes in each album
    echo "<p>Album &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; Number of Likes</p>";     

    $mostlikes_results= executeBoundSQL("SELECT A.name, SUM(Post.likes) AS sumlikes
FROM ProUser U, Album A, Photo P, Post
WHERE A.username = U.username AND P.albId = A.albId AND Post.postId = P.postId AND U.username = :username
GROUP BY A.name
ORDER BY sumlikes desc ",array($postparams));

    while ($row = OCI_Fetch_Array($mostlikes_results, OCI_BOTH)) {
        echo "<p class='post-info'>".$row[NAME]."&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<span class='likes'><i class=' fa fa-heart' aria-hidden='true' ></i>" . $row["SUMLIKES"] . "</span></p>";
  
    }

    // List all the user's posts
    echo"<h3>".$userName."'s Post</h3>";

    /* To obtain the entire contents of a Post, we have to join it to either Photo or TextPost 
    so we know who created it and the number of likes (from Post), 
    but also the textual or visual contents of that Post (from Photo or TextPost).
    
    A left join to Photo and TextPost is used because we don't know what type of Post it is,
    but we want it's unique attributes. That means for a TextPost, Photo-specific attributes will be null,
    and vice versa.*/
    $userspost_results = executeBoundSQL("SELECT Post.*, P.URL, P.description,
        T.contents, TO_CHAR(createdat, 'fmMonth DD, YYYY') AS PostDate
    FROM Post 
    LEFT JOIN Photo P ON  P.postId = Post.postId 
    LEFT JOIN TextPost T ON T.postId = Post.postId
    WHERE Post.username= :username
    ORDER BY Post.createdAt desc",array($postparams));

    while ($row = OCI_Fetch_Array($userspost_results, OCI_BOTH)) {
   
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
        
        echo "<span class='author'><i class='fa fa-user' aria-hidden='true'></i>" . $row["USERNAME"] . "</span>";
       
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
