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
	    || empty($_REQUEST["estagio"])
	    || empty($_REQUEST["condicoes"]))) {
		$usuarios = $conec->getParticipant($_REQUEST["email"]);
		if ($usuario != false) {
			if ($_REQUEST["cursando"] == "Sim" && !(empty($_REQUEST["curso"])
			    || empty($_REQUEST["ano"])     || empty($_REQUEST["periodo"]))) {
				$conec->updateParticipantInfo(empty($_REQUEST["nome"])
				                              , empty($_REQUEST["doc"])
				                              , empty($_REQUEST["email"])
				                              , empty($_REQUEST["idade"])
				                              , empty($_REQUEST["genero"])
				                              , empty($_REQUEST["redes"])
				                              , empty($_REQUEST["cursando"])
				                              , empty($_REQUEST["curso"])
				                              , empty($_REQUEST["ano"])
				                              , empty($_REQUEST["periodo"])
				                              , empty($_REQUEST["estagio"])
				                              , empty($_REQUEST["condicoes"]));
			}
			else {
				$conec->updateParticipantInfoWoutGrad(empty($_REQUEST["nome"])
				                              , empty($_REQUEST["doc"])
				                              , empty($_REQUEST["email"])
				                              , empty($_REQUEST["idade"])
				                              , empty($_REQUEST["genero"])
				                              , empty($_REQUEST["redes"])
				                              , empty($_REQUEST["cursando"])
				                              , empty($_REQUEST["estagio"])
				                              , empty($_REQUEST["condicoes"]));
			}
			$mensagem = "Usuário atualizado com sucesso";
			$codigo = 200;
		}
		else {
			$mensagem = "Usuário não encontrado";
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
