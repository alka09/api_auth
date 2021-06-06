<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");

require_once '../../config/database.php';
require_once '../../models/user.php';

$database = new Database();

$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents("php://input"));

if ($data->username == '' || $data->password == '') {

    echo json_encode(
        array(
            'response code' => '400',
            'message'       => 'Could not add user, username or Password field can\'t be empty.'
        )
    );

    die();
}

$user->username = $data->username;
$user->password = $data->password;

$response = $user->register();

if ($response === '403') {

    // user is already registered
    echo json_encode(
        array(
            'response code' => '403',
            'message'       => 'A user with given username is already registered.'
        )
    );

} elseif ($response === true) {

    echo json_encode(
        array(
            'response code' => '201',
            'message'       => 'User Added!'
        )
    );

} else {

    echo json_encode(
        array(
            'response code' => '500',
            'message'       => 'Something went wrong.'
        )
    );

}
