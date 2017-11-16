<!DOCTYPE html>
<html>

<head>
    <title>User Profile</title>
    <?php include_once 'scripts.php'; ?>
</head>

<body>
    <?php include_once 'header.php'; ?>
    <h1>User Profile</h1>
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

function executeBoundSQL($cmdstr, $list) {
    /* Sometimes the same statement will be executed for several times ... only
     the value of variables need to be changed.
     In this case, you don't need to create the statement several times; 
     using bind variables can make the statement be shared and just parsed once.
     This is also very useful in protecting against SQL injection.  
      See the sample code below for how this functions is used */

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
    // Fetches all posts by all users
      $userName = $_GET["name"];
    if(!$userName){
        echo "<h1>Unknown username</h1>";
        return;
    }

     $postparams = array(":username" => $userName);
    $result = executeBoundSQL("SELECT ProUser.*
    FROM ProUser
    WHERE ProUser.username = :username", array($postparams));

    while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {

        echo "<h1>".$row["USERNAME"]."</h1>";
        echo "<a href='https://".$row["PROFILEURL"]."'>";
        echo "<p>".$row["PROFILEURL"]."</p>";
        echo "</a>";
        echo "<p>" . $row["SIGNATURE"] . "</p>";     
}

echo "<p>Album &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp; Number of Likes</p>";     


   $mostlikes_results= executeBoundSQL("SELECT A.name, SUM(Post.likes) AS sumlikes
FROM ProUser U, Album A, Photo P, Post
WHERE A.username = U.username AND P.albId = A.albId AND Post.postId = P.postId AND U.username = :username
GROUP BY A.name
ORDER BY sumlikes desc ",array($postparams));

    while ($row = OCI_Fetch_Array($mostlikes_results, OCI_BOTH)) {
   
     echo "<p class='post-info'>".$row[NAME]."&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;<span class='likes'><i class=' fa fa-heart' aria-hidden='true' ></i>" . $row["SUMLIKES"] . "</span></p>";
  
}

echo"<h3>".$userName."'s Post</h3>";

 $userspost_results= executeBoundSQL("SELECT Post.*, P.URL, P.description,
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