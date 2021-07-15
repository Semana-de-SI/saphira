<?php
    include './conector.php'
?>

<?php

        $conec;
        try{
            $conec = new Conector();
            $resp = $conec->getPalestraAtual($_GET['q']);

            if($resp == false){
                http_response_code(404);
                echo json_encode(Array("message" => "Palestra não encontrada!"));
            }else{
                http_response_code(200);
                echo json_encode($resp, JSON_PRETTY_PRINT);
            }
            
            $conec->desconectar();
        } catch(Exception $e){
          echo $e;
          http_response_code(500);
        }

?>
