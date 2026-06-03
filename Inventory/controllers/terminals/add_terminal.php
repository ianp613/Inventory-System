<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $modal = file_get_contents("../../views/modals/add_terminal.html");
    $terminal = [];
    

    $response = [
        "status" => true,
        "type" => "success",
        "size" => null,
        "message" => "Terminal has been saved."
    ];
    if($_SESSION["g_member"]){
        if($data) {
            $t = new Terminals;
            if(!DB::validate($t,"terminal_no",$data["terminal_no"])){
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "Terminal no. already exist."
                ];
                echo json_encode([$modal,$response]);
                exit;
            }
            $t->gid                         = $_SESSION["g_id"]             ? $_SESSION["g_id"] : "_*";
            $t->uid                         = $data["uid"];
            $t->terminal_no                 = $data["terminal_no"];
            $t->cabinet_no                  = $data["cabinet_no"]           ? $data["cabinet_no"] : "-";
            $t->ip_address                  = $data["ip_address"]           ? $data["ip_address"] : "-";
            $t->building                    = $data["building"]             ? $data["building"] : "-";
            $t->room                        = $data["room"]                 ? $data["room"] : "-";
            $t->project                     = $data["project"]              ? $data["project"] : "-";
            $t->remarks                     = $data["remarks"]              ? $data["remarks"] : "-";
            $t->tech_recommendation         = $data["tech_recommendation"]  ? $data["tech_recommendation"] : "-";
            $t->unit_type                   = $data["unit_type"]            ? $data["unit_type"] : "-";
            $t->casing                      = $data["casing"]               ? $data["casing"] : "-";
            $t->motherboard_model           = $data["motherboard_model"]    ? $data["motherboard_model"] : "-";
            $t->motherboard_barcode         = $data["motherboard_barcode"]  ? $data["motherboard_barcode"] : "-";
            $t->cpu                         = $data["cpu"]                  ? $data["cpu"] : "-";
            $t->ram                         = $data["ram"]                  ? $data["ram"] : "-";
            $t->storage                     = $data["storage"]              ? $data["storage"] : "-";
            $t->psu                         = $data["psu"]                  ? $data["psu"] : "-";
            $t->gpu                         = $data["gpu"]                  ? $data["gpu"] : "-";
            $t->cs                          = $data["cs"]                   ? $data["cs"] : "-";
            $t->ec                          = $data["ec"]                   ? $data["ec"] : "-";
            $t->id_                         = $data["id"]                   ? $data["id"] : "-";
            $t->od                          = $data["od"]                   ? $data["od"] : "-";
            $t->sp                          = $data["sp"]                   ? $data["sp"] : "-";
            $t->ups_battery                 = $data["ups_battery"]          ? $data["ups_battery"] : "-";
            $t->ups_brand                   = $data["ups_brand"]            ? $data["ups_brand"] : "-";
            $t->ups_casing_model            = $data["ups_casing_model"]     ? $data["ups_casing_model"] : "-";
            $t->ups_casing_barcode          = $data["ups_casing_barcode"]   ? $data["ups_casing_barcode"] : "-";
            $t->ups_status                  = $data["ups_status"]           ? $data["ups_status"] : "-";
            $t->kaspersky                   = $data["kaspersky"]            ? $data["kaspersky"] : "-";
            $t->bitdefender                 = $data["bitdefender"]          ? $data["bitdefender"] : "-";
            $t->windows_update              = $data["windows_update"]       ? $data["windows_update"] : "-";
            $t->operating_system            = $data["operating_system"]     ? $data["operating_system"] : "-";
            $t->windows_license             = $data["windows_license"]      ? $data["windows_license"] : "-";
            DB::save($t);
            $terminal = DB::where2($t,"terminal_no","=",$data["terminal_no"],"uid","=",$data["uid"])[0];
        }else{
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Something went wrong."
            ];
        }    
    }else{
        $response = [
            "status" => false,
            "type" => "info",
            "size" => null,
            "message" => "Please operate as group member."
        ];
    }
    echo json_encode([$modal,$response,$terminal]);
?>