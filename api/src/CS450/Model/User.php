<?php

namespace CS450\Model;

use CS450\Lib\Password;
use CS450\Lib\EmailAddress;

use CS450\Service\DbService;

final class User {
    private $db;

    private $id;
    private $name;
    private $email;
    private $passwordHash;
    private $role;
    private $department;

    public function __construct(DbService $db) {
        $this->db = $db;
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): EmailAddress {
        return $this->email;
    }

    public function getPasswordHash(): string {
        return $this->passwordHash;
    }

    public function getRole() {
        return $this->role;
    }

    public function getDepartment() {
        return $this->department;
    }

    function setId($id) {
        $this->id = $id;
        return $this;
    }

    function setName($name) {
        $this->name = $name;
        return $this;
    }

    function setEmail($email) {
        $this->email = EmailAddress::fromString($email);
        return $this;
    }

    function setRole($role) {
        $this->role = $role;
        return $this;
    }

    function setPasswordHash($passwordHash) {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    function setDepartment($department) {
        $this->department = $department;
        return $this;
    }

    public function save(): Self {
        $insertUserSql = <<<EOD
            INSERT INTO tbl_fact_users (name, email, password, department, user_role)
            VALUES (?, ?, ?, ?, '$this->role')
        EOD;

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($insertUserSql);

        if (!$stmt) {
            $errMsg = sprintf("An error occurred preparing your query: %s - %s", $insertUserSql, $conn->error);
            throw new \Exception($errMsg);
        }

        $executed = $stmt->bind_param(
            "sssi",
            $this->name,
            $this->email,
            $this->passwordHash,
            $this->department,
        ) && $stmt->execute() && $stmt->close();

        if (!$executed) {
            $errNo = $conn->error_list[0]["errno"];
            if (Self::errorIsEmailExists($errNo)) {
                throw new \Exception(
                    "A user with that email is already registered",
                    $errNo,
                );
            }
            throw new \Exception($conn->error);
        }

        $this->id = $conn->insert_id;
        return $this;
    }

    private static function errorIsEmailExists(int $errorcode): bool {
        return $errorcode == 1062;
    }
}
