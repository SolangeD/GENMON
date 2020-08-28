<?php 
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GenMon-CH</title>
		<style type="text/css">
		</style>
		<link rel="stylesheet" href="../style.css" media="screen"/>

	</head> 
<?php 
include("../header.php");
include("../connectDataBase.php");
$dbh=db_connect();
?>
        <div id="page">
            <div id="content">
					<form action="GenUserDb.php" method="post" enctype="multipart/form-data">	<!-- send file and info to the php file -->
                        <b>Pseudo:</b>
                        <input type="text" name="pseudo" />
						<?php
							if(isset($_GET['code']) && $_GET['code']=='nopseudo')
							{
								echo "Please choose a pseudo";
							}
							if(isset($_GET['code']) && $_GET['code']=='public')
							{
								echo "You can not choose this pseudo";
							}
							if(isset($_GET['code']) && $_GET['code']=='pseudo')
							{
								echo "This pseudo already exist";
							}
						?>
						<br>
						<b>Password:</b>
                        <input type="password" name="mdp" />
						<br>
						<b>Verify password:</b>
                        <input type="password" name="mdp2" />
						
						<?php
						if(isset($_GET['code']) && $_GET['code']=='pwd_diff')
							{
								echo "This password needs to be identical to the previous one";
							}
						?>
						<br/>
						<b>Mail:</b>
                        <input type="text" name="mail" />
						<?php
						if(isset($_GET['code']) && $_GET['code']=='pwd_diff')
							{
								echo "This password needs to be identical to the previous one";
							}
						?>
						<br>
						<input type="submit" value="Add" />
                    </form>
			</div>
			<!-- end #content -->
    	</div>    
    <!-- end #page -->
	</body>
	<?php
include("../footer.php");
?>
</html>
