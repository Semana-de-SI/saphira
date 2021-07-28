<?php
    require(__DIR__."/../vendor/autoload.php");

class Conector
{
    private $link;
    private $getEventoQuery = "SELECT * FROM saphira_evento WHERE ID_evento=?";
    private $loginQuery = "SELECT Senha FROM saphira_usuario WHERE Login=? AND ID_evento=?";
    private $loginParticipanteQuery = "SELECT * FROM saphira_pessoa WHERE Num_usp=?";
    private $getPresencaPessoaQuery = "CALL get_presenca_pessoa(?)";
    private $getPalestraAtualQuery = "SELECT * FROM saphira_subdivisoes WHERE ID_evento=? AND NOW() < dataExpiraToken";
    private $registerParticipant = "INSERT INTO `saphira_pessoa`(`Nome`, `Num_usp`,`email`) VALUES (?,?,?)";

    function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__."/..");
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

    public function loginParticipante($login)
    {
        $prepara = $this->link->prepare($this->loginParticipanteQuery);
        $prepara->bind_param('i', $login);
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

    public function desconectar()
    {
        $this->link->close();
    }
}
?>