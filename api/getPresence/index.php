<?php
	include("../conector.php");
?>

<?php
	$conec = new Conector();
	$mensagem = "";
	$data = Array();
	$codigo = 0;
	$usuarios = $conec->getParticipantInfo($_REQUEST["email"]);

	if ($usuarios != false) {
		$data = $conec->getPresenca($_REQUEST["email"]);
		$mensagem = "Usuário encontrado";
		$codigo = 200;
	}
	else {
		$mensagem = "Usuário não encontrado";
		$codigo = 400;
	}

	$conec->desconectar();
	echo json_encode(Array("message" => $mensagem, "code" => $codigo, "data" => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
