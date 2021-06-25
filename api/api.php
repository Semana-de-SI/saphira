<?php
    include 'Conector.php'
?>

<?php    

    if(isset($_GET['value'])){
        $conec;

        try{
            $conec = new Conector();
            $resp = $conec->login("SSIOnline", 6, $_GET['value']);

            http_response_code(200);
            echo json_encode($resp, JSON_PRETTY_PRINT);
        }catch(Exception $e){
            http_response_code(500);
        }


    }else{
        http_response_code(404);
    }
?>