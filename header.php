<header><!--<div id="header">-->
	<div id="logo" >
		<h1><a href="http://localhost/genmon-ch/index.php"><strong><font color="#FFFFFF">GenMon-CH</font></strong></a></h1>
	</div><!-- end #logo -->
	<div id="user" >
		
		<?php 
			$url="http://localhost/genmon-ch";
			include("/var/www/html/genmon-ch/membres/sessStarted.php");
			if ( is_session_started() === FALSE ) session_start();
			
			if(isset($_SESSION['user'])) //if user is loged in show account management and logout option
			{
			?>
			<br />
			<br />
			<?php
				echo "<b> Welcome ".$_SESSION['user']." </b>" ;
			?>
			<br />
			
			<input type="button" value="Logout" onclick="window.location='membres/logout.php'"/>
			<p>
				<b>
					<br />
					&nbsp;
					&nbsp;
					&nbsp;
					<a href="<?php echo $url;?>/membres/AccManage.php">Account Management</a>
					&nbsp;
					&nbsp;
					&nbsp;
					<a href="<?php echo $url;?>/tutorial.php">Tutorial</a>
				</b>
			</p>
		<?php
		}
		else //if user is not loged in show sign in and sign up option
		{
		?>
		
		<form action="<?php echo $url;?>/membres/login.php" method="post">
		</br>
		<?php
		if(isset($_GET['error']) && $_GET['error']=='log')
		{
		echo "<er>Wrong pseudo and/or password
		</er>";
		}
		?>
		<b>Pseudo: </b>
		<input type="text" name="pseudo"  />
		<br>
		<b>Password: </b>
		<input type="password" name="pwd" />
		<br /> 
		<input type="submit" value="login"/>
		</form> <br />
		<b>
		<a href="<?php echo $url;?>/membres/reinitpwd.php">Reinitialize password</a>
		<a href="<?php echo $url;?>/membres/signUp.php">&nbsp; &nbsp; &nbsp; Sign Up</a>		
		<a href="<?php echo $url;?>/tutorial.php">&nbsp; &nbsp; &nbsp; Tutorial</a>
		<a href="<?php echo $url;?>/pdf/data_sample.csv">&nbsp; &nbsp; &nbsp; Example data</a>
		</b>
		<?php
		}
		?>
		</div><!-- end #user -->
		</header><!-- end #header -->
		
		
				