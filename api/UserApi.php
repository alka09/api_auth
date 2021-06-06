<?php

require_once 'users/authentication.php';
require_once 'Db.php';
require_once 'Users.php';


class UserApi
{

    public $apiName = 'users';

    public function indexAction()
    {
        $db = (new Db())->getConnection();
        $users = \http\Client\Curl\User::getAll($db);
        if ($users) {
            return $this->response($users, 200);
        }
        return $this->response('Data not found', 404);
    }

    public function viewAction()
    {
        $db = (new Db())->getConnection();
        //id должен быть первым параметром после /users/x
        $id = array_shift($this->requestUrl);

        if($id){
            $user = Users::getById($db, $id);
            if($user){
                return $this->response($user, 200);
            }
        }
        return $this->response('Data not found', 404);
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

    public function updateAction()
    {
        $parse_url = parse_url($this->requestUrl[0]);
        $userId = $parse_url['path'] ?? null;

        $db = (new Db())->getConnect();

        if(!$userId || !Users::getById($db, $userId)){
            return $this->response("User with id=$userId not found", 404);
        }

        $name = $this->requestParams['username'] ?? '';
        $password = $this->requestParams['password'] ?? '';

        if($name && $password){
            if($user = Users::update($db, $userId, $name, $password)){
                return $this->response('Data updated.', 200);
            }
        }
        return $this->response("Update error", 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/users/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $userId = $parse_url['path'] ?? null;

        $db = (new Db())->getConnection();

        if(!$userId || !Users::getById($db, $userId)){
            return $this->response("User with id=$userId not found", 404);
        }
        if(Users::deleteById($db, $userId)){
            return $this->response('Data deleted.', 200);
        }
        return $this->response("Delete error", 500);
    }

}

