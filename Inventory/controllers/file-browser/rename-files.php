<?php
$data = json_decode(file_get_contents('php://input'), true);
$baseDir = realpath("E:" . $data["folder"]);
$response = [
    "status" => true,
    "type" => "success",
    "message" => "File renamed successfully!",
];

$old = $baseDir."\\".$data["old"];
$new = $baseDir."\\".$data["new"];

if (!file_exists($old)) {
    $response["status"] = false;
    $response["type"] = "warning";
    $response["message"] = "File does not exist.";
} elseif (!is_readable($old)) {
    $response["status"] = false;
    $response["type"] = "warning";
    $response["message"] = "File is not readable.";
} elseif (!is_writable(dirname($old))) {
    $response["status"] = false;
    $response["type"] = "warning";
    $response["message"] = "Directory is not writable.";
} else {
    if (rename($old, $new)) {
        $response["message"] = "File renamed successfully!";
    } else {
        $response["status"] = false;
        $response["type"] = "warning";
        $response["message"] = "Rename failed.";
    }
}

echo json_encode($response);
?>