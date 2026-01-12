<?php
    session_start();
    header('Content-Type: application/json');
    include("../../includes.php");

    $ipInfo = file_get_contents("https://ipinfo.io/json");
    $data = json_decode($ipInfo, true);

    $orgParts = explode(" ", $data['org'], 2); 
    $asn = $orgParts[0];
    $isp = $orgParts[1];

    $entry = new Equipment_Entry;
    $entry = $_SESSION["g_id"] ? DB::where($entry,"gid","=",$_SESSION["g_id"]) : DB::all($entry);
    $response = [
        "status" => true,
        "entry" => $entry,
        "ip" => $data["ip"],
        "isp" => $isp,
        "asn" => $asn,
        "country" => $data["country"],
        "region" => $data["region"],
        "city" => $data["city"]
    ];

    echo json_encode($response);
?>