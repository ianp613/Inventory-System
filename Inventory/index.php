<?php
    include("includes.php");
    session_start();
    !array_key_exists("userid",$_SESSION) ?  $_SESSION["userid"] = null : null; 
    if($_SESSION["userid"]){
        if(array_key_exists("inactivity",$_GET)){
            if($_GET["inactivity"] == "true"){
                $log = new Logs;
                $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
                $log->uid = $_SESSION["userid"];
                $log->log = $_SESSION["name"]." has been logged out from the system due to inactivity.";
                DB::save($log);
            }
        }else{
            $log = new Logs;
            $log->gid = $_SESSION["g_id"] ? $_SESSION["g_id"] : "_*";
            $log->uid = $_SESSION["userid"];
            $log->log = $_SESSION["name"]." has logged out from the system.";
            DB::save($log);
        }    
    }
        
    $_SESSION["auth"] = false;
    $_SESSION["name"] = null;
    $_SESSION["privileges"] = null;
    $_SESSION["code"] = null;
    $_SESSION["assign_camera_id"] = null;
    $_SESSION["assign_type"] = null;
    $_SESSION["log"] = null;
    $_SESSION["log1"] = null;
    $_SESSION["log2"] = null;
    $_SESSION["log3"] = null;
    $_SESSION["log4"] = null;
    $_SESSION["mail_username"] = 'paulian.dumdum@gmail.com';
    $_SESSION["mail_password"] = 'ytrr qwdo kqox vdre';
    $_SESSION["g_member"] = false;
    $_SESSION["g_name"] = null;
    $_SESSION["g_id"] = null;
    $_SESSION["g_type"] = null;
    $_SESSION["operate_as_group"] = null;

    if(isset($_GET["reset-user"])){
        $user = new User;
        if($_GET["reset-user"]){
            $users = DB::where($user,"username","=",$_GET["reset-user"]);
            if(count($users)){
                $temp = DB::prepare($user,$users[0]["id"]);
                $temp->password = Data::encrypt("12345");
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : DB::update($temp);
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? Data::pp("Warning: You don't have enough privilege to reset user account <b>".$temp->username."</b>.") : Data::pp("User account of <b>".$temp->name."</b> has been reset.");
            }else{
                Data::pp("User not found.");
            }
        }else{
            $users = DB::all($user);
            foreach ($users as $use) {
                $temp = DB::prepare($user,$use["id"]);
                $temp->password = Data::encrypt("12345");
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : DB::update($temp);
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : Data::pp("User account of <b>".$temp->username."</b> has been reset.");
            }
        }
    }elseif(isset($_GET["renew-passkey"])){
        $user = new User;
        $passkey = Data::generate(4,"numeric");
        while(!DB::validate($user,"passkey",$passkey)){
            $passkey = Data::generate(4,"numeric");
        }
        if($_GET["renew-passkey"]){
            $users = DB::where($user,"username","=",$_GET["renew-passkey"]);
            if(count($users)){
                $temp = DB::prepare($user,$users[0]["id"]);
                $temp->passey = $passkey;
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : DB::update($temp);
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? Data::pp("Warning: You don't have enough privilege to alter user account <b>".$temp->username."</b>.") : Data::pp("Passkey for account <b>".$temp->username."</b> has been set to <b>".$passkey."</b>.");
            }else{
                Data::pp("User not found.");
            }
        }else{
            $users = DB::all($user);
            foreach ($users as $use) {
                while(!DB::validate($user,"passkey",$passkey)){
                    $passkey = Data::generate(4,"numeric");
                }
                $temp = DB::prepare($user,$use["id"]);
                $temp->passkey = $passkey;
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : DB::update($temp);
                $temp->username == "administrator" || $temp->username == "703F_administrator" ? null : Data::pp("Passkey for account <b>".$temp->username."</b> has been set to <b>".$passkey."</b>.");
            }
        }
    }elseif(isset($_GET["test"])){
        $user = new User;
        $sample = DB::where($user,"username","=","sdfkjsd");
        echo count($sample);
    }else{
        header("location: views/login.php");
    }

    
?>