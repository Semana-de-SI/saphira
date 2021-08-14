<?php
  include("../conector.php");
?>

<?php

  $conec = new Conector();
  $mensagem = "";
  $codigo = 0;

  if (!(empty($_REQUEST['email'])) && !(empty($_REQUEST['token'])) && !(empty($_REQUEST['idEvento']))){
    $token = preg_replace('/[^[:alnum:]_]/', '', $_REQUEST['token']);
    $email = $_REQUEST['email'];
    $resultParticipante = $conec->loginParticipante($email);

    if($resultParticipante != false){
      $resultPalestra = $conec->getPalestraByToken($token);

      if($resultPalestra != false){
        if($resultPalestra['dataExpiraToken'] > date('Y-m-d H:i:s')){
          $resultPresenca = $conec->checkPresenca($resultParticipante['ID_pessoa'], $resultPalestra['ID_subdivisoes']);

          if($resultPresenca == false){ // Ainda nao tem presenca
            $conec->insertPresenca($resultParticipante['ID_pessoa'], $resultPalestra['ID_subdivisoes']);

            $resultQuantidadePresenca = $conec->getQuantidadePresenca($resultPalestra['ID_evento'], $resultParticipante['ID_pessoa']);

            if($resultQuantidadePresenca != false){
              $newQuantidade = $resultQuantidadePresenca['Quantidade_presenca'] + 1;
              $conec->updateQuantidadePresenca($newQuantidade, $resultParticipante['ID_pessoa'], $resultPalestra['ID_evento']);

            } else { // Primeira palestra da pessoa no evento
              $conec->setNewUserQuantidadePresenca($resultParticipante['ID_pessoa'], $resultPalestra['ID_evento']);
            }

            $conec->updateParticipantesInPalestra($resultPalestra['ID_subdivisoes']);
            $mensagem = "Presença do usuário ".$resultParticipante['Nome']." confirmada com sucesso!";
            $codigo = 200;

          } else { // Ja possui presenca na palestra
            $mensagem = "Usuário já possui presença nesta palestra";
            $codigo = 405;
          }
        } else { // Token expirado

          if($token != ""){
            $mensagem = "Token com data expirada!";
            $codigo = 405;
          } else {
            $mensagem = "Nenhum token enviado!";
            $codigo = 400;
          }
        }
      } else { //Token nao encontrado
          $mensagem = "Token inválido!";
          $codigo = 404;
        }
    } else { //Este numero nao esta cadastrado
        $mensagem = "Usuário não cadastrado!";
        $codigo = 404;
    }
  } else { // Faltam credenciais
    $mensagem = "Alguma das credenciais está faltando (Nusp, Token, Id do evento)";
    $codigo = 400;
  }

  $conec->desconectar();
  echo json_encode(Array("message" => $mensagem, "code" => $codigo), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
