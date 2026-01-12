<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    /**------------------------ */

    require '../../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\IOFactory;
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Style\Color;

    // Load an existing Excel file
    $inputFile = '../../assets/files/ip_export.xlsx'; // Change this to your file path
    $spreadsheet = IOFactory::load($inputFile);

    // Get the active sheet
    $sheet = $spreadsheet->getActiveSheet();

    /**------------------------ */

    $response = [
        "status" => false,
        "type" => "error",
        "size" => null,
        "message" => "Something went wrong, please try again."
    ];

    if($data["id"]) {
        // Set timezone (optional, change as needed)
        date_default_timezone_set('Asia/Manila'); // Example: Philippine Time

        // Get current date and time
        $currentDate = date('F j, Y'); // Format: May 12, 2025
        $currentTime = date('h:i:s A'); // Format: 01:35:42 AM or 01:35:42 PM


        $network = new IP_Network;
        $network = DB::find($network,$data["id"])[0];

        // Edit a cell (Example: Change A1 value)
        $sheet->setCellValue('B1', $currentDate);
        $sheet->setCellValue('B2', $currentTime);
        $sheet->setCellValue('B3', $network['name']);

        $ip = new IP_Address;
        $ip = DB::where($ip,"nid","=",$data["id"]);

        $row = 12;
        $used = 0;
        $available = 0;

        $up = 0;
        $down = 0;
        foreach ($ip as $i) {
            $sheet->setCellValue('A'.$row,$i["ip"]);
            $sheet->setCellValue('B'.$row,$i["subnet"]);
            $sheet->setCellValue('C'.$row,$i["hostname"]);
            $sheet->setCellValue('D'.$row,$i["site"]);
            $sheet->setCellValue('E'.$row,$i["server"]);
            $sheet->setCellValue('F'.$row,$i["state"]);
            $sheet->setCellValue('G'.$row,$i["status"]);
            $sheet->getStyle('F'.$row)->getFont()->getColor()->setARGB($i["state"] == "DOWN" ? 'FF0000' : '224814');
            $sheet->getStyle('G'.$row)->getFont()->getColor()->setARGB($i["status"] == "UNASSIGNED" ? 'FF0000' : '224814');
            $sheet->setCellValue('H'.$row,$i["webmgmtpt"]);
            $sheet->setCellValue('I'.$row,$i["username"]);
            $sheet->setCellValue('J'.$row,$i["password"]);
            $sheet->setCellValue('K'.$row,$i["remarks"]);
            $i["state"] == "DOWN" ? $down++ : $up++;
            $i["status"] == "UNASSIGNED" ? $available++ : $used++;
            $row++;
        }

        $sheet->setCellValue('B5',$used);
        $sheet->setCellValue('B6',$available);

        $sheet->setCellValue('B8',$up);
        $sheet->setCellValue('B9',$down);

        $uid = uniqid();

        if(!is_dir("../../assets/temp/")){
            mkdir("../../assets/temp/");
        }

        // Save as another file
        $outputFile = '../../assets/temp/'.$uid.'.xlsx'; // Change this to your desired output file name
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save($outputFile);

        $log = new Logs;
        $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
        $log->uid = $_SESSION["userid"];
        $log->log = $_SESSION["name"]." has exported data of network \"".$network['name']."\".";
        if($_SESSION["log"] != $log->log){
            $_SESSION["log"] = $log->log;
            DB::save($log);
        }
    }
    echo json_encode([$outputFile, $network['name']."_".date('m-d-Y_Hi').'.xlsx']);
?>