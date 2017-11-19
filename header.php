<!-- Header included in every PHP file -->

<div id="navbar">
    <a href="index.php" style="float:left">Home</a>
    <?php if (isset($_COOKIE["ispro"]) && $_COOKIE["ispro"]) {
    	echo"<a href='addpost.php' style='float:left'>Add</a>";
    }
   
    if (isset($_COOKIE["username"])) {
        echo "<a href='logout.php' style='float: right'>Logout</a>";
        echo "<a href='setting.php' style='float:right'>Setting</a>";
    } else {
        echo "<a href='login.php' style='float: right'>Login</a>";
    } ?>

    </div>
Make sure you add your Oracle login info! See the source of this file for details.
