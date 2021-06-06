<?php

class Database {

    private $_host = 'localhost';
    private $_db_name = 'testdb';
    private $_username = 'root';
    private $_password = 'root';

    private $_conn;

    public function connect(): ?PDO
    {

        $this->_conn = null;

        try {
            $this->_conn = new PDO(
                'mysql:host=' . $this->_host . ';dbname=' . $this->_db_name,
                $this->_username,
                $this->_password
            );

            $this->_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $e) {
            echo 'Connection Error' . $e -> getMessage();
        }

        return $this->_conn;
    }

}