<?php

use GrahamCampbell\ResultType\Result;

include_once __DIR__ . '/vendor/autoload.php';

class Conector
{
    private $link;
    private $getAllParticipants = "SELECT Nome, Num_usp FROM saphira_pessoa";
    private $getParticipantInfo = "SELECT * FROM saphira_pessoa as p LEFT JOIN saphira_cad_complementar as cc ON p.ID_pessoa=cc.ID_pessoa WHERE p.email=?";
    private $getEventoQuery = "SELECT * FROM saphira_evento WHERE ID_evento=?";
    private $loginQuery = "SELECT Senha FROM saphira_usuario WHERE Login=? AND ID_evento=?";
    private $loginParticipanteQuery = "SELECT * FROM saphira_pessoa WHERE email=?";
    private $getPresencaPessoaQuery = "CALL get_presenca_pessoa(?)";
    private $getPalestraAtualQuery = "SELECT * FROM saphira_subdivisoes WHERE ID_evento=? AND NOW() < dataExpiraToken";
    private $registerParticipant = "INSERT INTO `saphira_pessoa`(`Nome`, `Num_usp`,`email`) VALUES (?,?,?)";
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
        $this->link = new mysqli($_ENV['local'], $_ENV['nome'], $_ENV['senha'], $_ENV['db']);
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

        return password_verify($senha, $resultado['Senha']);
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

    public function cadastrarParticipante($nome, $documento, $email) {
        $prepara = $this->link->prepare($this->registerParticipant);
        $prepara->bind_param('sis', $nome, $documento, $email);
        $prepara->execute();
        return true;
    }

    public function getParticipantInfo($email) {
        if (empty($email)) {
            $prepara = $this->link->prepare($this->getAllParticipants);
        }
        else {
            $prepara = $this->link->prepare($this->getParticipantInfo);
            $prepara->bind_param('s', $email);
        }
        $prepara->execute();
        return $prepara->get_result()->fetch_assoc();
    }

    public function updateParticipantInfo($documento, $dados) {
        if (empty($documento)) {
            $prepara = $this->link->prepare($this->getAllParticipants);
        }
        else {
            $prepara = $this->link->prepare($this->getParticipantInfo);
            $prepara->bind_param('i', $documento);
        }
        $prepara->execute();
        return $prepara->get_result()->fetch_assoc();
    }

    public function getPresencaPessoa($numUsp)
    {
        $prepara = $this->link->prepare($this->getPresencaPessoaQuery);
        $prepara->bind_param('i', $numUsp);
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
