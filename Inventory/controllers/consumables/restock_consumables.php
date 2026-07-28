<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    // Set timezone (Philippines)
    date_default_timezone_set("Asia/Manila");
    // Get current date and time
    $currentDateTime = date("Y-m-d H:i:s");

    if($_SESSION["g_member"]){
        $consumables = new Consumables;
        $consumables = DB::prepare($consumables,$data["sid"]);
        $consumables->last_restock = $currentDateTime;

        $consumables->stock += $data["quantity"];
        DB::update($consumables);

        $log = new Logs;
        $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $log->uid = $_SESSION["userid"];
        $log->log = $_SESSION["name"]." has added a stock to consumable \"".$consumables->description."\" with a quantity of \"".$data["quantity"]."\".";
        if($_SESSION["log"] != $log->log){
            $_SESSION["log"] = $log->log;
            DB::save($log);
        }

        $g_id = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $redis->del("icore_consumable:all" . $g_id);
        $redis->del("icore_consumable_log:page" . $g_id);
        $redis->del("icore_consumable_log:dashboard" . $g_id);

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