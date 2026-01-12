<?php
include("../../includes.php");
include("Identifier.php");
session_start();
if (isset($_POST['message'])) {
    $message = $_POST['message'];
    $file = "../../assets/files/telebot_notes.txt";
    $prev_file = "../../assets/files/telebot_notes_prev.txt";
    $telebot = "../../assets/files/telebot";
    $message_temp = explode("$", $message);
    if (!file_exists($file) || filesize($file) === 0) {
        file_put_contents($file, "");
    }
    if (!file_exists($prev_file) || filesize($prev_file) === 0) {
        file_put_contents($prev_file, "");
    }
    if (!file_exists($telebot) || filesize($telebot) === 0) {
        file_put_contents($telebot, "false");
    }


    if(strtolower($message_temp[0]) == "notes" || strtolower($message_temp[0]) == "note"){
        echo json_encode(file_get_contents($file));
    }elseif(strtolower($message_temp[0]) == "notes update" || strtolower($message_temp[0]) == "notes edit" || strtolower($message) == "note update" || strtolower($message) == "note edit"){
        file_put_contents($telebot,"true");
        file_put_contents($prev_file,file_get_contents($file));
        echo json_encode("--- This is your current note: br|br|".file_get_contents($file)."br|br|--- Enter your updated note and begin the first line with: <b>note save$</b>.");
    }elseif(strtolower(str_replace(" ","",$message_temp[0])) == "notesave" || strtolower(str_replace(" ","",$message_temp[0])) == "notessave"){
        if(file_get_contents($telebot) == "true"){
            array_shift($message_temp);
            $string = implode("$", $message_temp);
            if($string){
                file_put_contents($file,$string);
                echo json_encode("--- Note has been updated: br|".file_get_contents($file));
                file_put_contents($telebot, "false");    
            }else{
                echo json_encode("--- Note cannot be empty.");
            }
        }else{
            echo json_encode("--- Use command <b>note update or note edit</b> to update note.");
        }
    }elseif(strtolower($message_temp[0]) == "note undo" || strtolower($message_temp[0]) == "notes undo"){
        if(file_get_contents($prev_file) != ""){
            file_put_contents($file,file_get_contents($prev_file));
            file_put_contents($prev_file,"");
            echo json_encode("--- Changes has been undone: br|br|".file_get_contents($file));
            file_put_contents($telebot, "false");
        }else{
            echo json_encode("--- There is nothing to undo.");
        }
    }elseif(strtolower($message_temp[0]) == "ngrok tunnel"){
        $batchPath = realpath(__DIR__ . '/../../_ngrok/get_tunnels.bat');
        shell_exec("start \"\" \"$batchPath\"");
        sleep(1);
        $tunnels = file_get_contents("tunnels.json");
        $data = json_decode($tunnels, true);
        echo json_encode($data['tunnels'][0]['public_url']);
    }
    else{
        echo json_encode(Identifier::main($message), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo "No message received, something is wrong with bot module [python]";
}
?>