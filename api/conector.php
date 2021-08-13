<?php

use GrahamCampbell\ResultType\Result;

include_once __DIR__ . '/vendor/autoload.php';

class Conector
{
    private $link;
    private $getAllParticipants = "SELECT Nome, Email FROM saphira_pessoa";
    private $getParticipant = "SELECT * FROM saphira_pessoa WHERE Email=?";
    private $getParticipantInfo = "SELECT * FROM saphira_pessoa as p LEFT JOIN saphira_cad_complementar as cc ON p.ID_pessoa=cc.ID_pessoa WHERE p.Email=?";
    private $getEventoQuery = "SELECT * FROM saphira_evento WHERE ID_evento=?";
    private $loginQuery = "SELECT Senha FROM saphira_usuario WHERE Login=? AND ID_evento=?";
    private $loginParticipanteQuery = "SELECT * FROM saphira_pessoa WHERE Email=?";
    private $getPresencaPessoa = "SELECT s.Nome FROM saphira_presenca as pr INNER JOIN saphira_pessoa as p ON pr.ID_pessoa=p.ID_pessoa INNER JOIN saphira_subdivisoes as s ON pr.ID_subdivisoes=s.ID_subdivisoes WHERE p.Email=(?);";
    private $getPalestraAtualQuery = "SELECT * FROM saphira_subdivisoes WHERE ID_evento=? AND NOW() < dataExpiraToken";
    private $registerParticipant = "INSERT INTO `saphira_pessoa`(`Nome`, `Documento`,`Email`) VALUES (?,?,?)";
    private $registerParticipantExtra = "INSERT INTO `saphira_cad_complementar`(`ID_pessoa`, `Idade`, `Genero`, `Redes`, `Cursando`, `Curso`, `Ano`, `Periodo`, `Estagio`, `Condicoes`) VALUES (?,?,?,?,?,?,?,?,?,?)";
    private $registerParticipantExtraWoutGrad = "INSERT INTO `saphira_cad_complementar`(`ID_pessoa`, `Idade`, `Genero`, `Redes`, `Cursando`, `Condicoes`) VALUES (?,?,?,?,?,?)";
    private $updateParticipant = "UPDATE `saphira_pessoa` SET `Nome` = (?), `Documento` = (?) WHERE `Email` = (?)";
    private $updateParticipantExtra = "UPDATE `saphira_cad_complementar` SET `Idade` = (?), `Genero` = (?), `Redes` = (?), `Cursando` = (?), `Curso` = (?), `Ano` = (?), `Periodo` = (?), `Estagio` = (?), `Condicoes` = (?) WHERE `ID_pessoa` = (?)";
    private $updateParticipantExtraWoutGrad = "UPDATE `saphira_cad_complementar` SET `Idade` = (?), `Genero` = (?), `Redes` = (?), `Cursando` = (?), `Condicoes` = (?) WHERE `ID_pessoa` = (?)";
    private $getPalestraByTokenQuery = "SELECT * FROM saphira_subdivisoes WHERE token=(?)";
    private $checkPresencaQuery = "SELECT * FROM saphira_presenca WHERE ID_pessoa=(?) AND ID_subdivisoes=(?)";
    private $insertPresencaQuery = "INSERT INTO saphira_presenca (`ID_pessoa`, `ID_subdivisoes`) VALUES (?,?)";
    private $getQuantidadePresencaQuery = "SELECT * FROM saphira_quantidade_presenca WHERE ID_evento=(?) and ID_pessoa =(?)";
    private $updateQuantidadePresencaQuery = "UPDATE `saphira_quantidade_presenca` SET `Quantidade_presenca`= (?) WHERE `ID_pessoa` = (?) and `ID_evento` = (?)";
    private $setNewUserQuantidadePresencaQuery = "INSERT INTO `saphira_quantidade_presenca`(`ID_pessoa`, `ID_evento`, `Quantidade_presenca`) VALUES (?,?,'1')";
    private $updateParticipantesInPalestraQuery = "UPDATE `saphira_subdivisoes` SET `Quantidade_presentes`= Quantidade_presentes+1 WHERE `ID_subdivisoes` = (?)";

