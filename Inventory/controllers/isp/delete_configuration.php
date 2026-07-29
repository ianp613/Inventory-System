<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");
    $data = json_decode(file_get_contents('php://input'), true);

    if($_SESSION["g_member"]){
        if($data) {
            $config = new ISP_Configuration;
            $conf = DB::find($config,$data["id"]);


            $isp = new ISP;
            $isp_temp = DB::where($isp,"configuration","=",$data["id"]);
        
            if(count($isp_temp)){
                $ids = "";
                $count_ = 1;
                foreach ($isp_temp as $i) {
                    $ids .= $i["id"];
                    if($count_ != count($isp_temp)) $ids .= ",";
                    $count_++;
                }
                $sql = "UPDATE `sql_table` SET `configuration` = '-' WHERE `id` IN (".$ids.")";
                DB::sql($isp,$sql);
            }

            DB::delete($config,$data["id"]);

            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has deleted ISP configuration \"".$conf[0]["name"]."\".";
            if($_SESSION["log"] != $log->log){
                $_SESSION["log"] = $log->log;
                DB::save($log);
            }

            $g_id = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $redis->del("icore_isp_configuration:all" . $g_id);
            $redis->del("icore_current_configuration:all");
            $redis->del("icore_isp_router:all" . $g_id);

            $response = [
                "status" => true,
                "type" => "success",
                "size" => null,
                "message" => "ISP configuration has been deleted."
            ];
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
    echo json_encode($response);
?>