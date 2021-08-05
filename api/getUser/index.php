<?php
	include("../conector.php");
?>

<?php
	$conec = new Conector();
	$mensagem = "";
	$data = Array();
	$codigo = 0;
	$usuarios = $conec->getParticipantInfo($_REQUEST["doc"]);

	if ($usuarios != false) {
		$mensagem = "Usuário(s) encontrado";
		$data = $usuarios;
		$codigo = 200;
	}
	else {
		$mensagem = "Usuário(s) não encontrado";
		$codigo = 400;
	}

	$conec->desconectar();
	echo json_encode(Array("message" => $mensagem, "code" => $codigo, "data" => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