    function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
        $dotenv->load();
        $this->conectar();
    }

    public function conectar()
    {
        $this->link = new mysqli($_ENV['HOST'], $_ENV['USER'], $_ENV['PASS'], $_ENV['DATABASE']);
        if ($this->link->connect_errno) {
            throw new RuntimeException("Não foi possível conectar ao banco de dados");
        }

        return true;
    }

    public function getEvento($evento)
    {
        $prepara = $this->link->prepare($this->getEventoQuery);
        $prepara->bind_param('i', $evento);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        return $resultado;
    }

    public function login($login, $IdEvento, $senha)
    {
        $prepara = $this->link->prepare($this->loginQuery);
        $prepara->bind_param('si', $login, $IdEvento);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        return password_verify(password_hash($senha, PASSWORD_BCRYPT, ["salt" => "uma palavra uma frase qualquer coisa"]), $resultado['Senha']);
    }

    public function loginParticipante($email)
    {
        $prepara = $this->link->prepare($this->loginParticipanteQuery);
        $prepara->bind_param('s', $email);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        if ($resultado != null) {
            return $resultado;
        } else {
            return false;
        }
    }

    public function getParticipant($email) {
        $prepara = $this->link->prepare($this->getParticipant);
        $prepara->bind_param('s', $email);
        $prepara->execute();
        return $prepara->get_result()->fetch_assoc();
    }

    public function cadastrarParticipante($nome, $documento, $email, $idade, $genero, $redes, $cursando, $curso, $ano, $periodo, $estagio, $condicoes) {
        $prepara = $this->link->prepare($this->registerParticipant);
        $prepara->bind_param('sss', $nome, $documento, $email);
        $prepara->execute();

        $userInfo = $this->getParticipant($email);

        // ID_pessoa, Idade, Genero, Redes, Cursando, Curso, Ano, Periodo, Estagio, Condicoes
        $prepara = $this->link->prepare($this->registerParticipantExtra);
        $prepara->bind_param('iissssissi', $userInfo["ID_pessoa"], $idade, $genero, $redes, $cursando, $curso, $ano, $periodo, $estagio, $condicoes);
        $prepara->execute();

        return true;
    }

    public function cadastrarParticipanteSemCurso($nome, $documento, $email, $idade, $genero, $redes, $cursando, $condicoes) {
        $prepara = $this->link->prepare($this->registerParticipant);
        $prepara->bind_param('sss', $nome, $documento, $email);
        $prepara->execute();

        $userInfo = $this->getParticipant($email);

        // ID_pessoa, Idade, Genero, Redes, Cursando, Curso, Ano, Periodo, Estagio, Condicoes
        $prepara = $this->link->prepare($this->registerParticipantExtraWoutGrad);
        $prepara->bind_param('iisssi', $userInfo["ID_pessoa"], $idade, $genero, $redes, $cursando, $condicoes);
        $prepara->execute();

        return true;
    }

    public function getParticipantInfo($email) {
        $prepara = $this->link->prepare($this->getParticipantInfo);
        $prepara->bind_param('s', $email);
        $prepara->execute();
        return $prepara->get_result()->fetch_assoc();
    }

    public function updateParticipantInfo($nome, $documento, $email, $idade, $genero, $redes, $cursando, $curso, $ano, $periodo, $estagio, $condicoes) {
        $prepara = $this->link->prepare($this->updateParticipant);
        $prepara->bind_param('sss', $nome, $documento, $email);
        $prepara->execute();

        $userInfo = $this->getParticipant($email);

        $prepara = $this->link->prepare($this->updateParticipantExtra);
        $prepara->bind_param('issssissii', $idade, $genero, $redes, $cursando, $curso, $ano, $periodo, $estagio, $condicoes, $userInfo["ID_pessoa"]);
        $prepara->execute();
        return true;
    }

    public function updateParticipantInfoWoutGrad($nome, $documento, $email, $idade, $genero, $redes, $cursando, $condicoes) {
        $prepara = $this->link->prepare($this->updateParticipant);
        $prepara->bind_param('sss', $nome, $documento, $email);
        $prepara->execute();

        $userInfo = $this->getParticipant($email);

        $prepara = $this->link->prepare($this->updateParticipantExtraWoutGrad);
        $prepara->bind_param('issssii', $idade, $genero, $redes, $cursando, $condicoes, $userInfo["ID_pessoa"]);
        $prepara->execute();
        return true;
    }

    public function getPresenca($email)
    {
        $prepara = $this->link->prepare($this->getPresencaPessoa);
        $prepara->bind_param('s', $email);
        $prepara->execute();
        return $prepara->get_result()->fetch_all();
    }

    public function getPresencaPessoa($document)
    {
        $prepara = $this->link->prepare($this->getPresencaPessoaQuery);
        $prepara->bind_param('i', $document);
        $prepara->execute();
        $resultado = $prepara->get_result();
        $fetched = null;

        $i = 0;
        while ($row = $resultado->fetch_assoc()) {
            $fetched[$i] = $row;
            $i++;
        }

        if($fetched == null){
            return false;
        }else{
            return $fetched;
        }
    }

    public function getPalestraAtual($IdEvento){
        $prepara = $this->link->prepare($this->getPalestraAtualQuery);
        $prepara->bind_param('i', $IdEvento);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        if($resultado != null){
            return $resultado;
        }else{
            return false;
        }
    }

    public function getPalestraByToken($token){
        $prepara = $this->link->prepare($this->getPalestraByTokenQuery);
        $prepara->bind_param('s', $token);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        if($resultado != null){
            return $resultado;
        }else{
            return false;
        }
    }

    public function checkPresenca($ID_pessoa, $ID_subdivisoes){
        $prepara = $this->link->prepare($this->checkPresencaQuery);
        $prepara->bind_param('ii', $ID_pessoa, $ID_subdivisoes);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        if($resultado != null){
            return $resultado;
        }else{
            return false;
        }
    }

    public function insertPresenca($ID_pessoa, $ID_subdivisoes){
        $prepara = $this->link->prepare($this->insertPresencaQuery);
        $prepara->bind_param('ii', $ID_pessoa, $ID_subdivisoes);
        $prepara->execute();

        return;
    }

    public function getQuantidadePresenca($ID_evento, $ID_pessoa){
        $prepara = $this->link->prepare($this->getQuantidadePresencaQuery);
        $prepara->bind_param('ii', $ID_evento, $ID_pessoa);
        $prepara->execute();
        $resultado = $prepara->get_result()->fetch_assoc();

        if($resultado != null){
            return $resultado;
        }else{
            return false;
        }
    }

    public function updateQuantidadePresenca($quantidade, $ID_pessoa, $ID_evento){
        $prepara = $this->link->prepare($this->updateQuantidadePresencaQuery);
        $prepara->bind_param('iii', $quantidade, $ID_pessoa, $ID_evento);
        $prepara->execute();

        return;
    }

    public function setNewUserQuantidadePresenca($ID_pessoa, $ID_evento){
        $prepara = $this->link->prepare($this->setNewUserQuantidadePresencaQuery);
        $prepara->bind_param('ii', $ID_pessoa, $ID_evento);
        $prepara->execute();

        return;
    }

    public function updateParticipantesInPalestra($ID_subdivisoes){
        $prepara = $this->link->prepare($this->updateParticipantesInPalestraQuery);
        $prepara->bind_param('i', $ID_subdivisoes);
        $prepara->execute();

        return;
    }

    public function desconectar()
    {
        $this->link->close();
    }
}

header('Access-Control-Allow-Origin: *');
