<?php
	include("../conector.php");
?>

<?php
	$conec = new Conector();
	$mensagem = "";
	$codigo = 0;
	if (!(empty($_REQUEST["nome"]) || empty($_REQUEST["doc"]) || empty($_REQUEST["email"]))) {
		$usuarios = $conec->loginParticipante($_REQUEST["doc"]);

		if ($usuarios == false) {
			$conec->cadastrarParticipante($_REQUEST["nome"], $_REQUEST["doc"], $_REQUEST["email"]);
			$mensagem = "Usuário cadastrado com sucesso";
			$codigo = 200;
		}
		else {
			$mensagem = "Usuário já cadastrado";
			$codigo = 400;
		}
	}
	else {
		$mensagem = "Informações insuficientes";
		$codigo = 400;
	}
	$conec->desconectar();
	echo json_encode(Array("message" => $mensagem, "code" => $codigo), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
