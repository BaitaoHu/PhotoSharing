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
    
     <input type="text" name="contents">

    
</div>

<div>
    <input type="submit" value="addTextPost" name="addText"></p>

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
        ":bind6" => $_POST["albumname"],
        ":bind7" => executePlainSQL("SELECT DISTINCT albId
                                     FROM Album 
                                     WHERE Album.username = :username2 AND Album.name = :bind6")
                         );
       
        $alltuples = array (
            $tuple
        );

       $result = executePlainSQL("SELECT DISTINCT albId
                                     FROM Album 
                                     WHERE Album.username = :username2 AND Album.name = :bind6");
       
    if(mysql_num_rows($result)>0){
        executeBoundSQL("insert into Post values ( :postId2, SYSDATE, :username2, 0)", $alltuples);



        executeBoundSQL("insert into Photo values ( :postId2, :bind2, :bind3, :bind4, :bind5, :bind7)", $alltuples);
        
        OCICommit($db_conn);
        echo "you have successfull added a new photo post!";
           } else {

         $albumId = mt_rand(100000,999999);
        
        executeBoundSQL("insert into Album values ( albumId, :bind6, :username2)", $alltuples);
       
        executeBoundSQL("insert into Post values ( :postId2, SYSDATE, :username2, 0)", $alltuples);

        executeBoundSQL("insert into Photo values ( :postId2, :bind2, :bind3, :bind4, :bind5, :bind7)", $alltuples);
        
        OCICommit($db_conn);
        echo "A new Album was created and your photo was added into it successfully!";

           } 
    }
}
?> 

</body>

</html>