<?php
    include 'Conector.php'
?>

<?php    

        $conec;
        try{
            $conec = new Conector();
            $resp = $conec->getPalestraAtual($_GET['q']);

            if($resp == false){
                http_response_code(404);
            }else{
                http_response_code(200);
                echo json_encode($resp, JSON_PRETTY_PRINT);
            }
            
        }catch(Exception $e){
            http_response_code(500);
        }

    $conec->desconectar();
?>