<!DOCTYPE html>
<form method="POST" action="addpost.php">

<html>

<head>
    <title>Add post</title>

    <?php include_once 'scripts.php'; ?>
</head>

    <?php include_once 'utils.php'; ?>

<body>
    <?php include_once 'header.php'; ?>
    <?php if (!isset($_COOKIE["username"]) || !$_COOKIE["ispro"]) {
        header("location: login.php");
    } ?>    

<h1>Add post</h1>    

<div>
    <label>Input a TextPost below:</label><br>
    
     <input type="text" name="contents">
</div>

<div>
    <input type="submit" value="Add Text Post" name="addText"></p>
<hr>
    <p> Input photo info below:</p>
</div>

<div>
    <label>Photo URL</label>
    <input type="text" name="URL">
</div>

<div>
    <label>Photo height</label>
    <input type="text" name="height">
</div>

<div>
    <label>Photo width</label>
    <input type="text" name="width">
</div>

<div>
    <label>Description</label>
    <input type="text" name="description" >
</div>

<div>
    <label>Album name</label>
    <input type="text" name="albumname" >
</div>

<div>
    <input type="submit" value="Add Photo Post" name="addPhoto"></p>
</div>
       
<?php
if ($db_conn) { 
  
    if (array_key_exists('addText', $_POST)) {
        $postId = mt_rand(10000,999999);
        $tuple = array (
            ":bind1" => $_POST['contents'],
            ":postId1" => $postId,
            ":username" =>$_COOKIE['username']    
        );
        $alltuples = array (
            $tuple
        );
        
        /* Insert a new TextPost. We first add the Post, since TextPost has a FK to it */
        executeBoundSQL("INSERT INTO Post VALUES (:postId1, SYSDATE, :username, 0)", $alltuples);
        executeBoundSQL("INSERT INTO TextPost VALUES (:postId1, :bind1)", $alltuples);
        OCICommit($db_conn);
        // Redirect to newly added post
        header("location: post.php?id=" . $postId);
    } 
   
    if (array_key_exists('addPhoto', $_POST)) {
        $tuple = array(
            ":username" => $_COOKIE['username'],
            ":albumname" => $_POST['albumname']
        );

        // Check if the album name exists under the current user.
        $result = executeBoundSQL('SELECT DISTINCT albId
                                    FROM Album 
                                    WHERE Album.username = :username AND Album.name = :albumname', array($tuple));

        $postId = mt_rand(10000,999999);
        $albId = 0;

        if(oci_num_rows($result) > 0) {
            // Album exists. We will add the Photo to this Album later
            $albId = 0;
            while ($row = OCI_Fetch_Array($mostlikes_results, OCI_BOTH)) {
                $albId = $row["ALBID"];
            };
        } else {
            // Album does not exist, so we will create it on behalf of the user.     
            $albId = mt_rand(10000,999999);

            // Create Album for the user under the provided Album name
            executeBoundSQL('INSERT INTO Album VALUES (:albId, :albName, :username)', array(array(
                ":albId" => $albId,
                ":albName" => $_POST["albumname"],
                ":username" => $_COOKIE["username"]
            )));

            OCICommit($db_conn);
        }

        
        // Insert new Post
        executeBoundSQL('INSERT INTO Post VALUES (:postId, SYSDATE, :username, 0)', array(array(
            ":username" => $_COOKIE['username'],
            ":postId" => $postId
        )));

        // Insert new Photo using provided user data linked to the Post we created above
        // and to the album we specified
        executeBoundSQL('INSERT INTO Photo VALUES (:postId, :url, :description, :height, :width,  :albId)', array(array(
            ":postId" => $postId,
            ":url" => $_POST["URL"],
            ":description" => $_POST["description"],
            ":height" => $_POST["height"],
            ":width" => $_POST["width"],
            ":albId" => $albId
        )));
        
        OCICommit($db_conn);
        // Redirect to created post
        header("location: post.php?id=" . $postId);
    }
}
?>
</body>

</html>
