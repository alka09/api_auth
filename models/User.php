<?php

/**
 * This class contains User specific functionalities
 *
 * Contains all the properties for the users and provides
 * functions for api CRUD operations to interact with the users'
 * table
 *
 * @package    Authentication_API
 * @subpackage Authentication_API/models
 */

class User {

    private $_conn;
    private $_table = 'api-users';

    public $id;
    public $username;
    public $password;

    public function __construct($db) {
        $this->_conn = $db;
    }


    /**
     * @return   PDOStatement object
     */
    public function get_all(): PDOStatement
    {

        $query = 'SELECT
                    id,
                    username,
                    password
                FROM ' . $this->_table . '
                    ORDER BY id ASC';

        $stmt = $this->_conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }


    /**
     * @return   boolean true if user found, else false
     */
    public function get_single(): bool
    {

        $query = 'SELECT
                    id,
                    username
                FROM ' . $this->_table . ' 
                WHERE 
                    id = :id
                LIMIT 0,1';

        $stmt = $this->_conn->prepare($query);

        $stmt->bindParam(':id', $this->id);

        $stmt->execute();

        $row_count = $stmt->rowCount();

        if ($row_count <= 0) {
            return false;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->username = $row[ 'username' ];

        return true;
    }

    /**
     * @return   Mixed true if successfully registered, else false/403
     */
    public function register() {

        // check if user already exists
        $query = 'SELECT id from ' .
            $this->_table . '
                WHERE
                    username = :username';

        $stmt = $this->_conn->prepare($query);

        $this->username = htmlSpecialChars(strip_tags($this->username));

        // bind data
        $stmt->bindParam(':username', $this->username);

        if ($stmt->execute()) {
            $rows = $stmt->rowCount();

            if ($rows > 0) {
                // user already is registered
                return '403';
            }
        }

        $query = 'INSERT INTO ' .
            $this->_table . ' 
            SET
                username = :username,
                password  = :password';

        //prepare statement
        $stmt = $this->_conn->prepare($query);

        // sanitize data
        $this->username = htmlSpecialChars(strip_tags($this->username));
        $this->password = htmlSpecialChars(strip_tags($this->password));

        // bind data
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);

        // execute query
        if ($stmt->execute()) {
            return true;
        } else {
            //print error if something goes wrong
            print_f('Error: %s.\n', $stmt->error);

            return false;
        }

    }

    /**
     * @return   boolean true if successfully updated, else false
     */
    public function update(): bool
    {
        $query = 'UPDATE ' .
            $this->_table . ' 
            SET
                id = :id,
                password = :password,
                username = :username
            WHERE
                username = :username AND id = :id';

        //prepare statement
        $stmt = $this->_conn->prepare($query);

        // sanitize data
        $this->username = htmlSpecialChars(strip_tags($this->username));
        $this->password = htmlSpecialChars(strip_tags($this->password));
        $this->id = htmlSpecialChars(strip_tags($this->id));

        // bind data
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);

        // execute query
        if ($stmt->execute()) {
            return true;
        } else {
            //print error if something goes wrong
            print_f('Error: %s.\n', $stmt->error);

            return false;
        }
    }

    /**
     * @return   boolean true if successfully deleted, else false
     */
    public function delete(): bool
    {

        $query = 'DELETE FROM ' . $this->_table . ' 
                WHERE
                 id = :id AND username = :username';

        // prepare statement
        $stmt = $this->_conn->prepare($query);

        // sanitize id
        $this->id = htmlSpecialChars(strip_tags($this->id));
        $this->username = htmlSpecialChars(strip_tags($this->username));

        // bind id
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':username', $this->username);

        // execute query
        if ($stmt->execute()) {
            return true;
        } else {

            //print error if something goes wrong
            print_f('Error: %s.\n', $stmt->error);

            return false;
        }

    }

    /**
     * @return   Mixed true if successfully deleted, else false/401
     */
    public function authenticate() {

        // create query
        $query = 'SELECT
                    id, username 
                FROM ' .
            $this->_table . ' 
                WHERE
                    username = :username 
                AND 
                    password = :password';

        //prepare statement
        $stmt = $this->_conn->prepare($query);

        // sanitize data
        $this->username = htmlSpecialChars(strip_tags($this->username));
        $this->password = htmlSpecialChars(strip_tags($this->password));

        // bind data
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':password', $this->password);

        // execute query
        if ($stmt->execute()) {

            $rows = $stmt->rowCount();

            if ($rows === 1) {
                // set id from the fetched data
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->id = $row['id'];
                return true;
            } else {
                // invalid username of password
                return '404';
            }

        } else {
            //print error if something goes wrong
            print_f('Error: %s.\n', $stmt->error);

            return false;
        }

    }

}