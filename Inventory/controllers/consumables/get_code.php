<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");


    $consumables = new Consumables;
    $proceed = false;
    while(!$proceed){
        $code = Data::generate(6,"numeric");
        if(DB::validate($consumables,"code",$code)){
            $_SESSION["consumables_code"] = $code;
            $proceed = true;
        }
    }

    echo json_encode($code);
?>