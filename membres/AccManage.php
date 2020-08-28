<?php 
	session_start();
	if(empty($_SESSION['user']))
	{
	header("Location:../index.php");
	}
	include("../connectDataBase.php");
	$dbh=db_connect();

?>
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
?>
    <body>
        <div id="page">
            <div id="content">
			<?php
				if(isset($_GET['code']) && $_GET['code']=='changeok')
				{
					echo "Password changed";
				}
				if(isset($_GET['code']) && $_GET['code']=='delok')
				{
					echo "Study droped";
				}
				if(isset($_GET['up']) && $_GET['up']=='upOK')
				{
					echo "Study update";
				}
				if(isset($_GET['code']) && $_GET['code']=='roleup')
				{
					echo "New admin";
				}
				if(isset($_GET['code']) && $_GET['code']=='deluser')
				{
					echo "User droped";
				}
				if(isset($_GET['code']) && $_GET['code']=='cleardata')
				{
					echo "All data files droped";
				}
				if(isset($_GET['code']) && $_GET['code']=='colorchange')
				{
					echo "Color update";
				}
			?>
			<br>
			<b>Change your password:</b>
				<br></br>
						<form action="ChangePwd.php" method="post" enctype="multipart/form-data">	<!-- send file and info to the php file -->
							<b>Last password:</b>
							<input type="password" name="lastpwd" />
							<?php
								if(isset($_GET['code']) && $_GET['code']=='pwd')
								{
								echo "Wrong password";
								}
							?>
							<br>
							<b>New password:</b>
							<input type="password" name="newpwd" />
							<?php
								if(isset($_GET['code']) && $_GET['code']=='pwdr')
								{
								echo "Wrong repeat";
								}
							?>
							<br>
							<b>Repeat new password:</b>
							<input type="password" name="newpwd2" />
							<br>
							<input type="submit" value="Change" />
						</form>
				<br></br>
<?php
				$sql="select mail from membres where pseudo='".$_SESSION['user']."'";
				$result = pg_query($sql);
				$row= pg_fetch_array($result);
				$mail = $row[0];
				
				?>
			<b>Change your mail:</b>
			<br></br>
					<form action="mailup.php" method="post" enctype="multipart/form-data">	<!-- send file and info to the php file -->
						<b>EMail:</b>
						<input type="text" name="mail" value=<?php echo $mail; ?> />
						<?php
								if(isset($_GET['code']) && $_GET['code']=='mailchange')
								{
								echo "Your email address has been changed successfully";
								}
						?><br />
						<b>Password:</b>
						<input type="password" name="mail_pwd" />
						<?php
								if(isset($_GET['code']) && $_GET['code']=='mail_wrongpwd')
								{
								echo "Wrong password";
								}
						?>
						<br/>
						<input type="submit" value="Change" />
					</form>
			<br></br>
			<?php
				if($_SESSION['admin']==true)
				{
			?>
				<object data="GenUserTab.php" type="text/html" width="520" height="100"></object>
				<br></br>
				<b>Delete user:</b>
				<br>
				<form action="deleteUser.php" method="post">
					<input type="text" name="ref">
					<?php
						if(isset($_GET['code']) && $_GET['code']=='autodel')
						{
							echo "You can't drop your own account";
						}
					?>
					<input type="submit" value="Delete User">
				</form>
				<br></br>
				<b>Make new administrator:</b>
				<br>
				<form action="RoleUp.php" method="post">
					<input type="text" name="ref">
					<input type="submit" value="Update">
				</form>
				<br></br>
				<b>Clear all data files:</b>
				<br>
				<form action="cleardatafiles.php" method="post">
					<input type="submit" value="Clear">
				</form>
				<br /><br /><br /><br /><br /><br /><br /><br /><br />
			<?php
				}
			?>
			</div>
			<!-- end #content -->
    	</div>    
    <!-- end #page -->

</body>
<?php
db_disconnect($dbh);
include("../footer.php");
?>
</html>