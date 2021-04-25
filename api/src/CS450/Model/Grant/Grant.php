<?php

namespace CS450\Model;

use CS450\Model\GrantBuilder;
use CS450\Service\DbService;

final class User {
    private $db;

    private $id;
    private $name;
    private $type;
    
    public function __construct(GrantBuilder $builder, DbService $db) {
        $this->db = $db;

        $this->id = $builder->id;
        $this->name = $builder->name;
        $this->type = $builder->type;
         
    }

    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getType(): string {
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
/*
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
            "sssd",
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
*/
    private static function errorIsEmailExists(int $errorcode): bool {
        return $errorcode == 1062;
    }
}
