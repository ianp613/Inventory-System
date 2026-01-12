<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        $consumables = new Consumables;
        $consumables = DB::prepare($consumables,$data["sid"]);

        $consumables->stock += $data["quantity"];
        DB::update($consumables);

        $response = [
            "status" => true,
            "type" => "success",
            "size" => null,
            "message" => "Stock has been added.",
        ];
    }else{
        $response = [
            "status" => false,
            "type" => "info",
            "size" => null,
            "message" => "Please operate as group member."
        ];
    }
    echo json_encode($response);

?>