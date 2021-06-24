<?php
    include_once __DIR__ . '/vendor/autoload.php';

    class conector{
        protected $link;

        function __construct()
        {
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
            $dotenv->load(); 
            $this->conectar();
        }

        private function conectar(){
            $this->link = mysqli_connect($_ENV['local'], $_ENV['nome'], $_ENV['senha'], $_ENV['db']);
        }
    }
?>