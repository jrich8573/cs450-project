<?

require_once 'dbconfig.php';

use Psr\Container\ContainerInterface;
use function DI\factory;

use Monolog\Logger;
use Monolog\ErrorHandler;
use Monolog\Handler\StreamHandler;

use CS450\Service\JwtService;
use CS450\Service\DbService;

$db_conn_params = load_db_config(
    getenv("MYSQL_HOST"),
    getenv("MYSQL_USER"),
    getenv("MYSQL_PASSWORD"),
    getenv("MYSQL_DATABASE"),
);

$db_config = array_merge(
    $db_conn_params,
    array(
        'adapter' => 'mysql',
        'charset' => 'utf8',
        'port' => '3306',
    )
);

$jwt_config = json_decode(base64_decode(getenv("JWT_CONFIG")));

return [
    "env" => "development",
    "db" => $db_config,
    "jwt" => $jwt_config,
    DbService::class => DI\Autowire(CS450\Service\Db\MysqlDb::class),
    JwtService::class => DI\Autowire(CS450\Service\Jwt\FirebaseJwt::class),
    Psr\Log\LoggerInterface::class => DI\factory(function () {
        $logger = new Logger("CS450");

        $fileHandler = new StreamHandler("php://stdout", Logger::DEBUG);
        $logger->pushHandler($fileHandler);

        ErrorHandler::register($logger);

        return $logger;
    }),
];
