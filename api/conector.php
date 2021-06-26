<?php

use GrahamCampbell\ResultType\Result;

include_once __DIR__ . '/vendor/autoload.php';

    class Conector{
        private mysqli $link;
        private $getEventoQuery = "SELECT * FROM saphira_evento WHERE ID_evento=?";
        private $loginQuery = "SELECT Senha FROM saphira_usuario WHERE Login=? AND ID_evento=?";
        private $loginParticipanteQuery = "SELECT * FROM saphira_pessoa WHERE Num_usp=?";

        function __construct()
        {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load();
            $this->conectar();
        }
            
        public function conectar(){
            $this->link = new mysqli($_ENV['local'], $_ENV['nome'], $_ENV['senha'], $_ENV['db']);
            if($this->link->connect_errno){
                throw new RuntimeException("Não foi possível conectar ao banco de dados");
            }
                  
            return true;
        }

        public function getEvento($evento){
            $prepara = $this->link->prepare($this->getEventoQuery);
            $prepara->bind_param('i', $evento);
            $prepara->execute();
            $resultado = $prepara->get_result()->fetch_assoc();

            return $resultado;
        }

        public function login($login, $IdEvento, $senha){
            $prepara = $this->link->prepare($this->loginQuery);
            $prepara->bind_param('si', $login, $IdEvento);
            $prepara->execute();
            $resultado = $prepara->get_result()->fetch_assoc();

            return password_verify($senha, $resultado['Senha']);            
        }

        public function loginParticipante($login){
            $prepara = $this->link->prepare($this->loginParticipanteQuery);
            $prepara->bind_param('i', $login);
            $prepara->execute();
            $resultado = $prepara->get_result()->fetch_assoc();

            if($resultado != null){
                return $resultado;
            }else{
                return false;
            }
            
        }

        public function desconectar(){
            $this->link->close();
        }

    }
?>