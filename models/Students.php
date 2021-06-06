<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: *");

class Students
{
    private $conn;
    private $table_name = "students";

    public $id;
    public $firstname;
    public $surname;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    function getStudents($connect)
    {
        $query = "SELECT * FROM " . $this->table_name;

        $sth = $connect->prepare($query);
        $sth->execute();
        $studentsList = [];

        while ($student = $sth->fetch(PDO::FETCH_ASSOC)) {
            $studentsList[] = $student;
        }

        echo json_encode($studentsList);
    }

    function getStudent($connect, $id)
    {
        $query = $connect->prepare("SELECT * FROM `students` WHERE `id` = :id");

        $query->execute(array('id' => $id));

        $student = $query->fetch(PDO::FETCH_ASSOC);

        if (!isset($student)) {
            http_response_code(400);
            echo json_encode(array("message" => "Student not found"));
        } else {

            echo json_encode($student);
        }
    }

    function addStudent($connect, $data): bool
    {

        $query = "INSERT INTO " . $this->table_name . "
            SET                
                firstname = :firstname,
                surname = :surname";

        $prop = $this->conn->prepare($query);

        $this->firstname = htmlspecialchars(strip_tags($this->firstname));
        $this->surname = htmlspecialchars(strip_tags($this->surname));

        $prop->bindParam(':firstname', $this->firstname);
        $prop->bindParam(':surname', $this->surname);

        if ($prop->execute()) {
            return true;
        }

        http_response_code(201);

        $res = [
            "status" => true,
            "student_id" => mysqli_insert_id($connect)
        ];

        echo json_encode($res);

        return false;
    }

}