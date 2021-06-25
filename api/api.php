<?php
    include 'Conector.php'
?>

<?php    

    if(isset($_GET['value'])){
        $conec = new Conector();

        try{
            $evento = $conec->conectar($_GET['value']);

            http_response_code(200);
            echo json_encode($evento, JSON_PRETTY_PRINT);
        }catch(Exception $e){
            http_response_code(500);
        }
    }else{
        http_response_code(404);
    }
?>