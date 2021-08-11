<?php
	use GrahamCampbell\ResultType\Result;

	include_once __DIR__ . '/../api/vendor/autoload.php';

	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->load();

	$link = mysqli_connect($_ENV['local'], $_ENV['nome'], $_ENV['senha'], $_ENV['db']);
	// mysql_set_charset('utf8');

	if (!$link) {
		echo "Failed to connect to MySQL: ".mysqli_connect_errno() . PHP_EOL;
	}
?>
