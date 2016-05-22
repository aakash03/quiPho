<?php

	$servername = "localhost";
	$username = "root";
	$password = "";
	
	$conn = mysqli_connect($servername, $username, $password,'quizivr');
	
	if( !$conn ) {
		die("Unable to connect to db.");
	}
?>
