<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);
    $sql = "";

    function cidrToRange($ip, $prefix) {
        $ipLong = ip2long($ip);

        $mask = $prefix == 0 ? 0 : (-1 << (32 - $prefix));
        $network = $ipLong & $mask;
        $broadcast = $network | (~$mask & 0xFFFFFFFF);

        return [$network + 1, $broadcast - 1];
    }

    function listUsableIPsFromCIDR($ip, $subnet, $nid) {
        $prefix = explode("/",$subnet)[1];
        [$start, $end] = cidrToRange($ip, $prefix);

        $output = "(";

        for ($i = $start; $i <= $end; $i++) {
            $output .= "'".$nid."', '".long2ip($i)."', '".$subnet."', '-', '-', '-', 'DOWN', 'UNASSIGNED', '-', '-', '-', '-'";

            if ($i < $end) {
                $output .= " ),\n( ";
            }else{
                $output .= ")";
            }
        }

        return $output;
    }

    if($_SESSION["g_member"]){
        if($data["name"]) {
            $ip_network = new IP_Network;
            $bol = DB::validate($ip_network,"name",$data["name"]);
            if($bol){
                $ip_network->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $ip_network->uid = $data["uid"];
                $ip_network->name = $data["name"];
                $ip_network->from = $data["from"];
                $ip_network->to = $data["to"];
                $ip_network->subnet = $data["subnet"];
                $ip_network->rid = $data["gateway"][0];
                DB::save($ip_network);

                $nid = DB::where($ip_network,"name","=",$data["name"])[0]["id"];

                $sql = "INSERT INTO `sql_table` (`nid`, `ip`, `subnet`, `hostname`, `site`, `server`, `state`, `status`, `remarks`, `webmgmtpt`, `username`, `password`) VALUES ";
                $sql .= listUsableIPsFromCIDR($data["gateway"][1],$data["subnet"],$nid);

                $ip_address = new IP_Address;
                DB::sql($ip_address,$sql);
                
                $response = [
                    "status" => true,
                    "type" => "success",
                    "size" => null,
                    "message" => "Network has been saved."
                ]; 
            }else{
                $response = [
                    "status" => false,
                    "type" => "warning",
                    "size" => null,
                    "message" => "Network already exist."
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