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
            if($network_name){
                $network_name = $network_name." - IMPORT: ".date('m-d-Y_His');
                $bol = true;
                !$sheet->getCell('A11')->getValue() ? $bol = false : null;
                !$sheet->getCell('B11')->getValue() ? $bol = false : null;
                !$sheet->getCell('C11')->getValue() ? $bol = false : null;
                !$sheet->getCell('D11')->getValue() ? $bol = false : null;
                !$sheet->getCell('E11')->getValue() ? $bol = false : null;
                !$sheet->getCell('F11')->getValue() ? $bol = false : null;
                !$sheet->getCell('G11')->getValue() ? $bol = false : null;
                !$sheet->getCell('H11')->getValue() ? $bol = false : null;
                !$sheet->getCell('I11')->getValue() ? $bol = false : null;
                !$sheet->getCell('J11')->getValue() ? $bol = false : null;
                !$sheet->getCell('K11')->getValue() ? $bol = false : null;
                
                if($bol){
                    $count = 12;
                    $ip_count = 0;
                    $ip_from = null;
                    $ip_to = null;
                    $ip_subnet = null;
                    while ($sheet->getCell('A'.$count)->getValue()) {
                        $count == 12 ? $ip_from = $sheet->getCell('A'.$count)->getValue() : null;
                        $ip_to = $sheet->getCell('A'.$count)->getValue();
                        $ip_subnet = $sheet->getCell('B'.$count)->getValue();
                        $count++;
                        $ip_count++;
                    }

                    if($ip_count <= 1000){
                        $network = new IP_Network;
                        $network->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                        $network->uid = $_SESSION["userid"];
                        $network->rid = "-";
                        $network->name = $network_name;
                        $network->from = $ip_from;
                        $network->to = $ip_to;
                        $network->subnet = $ip_subnet;
                        DB::save($network);

                        $nid = DB::where($network,"name","=",$network_name)[0]["id"];
                        $count = 12;
                        $ip = new IP_Address;
                        while ($sheet->getCell('A'.$count)->getValue()) {
                            $ip->nid = $nid;
                            $ip->ip = $sheet->getCell('A'.$count)->getValue();
                            $ip->subnet = $sheet->getCell('B'.$count)->getValue();
                            $ip->hostname = $sheet->getCell('C'.$count)->getValue();
                            $ip->site = $sheet->getCell('D'.$count)->getValue();
                            $ip->server = $sheet->getCell('E'.$count)->getValue();
                            $ip->state = $sheet->getCell('F'.$count)->getValue();
                            $ip->status = $sheet->getCell('G'.$count)->getValue();
                            $ip->webmgmtpt = $sheet->getCell('H'.$count)->getValue();
                            $ip->username = $sheet->getCell('I'.$count)->getValue();
                            $ip->password = $sheet->getCell('J'.$count)->getValue();
                            $ip->remarks = $sheet->getCell('K'.$count)->getValue();
                            DB::save($ip);
                            $count++;
                        }
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
                            "message" => "The IP Address exceeds the limits of 1000 IP per network only. You can use network name like \"Network [1] 1-1000\", \"Network [2] 1001-2000\" and so on."

                        ];  
                    }
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
            "size" => null,
            "message" => "Import error."
        ];     
    }
    

    
