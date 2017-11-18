<!DOCTYPE html>
<form method="POST" action="addpost2.php">

<html>

<head>
    <title>Add post</title>

    <?php include_once 'scripts.php'; ?>
</head>

    <?php include_once 'utils.php'; ?>

<body>
    <?php include_once 'header.php'; ?>
    
    <?php if ( $_SESSION["ispro"] == true ) : ?> 

<h1>Add post</h1>    

<div>
    <label>Input a TextPost below:</label>
    <input type="text" name="contents" size = "500">
</div>

<div>
    <input type="submit" value="addTextPost" name="addText"></p>

    <p> Input photo info below:</p>
</div>

<div>
    <label>Photo URL</label>
    <input type="text" name="URL" size = "200">
</div>

<div>
    <label>Photo height</label>
    <input type="text" name="height" size = "10">
</div>

<div>
    <label>Photo width</label>
    <input type="text" name="width" size = "10">
</div>

<div>
    <label>description</label>
    <input type="text" name="description" size = "500" >
</div>

<div>
    <label>Photo album Id</label>
    <input type="text" name="albId" size = "20">
</div>

<div>
    <input type="submit" value="addPhotoPost" name="addPhoto"></p>
</div>

<?php endif; ?> 
       
<?php
if ($db_conn) { 
  
    if (array_key_exists('addText', $_POST)) {
        $tuple = array (
        ":bind1" => $_POST["contents"],
          ":postId1"=> mt_rand(100000,999999),
          ":username" =>$_SESSION['username']    
        );
        $alltuples = array (
            $tuple
        );
    
        executeBoundSQL("insert into Post values ( :postId1, SYSDATE, :username, 0)", $alltuples);

        executeBoundSQL("insert into TextPost values ( :postId1, :bind1)", $alltuples);
        OCICommit($db_conn);
        echo "you have successfully added a new text post!";
    } 
   
    if (array_key_exists('addPhoto', $_POST)) {
        
        
        $tuple = array (
        ":postId2" => mt_rand(100000,999999),
        ":username2"=> $_SESSION['username'],
        ":bind2" => $_POST["URL"],
        ":bind3" => $_POST["description"],

        ":bind4" => $_POST["height"],
        ":bind5" => $_POST["width"],
        ":bind6" => $_POST["albId"]
               
        );
        $alltuples = array (
            $tuple
        );

        
        executeBoundSQL("insert into Post values ( :postId2, SYSDATE, :username2, 0)", $alltuples);

        executeBoundSQL("insert into Photo values ( :postId2, :bind2, :bind3, :bind4, :bind5, :bind6)", $alltuples);
        OCICommit($db_conn);
         echo "you have successfull added a new photo post!";
    }
}
?> 

</body>

</html>