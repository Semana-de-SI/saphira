<?php
    include './conector.php'
?>

<?php

        $conec;
        try{
            $conec = new Conector();
            $resp = $conec->getPalestraAtual($_GET['q']);

            if($resp == false){
                echo json_encode(Array("message" => "Palestra não encontrada!"), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }else{
                echo json_encode($resp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            $conec->desconectar();
        } catch(Exception $e){
          echo $e;
        }

?>
