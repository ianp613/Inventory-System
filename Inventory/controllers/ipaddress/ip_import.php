<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    /**------------------------ */

    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Style\Color;

    /**------------------------ */

    try {
        date_default_timezone_set('Asia/Manila');

        $file = $_FILES["file"];
        $fileName = basename($file["name"]);
        $filePath = "../../assets/temp/".uniqid().".xlsx";

        
        if (move_uploaded_file($file["tmp_name"], $filePath)) {

            // Load an existing Excel file
            $inputFile = $filePath; 
            $spreadsheet = IOFactory::load($inputFile);

            // Get the active sheet
            $sheet = $spreadsheet->getActiveSheet();

            // Check Network
            $network_name = $sheet->getCell('B3')->getValue();

            $bol_router = true;
            !$sheet->getCell('B5')->getValue() ? $bol_router = false : null;
            !$sheet->getCell('B6')->getValue() ? $bol_router = false : null;
            !$sheet->getCell('B7')->getValue() ? $bol_router = false : null;

            if(!$bol_router){
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "Router details is incomplete."
                ];  
                echo json_encode($response);
                exit;
            }

            if($network_name){
                $date = date('mdy-His');
                $network_name = "IMPORT: ".$date." | ".$network_name;
                $router_name = "IMPORT: ".$date." | ".$sheet->getCell('B5')->getValue();

                $router = new Routers;
                $router->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $router->uid = $_SESSION["userid"];
                $router->name = $router_name;
                $router->ip = $sheet->getCell('B6')->getValue();
                $router->subnet = $sheet->getCell('B7')->getValue();
                $router->webmgmtpt = '-';
                $router->wan1 = '-';
                $router->wan2 = '-';
                $router->active = '-';
                DB::save($router);

                $router_temp = DB::where($router,"name","=",$router_name)[0];

                $bol = true;
                $title_row = 15;
                !$sheet->getCell('A'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('B'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('C'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('D'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('E'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('F'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('G'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('H'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('I'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('J'.$title_row)->getValue() ? $bol = false : null;
                !$sheet->getCell('K'.$title_row)->getValue() ? $bol = false : null;
                
                if($bol){
                    $count = 16;
                    $ip_count = 0;
                    $ip_from = null;
                    $ip_to = null;
                    $ip_subnet = null;
                    while ($sheet->getCell('A'.$count)->getValue()) {
                        $count == 16 ? $ip_from = $sheet->getCell('A'.$count)->getValue() : null;
                        $ip_to = $sheet->getCell('A'.$count)->getValue();
                        $ip_subnet = $sheet->getCell('B'.$count)->getValue();
                        $count++;
                        $ip_count++;
                    }

                    $network = new IP_Network;
                    $network->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $network->uid = $_SESSION["userid"];
                    $network->rid = $router_temp["id"];
                    $network->name = $network_name;
                    $network->from = $ip_from;
                    $network->to = $ip_to;
                    $network->subnet = $ip_subnet;
                    DB::save($network);

                    $nid = DB::where($network,"name","=",$network_name)[0]["id"];
                    $sql = "INSERT INTO `sql_table` (`nid`, `ip`, `subnet`, `hostname`, `site`, `server`, `state`, `status`, `webmgmtpt`, `username`, `password`, `remarks`) VALUES ";
                    $count = 16;
                    $count_ = 0;
                    while ($sheet->getCell('A'.$count)->getValue()) {
                        $sql .= "('".$nid;
                        $sql .= "', '".$sheet->getCell('A'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('B'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('C'.$count)->getValue();
                        $sql .="', '".$sheet->getCell('D'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('E'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('F'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('G'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('H'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('I'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('J'.$count)->getValue();
                        $sql .= "', '".$sheet->getCell('K'.$count)->getValue()."')";
                        $count++;
                        $count_++;
                        if($count_ < $ip_count){
                            $sql .= ",";
                        }
                    }

                    $ip_address = new IP_Address;
                    DB::sql($ip_address,$sql);

                    $log = new Logs;
                    $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                    $log->uid = $_SESSION["userid"];
                    $log->log = $_SESSION["name"]." has imported data of network \"".$network_name."\".";
                    if($_SESSION["log"] != $log->log){
                        $_SESSION["log"] = $log->log;
                        DB::save($log);
                    }  
                    $response = [
                        "status" => true,
                        "type" => "success",
                        "size" => null,
                        "message" => "Import completed."
                    ]; 
                }else{
                    $response = [
                        "status" => false,
                        "type" => "warning",
                        "size" => null,
                        "message" => "Column is incomplete."
                    ];          
                }
            }else{
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "Please provide network name."
                ];      
            }
    
        } else {
            $response = [
                "status" => false,
                "type" => "error",
                "size" => null,
                "message" => "Something went wrong, please try again."
            ];
        }

        echo json_encode($response);
    } catch (\Throwable $th) {
        $response = [
            "status" => false,
            "type" => "error",
            "size" => 'lg',
            "message" => "Import error.".$th
        ];
        echo json_encode($response);     
    }
    

    
