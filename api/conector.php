<?php
    include_once __DIR__ . '/vendor/autoload.php';

    class Conector{
        protected $link;
        private $getEvento = "SELECT * FROM saphira_evento WHERE ID_evento=?";

        function __construct()
        {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load(); 
        }
            //TODO: separar o metodo conectar em: conectar, getEvento
        public function conectar($evento){
            $this->link = mysqli_connect($_ENV['local'], $_ENV['nome'], $_ENV['senha'], $_ENV['db']);
            if(!$this->link){
                throw new Exception("Não foi possível conectar ao banco de dados");
            }
            
            $prepara = $this->link->prepare($this->getEvento);
            $prepara->bind_param('i', $evento);
            $prepara->execute();
            $resultado = $prepara->get_result()->fetch_assoc();
            
            return $resultado;
        }

        public function login($id, $hash){

        }

    }
?>