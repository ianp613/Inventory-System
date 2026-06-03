<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $terminal = [];
    
    $response = [
        "status" => true,
        "type" => "success",
        "size" => null,
        "message" => "Terminal has been updated."
    ];
    if($_SESSION["g_member"]){
        if($data) {
            $t = new Terminals;
            $terminal1 = DB::where($t,"terminal_no","=",$data["terminal_no"]);
            if(count($terminal1)){
                if($terminal1[0]["id"] != $data["id"]){
                    $response = [
                        "status" => false,
                        "type" => "warning",
                        "size" => null,
                        "message" => "Terminal no. already exist."
                    ];
                    echo json_encode([null,$response]);
                    exit;
                }    
            }
            
            $ts                              = DB::prepare($t,$data["id"]);
            $ts->terminal_no                 = $data["terminal_no"];
            $ts->cabinet_no                  = $data["cabinet_no"]           ? $data["cabinet_no"] : "-";
            $ts->ip_address                  = $data["ip_address"]           ? $data["ip_address"] : "-";
            $ts->building                    = $data["building"]             ? $data["building"] : "-";
            $ts->room                        = $data["room"]                 ? $data["room"] : "-";
            $ts->project                     = $data["project"]              ? $data["project"] : "-";
            $ts->remarks                     = $data["remarks"]              ? $data["remarks"] : "-";
            $ts->tech_recommendation         = $data["tech_recommendation"]  ? $data["tech_recommendation"] : "-";
            $ts->unit_type                   = $data["unit_type"]            ? $data["unit_type"] : "-";
            $ts->casing                      = $data["casing"]               ? $data["casing"] : "-";
            $ts->motherboard_model           = $data["motherboard_model"]    ? $data["motherboard_model"] : "-";
            $ts->motherboard_barcode         = $data["motherboard_barcode"]  ? $data["motherboard_barcode"] : "-";
            $ts->cpu                         = $data["cpu"]                  ? $data["cpu"] : "-";
            $ts->ram                         = $data["ram"]                  ? $data["ram"] : "-";
            $ts->storage                     = $data["storage"]              ? $data["storage"] : "-";
            $ts->psu                         = $data["psu"]                  ? $data["psu"] : "-";
            $ts->gpu                         = $data["gpu"]                  ? $data["gpu"] : "-";
            $ts->cs                          = $data["cs"]                   ? $data["cs"] : "-";
            $ts->ec                          = $data["ec"]                   ? $data["ec"] : "-";
            $ts->id_                         = $data["id_"]                  ? $data["id_"] : "-";
            $ts->od                          = $data["od"]                   ? $data["od"] : "-";
            $ts->sp                          = $data["sp"]                   ? $data["sp"] : "-";
            $ts->ups_battery                 = $data["ups_battery"]          ? $data["ups_battery"] : "-";
            $ts->ups_brand                   = $data["ups_brand"]            ? $data["ups_brand"] : "-";
            $ts->ups_casing_model            = $data["ups_casing_model"]     ? $data["ups_casing_model"] : "-";
            $ts->ups_casing_barcode          = $data["ups_casing_barcode"]   ? $data["ups_casing_barcode"] : "-";
            $ts->ups_status                  = $data["ups_status"]           ? $data["ups_status"] : "-";
            $ts->kaspersky                   = $data["kaspersky"]            ? $data["kaspersky"] : "-";
            $ts->bitdefender                 = $data["bitdefender"]          ? $data["bitdefender"] : "-";
            $ts->windows_update              = $data["windows_update"]       ? $data["windows_update"] : "-";
            $ts->operating_system            = $data["operating_system"]     ? $data["operating_system"] : "-";
            $ts->windows_license             = $data["windows_license"]      ? $data["windows_license"] : "-";
            DB::update($ts);
            $terminal = DB::find($t,$data["id"])[0];
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
    echo json_encode([$terminal,$response]);
    