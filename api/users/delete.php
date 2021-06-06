<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");

require_once '../../config/database.php';
require_once '../../models/user.php';

$database = new Database();

$db = $database -> connect();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if ($data->id == '' || $data->username == '') {

    echo json_encode(
        array(
            'response code' => '400',
            'message'       => 'User could not be removed, `id` or username field can\'t be empty.'
        )
    );

    die();
}

$user->id = $data->id;
$user->username = $data->username;

// Delete post
if ($user->delete()) {

    echo json_encode(
        array(
            'response code' => '200',
            'message'       => 'User removed!'
        )
    );

} else {

    echo json_encode(
        array(
            'response code' => '304',
            'message' => 'User could not be removed'
        )
    );

}
