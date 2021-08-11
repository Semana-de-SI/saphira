<?php
	include("../conector.php");
?>

<?php
	$conec = new Conector();
	$mensagem = "";
	$codigo = 0;
	if (!(empty($_REQUEST["nome"])
	    || empty($_REQUEST["doc"])
	    || empty($_REQUEST["email"])
	    || empty($_REQUEST["idade"])
	    || empty($_REQUEST["genero"])
	    || empty($_REQUEST["redes"])
	    || empty($_REQUEST["cursando"])
	    || empty($_REQUEST["curso"])
	    || empty($_REQUEST["ano"])
	    || empty($_REQUEST["periodo"])
	    || empty($_REQUEST["estagio"])
	    || empty($_REQUEST["condicoes"]))) {

		$usuarios = $conec->getParticipantInfo($_REQUEST["email"]);

		if ($usuarios == false) {
			$conec->cadastrarParticipante($_REQUEST["nome"], $_REQUEST["doc"], $_REQUEST["email"], $_REQUEST["idade"], $_REQUEST["genero"], $_REQUEST["redes"], $_REQUEST["cursando"], $_REQUEST["curso"], $_REQUEST["ano"], $_REQUEST["periodo"], $_REQUEST["estagio"], $_REQUEST["condicoes"]);
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
