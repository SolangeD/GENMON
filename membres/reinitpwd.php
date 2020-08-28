<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>GenMon-CH</title>
		<style type="text/css">
		</style>
		<link rel="stylesheet" href="../style.css" media="screen"/>
		<!--<script type="text/javascript" src="js/cssrefresh.js"></script>-->
	</head>    
<?php 
include("../header.php");
include("../connectDataBase.php");
$dbh=db_connect();
?>
    <body >
        <div id="page">
            <div id="content">
			<br></br>
			<?php
				if(isset($_GET['error']) && $_GET['error']=='mail')
				{
					echo "<er>You haven't entered a email address</er>";
				}
				if(isset($_GET['error']) && $_GET['error']=='ref')
				{
					echo "<er>Wrong pseudo and/or mail</er>";
				}
			?>
			
				<br></br>
						<form action="sendnewpwd.php" method="post" enctype="multipart/form-data">	<!-- send file and info to the php file -->
							<b>Pseudo:</b>
							<input type="text" name="pseudo" />
							<br>
							<b>email:</b>
							<input type="text" name="mail" />
							<br>
							<input type="submit" value="Change" />
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