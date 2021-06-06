<?php

require_once 'users/get_single_user.php';
require_once 'users/get_users.php';

class Users
{
    private $db;
    private $table_name = "users";

    public $id;
    public $username;
    public $password;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function createAction()
    {
        $username = $this->requestParams['username'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if($username){
            $db = (new Db())->getConnection();
            $user = new Users($db, [
                'username' => $username,
                'password' => $password
            ]);
            if($user = $user->saveNew()){
                return $this->response('Data saved.', 200);
            }
        }
        return $this->response("Saving error", 500);
    }

}