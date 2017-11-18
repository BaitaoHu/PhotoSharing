<!-- Header included in every PHP file -->

 <?php include_once 'utils.php'; ?>

<div id="navbar">
    <a href="index.php" style="float:left">Home</a>
    <?php if ( $_SESSION["ispro"] == true ){
    	echo"<a href='addpost2.php' style='float:left'>Add</a>";
   }

  ?> 

    <a href="setting.php" style="float:right">Setting</a>
</div>
Make sure you add your Oracle login info! See the source of this file for details.