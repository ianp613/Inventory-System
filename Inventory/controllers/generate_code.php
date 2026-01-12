<?php
    header('Content-Type: application/json');
    session_start();
    include("../includes.php");
    
    // Get the POST data
    $data = json_decode(file_get_contents('php://input'), true);


    require '../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    $mail = new PHPMailer(true);


    $response = [
        "status" => false,
        "type" => "warning",
        "size" => null,
        "message" => "User not found."
    ]; 

    if($data) {
        $userid = $data['userid'];
        $user = new User;
        $temp = [];

        if(count(explode("@",$userid)) == 1){
            $temp = DB::where($user,"username","=",$userid);
            if(count($temp)){
                $_SESSION["code"] = Data::generate(6,"numeric");
                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_SESSION["mail_username"]; // Your Gmail address
                    $mail->Password = $_SESSION["mail_password"]; // Use the App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email details
                    $mail->setFrom('wifiteaminventory@gmail.com', 'Wifi Team Inventory');
                    $mail->Subject = 'Account Recovery';
                    $mail->isHTML(true); 
                    $mail->Body = '<div style="width: 100%; color: #332D2D;">'.
                    '<div style="position: absolute; left: 50%; transform: translateX(-50%); width: 500px;">'.
                    '<div style="padding-top: 5px; padding-bottom: 5px; padding-left: 20px; background-color: #dc3545; color: white;">'.
                    '<h2>ACCOUNT RECOVERY CODE</h2>'.
                    '</div>'.
                    '<h3>This code was sent to your email for help getting back into your Account:</h3>'.
                    '<h1 style="font-size: 50px; color: #dc3545;">'.$_SESSION['code'].'</h1>'.
                    '<h4><i>Note: Do not share this code to anyone.</i></h4>'.
                    '</div>'.
                    '</div>';
                    if($temp[0]["email"] != "-"){
                        $mail->addAddress($temp[0]["email"]);
                        $mail->send();

                        $response = [
                            "status" => true,
                            "type" => "info",
                            "size" => null,
                            "message" => "We sent your code to: ". $temp[0]["email"],
                            "user" => $temp,
                        ];    
                    }else{
                        $response = [
                            "status" => false,
                            "type" => "info",
                            "size" => "lg",
                            "message" => "You did not set up an email for account recovery. <br> Kindly ask your supervisor to reset your account credentials.",
                            "user" => $temp,
                        ]; 
                    } 
                } catch (Exception $e) {
                    $response = [
                        "status" => false,
                        "type" => "error",
                        "size" => null,
                        "message" => "Something went wrong. </br> Check internet connection."
                    ]; 
                }
            }
            
        }
        if(count(explode("@",$userid)) == 2){
            $temp = DB::where($user,"email","=",$userid);
            if(count($temp)){
                $_SESSION["code"] = Data::generate(6,"alphanumeric");
                try {
                    // SMTP settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = $_SESSION["mail_username"]; // Your Gmail address
                    $mail->Password = $_SESSION["mail_password"]; // Use the App Password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Email details
                    $mail->setFrom('wifiteaminventory@gmail.com', 'Wifi Team Inventory');
                    $mail->Subject = 'Account Recovery';
                    $mail->isHTML(true); 
                    $mail->Body = '<div style="width: 100%; color: #332D2D;">'.
                    '<div style="position: absolute; left: 50%; transform: translateX(-50%); width: 500px;">'.
                    '<div style="padding-top: 5px; padding-bottom: 5px; padding-left: 20px; background-color: #dc3545; color: white;">'.
                    '<h2>ACCOUNT RECOVERY CODE</h2>'.
                    '</div>'.
                    '<h3>This code was sent to your email for help getting back into your Account:</h3>'.
                    '<h1 style="font-size: 50px; color: #dc3545;">'.$_SESSION['code'].'</h1>'.
                    '<h4><i>Note: Do not share this code to anyone.</i></h4>'.
                    '</div>'.
                    '</div>';
                    if($temp[0]["email"] != "-"){
                        $mail->addAddress($temp[0]["email"]);
                        $mail->send();

                        $response = [
                            "status" => true,
                            "type" => "info",
                            "size" => null,
                            "message" => "We sent your code to: ". $temp[0]["email"],
                            "user" => $temp,
                        ];    
                    }else{
                        $response = [
                            "status" => false,
                            "type" => "info",
                            "size" => "lg",
                            "message" => "You did not set up an email for account recovery. <br> Kindly ask your supervisor to reset your account credentials.",
                            "user" => $temp,
                        ]; 
                    } 
                } catch (Exception $e) {
                    $response = [
                        "status" => false,
                        "type" => "error",
                        "size" => null,
                        "message" => "Something went wrong. </br> Check internet connection."
                    ]; 
                }
            }
        }
    }

    
    

    echo json_encode($response);
?>
