<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");

require_once '../../config/database.php';
require_once '../../models/user.php';

// instantiate database & connect
$database = new Database();

// connect is user defined function
$db = $database->connect();

// instantiate user object
$user = new User($db);

// get raw posted data
$data = json_decode(file_get_contents("php://input"));

// check if vars are empty
if ($data->username == '' || $data->password == '') {

    // prepare and echo json output
    echo json_encode(
        array(
            'response code' => '400',
            'message'       => 'Bad request, Username or Password field can\'t be empty.'
        )
    );

    die();
}

$user->username = $data->username;
$user->password = $data->password;

// Register user
$response = $user->authenticate();

if ($response === true) {
    echo json_encode(
        array(
            'id'            => $user->id,
            'response code' => '200',
            'message'       => 'Login successful'
        )
    );

} elseif ($response === '404') {
    echo json_encode(
        array(
            'response code' => '404',
            'message'       => 'Incorrect username or password'
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