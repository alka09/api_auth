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

$result = $user->get_all();

$count = $result->rowCount();

if ($count > 0) {

    $users_arr = array();
    $users_arr['response code'] = '';
    $users_arr['data'] = array();

    while ($row = $result -> fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $user_item = array(
            'id'       => $user->id,
            'password'    => $user->password
        );

        array_push($users_arr['data'], $user_item);
    }
    $users_arr['response code'] = '200';

    echo json_encode($users_arr);

} else {

    echo json_encode(
        array(
            'response code' => '404',
            'message'       => 'No users Found'
        )
    );
}
