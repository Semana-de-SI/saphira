<?php
	include("../conector.php");
?>

<?php
	$conec = new Conector();
	$mensagem = "";
	$data = Array();
	$codigo = 0;

	if (!empty($_REQUEST["email"])) {
		$usuario = $conec->getParticipantInfo($_REQUEST["email"]);
		if ($usuarios != false) {
			$mensagem = "Usuário encontrado";
			$data = $usuario;
			$codigo = 200;
		}
		else {
			$mensagem = "Usuário não encontrado";
			$codigo = 400;
		}
	}
	else {
		$mensagem = "Dados insuficientes";
		$codigo = 400;
	}

	$conec->desconectar();
	echo json_encode(Array("message" => $mensagem, "code" => $codigo, "data" => $data), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
