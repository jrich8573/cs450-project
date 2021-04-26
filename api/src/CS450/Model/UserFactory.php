<?php 

namespace CS450\Model;

use CS450\Model\User;
use CS450\Lib\EmailAddress;

final class UserFactory {
    /**
     * @Inject
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @Inject
     * @var CS450\Service\DbService
     */
    private $db;

    public function findByEmail(EmailAddress $email): ?User {
        $selectEmailQ = <<<EOD
            SELECT id, name, email, password, user_role, department
            FROM tbl_fact_users
            WHERE email=?
        EOD;

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($selectEmailQ);

        if (!$stmt) {
            $errMsg = sprintf("An error occurred preparing your query: %s, %s", $selectEmailQ, $conn->error);
            throw new \Exception($errMsg);
        }

        $executed = $stmt->bind_param(
            "s",
            $email,
        ) && $stmt->execute();

        if (!$executed) {
            throw new \Exception($conn->error);
        }

        $this->logger->info("Running sql " . $selectEmailQ . "(=" . $email .")");

        $result = $stmt->get_result();
        $userRow = $result->fetch_assoc();

        return $userRow
            ? (new User($this->db))
                ->setId($userRow["id"])
                ->setName($userRow["name"])
                ->setEmail($userRow["email"])
                ->setRole($userRow["user_role"])
                ->setPasswordHash($userRow["password"])
                ->setDepartment($userRow["department"])
            : null;
    }

    public function getFacultyInDepartmentForAdminId($id) {
        $selectFacultyQ = <<<EOD
            SELECT * FROM tbl_fact_users
            WHERE department = (
                SELECT department
                FROM tbl_fact_users
                WHERE id=$id
            )
            AND id NOT IN ($id)
            AND deleted=FALSE
        EOD;

        $result = $this->db->getConnection()->query($selectFacultyQ);

        if (!$result) {
            $errMsg = sprintf("An error occurred executing your query: %s, %s", $selectFacultyQ, $conn->error);
            throw new \Exception($errMsg);
        }
        
        $users = [];
        while($user = $result->fetch_object("CS450\Model\User", [$this->db])) {
            $this->logger->info(print_r($user, true));
            $users[$user->getId()] = $user;
        }

        return $users;
    }
}
