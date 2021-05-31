<?php
	$link = mysqli_connect("localhost", "root", "", "saphira");
	// mysql_set_charset('utf8');

	if (!$link) {
		echo "Failed to connect to MySQL: ".mysqli_connect_errno() . PHP_EOL;
	}
?>
