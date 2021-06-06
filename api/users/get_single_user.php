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

if (isset($_GET[ 'id' ])) {
    $user->id = $_GET[ 'id' ];
} else {

    echo json_encode(
        array(
            'response code' => '400',
            'message'       => 'Could not find user, `id` field can\'t be empty.'
        )
    );

    die();
}

if ($user->get_single()) {

    // create array
    $user_arr = array(
        'response code' => '200',
        'id'            => $user->id,
        'username'         => $user->username
    );

    echo json_encode($user_arr);

} else {

    echo json_encode(
        array(
            'response code' => '404',
            'message'       => 'No user for given `id` and `username` found'
        )
    );

}
