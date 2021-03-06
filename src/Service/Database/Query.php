<?php
namespace TallTree\Roots\Service\Database;

use PDO;

class Query
{

    /**
     * @var PDO $connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection->getConnection();
    }

    public function execute($statement, array $input = [])
    {
        $statement = $this->connection->prepare($statement);
        foreach ($input as $field => $value) {
            $statement->bindValue(":$field", $value, $this->determineType($value));
        }
        $statement->execute();

        return $statement;
    }

    public function patch($statement)
    {
        $statement = $this->execute($statement);
        return $statement->errorInfo();
    }

    public function write($statement, array $input)
    {
        $statement = $this->execute($statement, $input);
        return $statement->rowCount();
    }

    public function read($statement, array $input)
    {
        $statement = $this->execute($statement, $input);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function determineType($value)
    {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        }

        if (is_bool($value)) {
            return PDO::PARAM_BOOL;
        }

        return PDO::PARAM_STR;
    }
}
