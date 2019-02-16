<?php
const settingsFile = "../files/settings.json";
const databaseFile = "../../main/settings.json";
const audioDirectory = "../../rings/";
$settings = json_decode(file_get_contents(settingsFile));
$database = json_decode(file_get_contents(databaseFile));
if (isset($_POST["key"])) {
    $result = new stdClass();
    $result->auth = false;
    if (md5($_POST["key"]) === $settings->password) {
        $result->auth = true;
        if (isset($_POST["name"]) && isset($_POST["second"]) && isset($_POST["index"])) {
            $result->success = false;
            $name = $_POST["name"];
            $second = $_POST["second"];
            $index = intval($_POST["index"], 10);
            move_uploaded_file($_FILES["audio"]["tmp_name"], audioDirectory . $name . ".mp3");
            for ($i = 0; $i < sizeof($database->queue); $i++) {
                if ($i === $index) {
                    $database->queue[$i]->link = $name . ".mp3";
                    $database->queue[$i]->time = floatval($second);
                    $database->queue[$i]->md5 = md5_file(audioDirectory . $name . ".mp3");
                    $result->success = true;
                }
            }
            file_put_contents(databaseFile, json_encode($database));
        }
    }
    echo json_encode($result);
}