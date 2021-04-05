<?php 

namespace CS450\Model;

use CS450\Lib\Password;
use CS450\Lib\EmailAddress;
use CS450\Model\User\RegisterUserInfo;

final class User {
    /**
     * 
     * @Inject("jwt.key")
     */
    private $jwtKey;

    /**
     * 
     * @Inject
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * 
     * @Inject
     * @var CS450\Service\JwtService
     */
    private $jwt;

    /**
     * 
     * @Inject
     * @var CS450\Service\DbService
     */
    private $db;

    private function makeJwt($uid): string {
        $payload = array(
            'uid' => $uid,
        );

        return $this->jwt->encode($payload, $this->jwtKey);
    }

    public function register(RegisterUserInfo $userInfo): string {
        $sql = "INSERT INTO tbl_fact_users (name, email, password) VALUES (?, ?, ?)";

        $conn = $this->db->getConnection();
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            $errMsg = sprintf("An error occurred preparing your query: %s - %s", $sql, $conn->error);
            throw new \Exception($errMsg);
        }

        $executed = $stmt->bind_param(
            "sss", 
            $userInfo->name,
            $userInfo->email,
            $userInfo->password,
        ) && $stmt->execute() && $stmt->close();

        if (!$executed) {
            if (Self::errorIsEmailExists($conn->error_list[0]["errno"])) {
                $this->logger->info(sprintf(
                    "Existing user found checking password %s %s",
                    $userInfo->email,
                    $userInfo->password,
                ));

                // check if passwords match.
                // -> if so log the user in
                // else -> redirect to login with error email exists

                $selectEmailQ = "SELECT id, password FROM tbl_fact_users WHERE email=?";
                $stmt = $conn->prepare($selectEmailQ);

                if (!$stmt) {
                    $errMsg = sprintf("An error occurred preparing your query: %s, %s", $selectEmailQ, $conn->error);
                    throw new \Exception($errMsg);
                }

                $executed = $stmt->bind_param(
                    "s",
                    $userInfo->email,
                ) && $stmt->execute();

                if (!$executed) {
                    throw new \Exception($conn->error);
                }

                $stmt->bind_result($uid, $storedPassword);
                $stmt->fetch();

                $logger->info("GOT ID " . $uid);

                if ($userInfo->password->verifyhash($storedPassword)) {
                    $this->logger->info(sprintf(
                        "Found existing user (%s) with same password found. Logging in",
                        $userInfo->email
                    ));
                    return $this->makeJwt($uid);
                }

                throw new \Exception("email exists");

                // User exists but passwords dont match
            } else {
                throw new \Exception($conn->error);
            }
        }

        $uid = $conn->insert_id;
        $this->logger->info(sprintf("Make new user id: %d", $uid));

        return $this->makeJwt(0);
    }

    private static function errorIsEmailExists(int $errorcode): bool {
        return $errorcode == 1062;
    }
}