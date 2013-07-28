<?php

namespace Emmetog\Database;

use Emmetog\Database\Connection;
use Emmetog\Database\ConnectionException;
use Emmetog\Database\ConnectionInvalidQueryException;
use Emmetog\Database\ConnectionInvalidValueTypeException;
use Emmetog\Cache\Registry;

class PdoConnection extends Connection
{

    private $readQueryTypes = array(
        'show tables',
        'show create table',
        'select',
    );
    private $writeQueryTypes = array(
        'create table',
        'create temporary table',
        'update',
        'insert',
        'replace',
        'delete',
        'lock tables',
        'unlock tables',
    );

    /**
     * @var \PDOStatement
     */
    private $statement;

    /**
     * @var array
     */
    private $boundParams = array();
    private $queryType   = null;
    private $query       = null;
    private $description = null;
    private $readConnectionRegistryConfig;
    private $writeConnectionRegistryConfig;

    public function connect($profile)
    {
        $profile_config = $this->config->getDatabaseConfig($profile);

        $write_config = array_shift($profile_config['write']);
        $read_config  = array_shift($profile_config['read']);

        // Set default values
        $config_defaults = array(
            'hostname' => 'default',
            'database' => 'default',
            'username' => 'default',
            'password' => 'default',
            'port'     => 3306,
            'socket'   => '/tmp/mysql.sock',
        );

        $read_config  = array_replace($config_defaults, $read_config);
        $write_config = array_replace($config_defaults, $write_config);

        $key = 'db' . md5(
                        $read_config['hostname'] . $read_config['database']
                        . $read_config['username'] . $read_config['password']
                        . $read_config['port'] . $read_config['socket']
        );
        $this->readConnectionRegistryConfig = array(
            'key'      => $key,
            'hostname' => $read_config['hostname'],
            'database' => $read_config['database'],
            'username' => $read_config['username'],
            'password' => $read_config['password'],
            'port'     => $read_config['port'],
            'socket'   => $read_config['socket'],
        );

        $key = 'db' . md5(
                        $write_config['hostname'] . $write_config['database']
                        . $write_config['username'] . $write_config['password']
                        . $write_config['port'] . $write_config['socket']
        );
        $this->writeConnectionRegistryConfig = array(
            'key'      => $key,
            'hostname' => $write_config['hostname'],
            'database' => $write_config['database'],
            'username' => $write_config['username'],
            'password' => $write_config['password'],
            'port'     => $write_config['port'],
            'socket'   => $write_config['socket'],
        );
    }

    /**
     * Checks if there is an open connection.
     * 
     * @return boolean
     */
    public function isConnected()
    {
        return !empty($this->writeConnectionRegistryConfig) && !empty($this->readConnectionRegistryConfig);
    }

    public function setOptions($options)
    {
        
    }

    public function prepare($query, $description)
    {
        $this->statement   = null;
        $this->queryType   = null;
        $this->query       = null;
        $this->description = $description;

        $type = false;
        foreach ($this->readQueryTypes as $query_type)
        {
            if (stripos($query, $query_type) === 0)
            {
                $type = $query_type;
                break;
            }
        }
        if (!$type)
        {
            foreach ($this->writeQueryTypes as $query_type)
            {
                if (stripos($query, $query_type) === 0)
                {
                    $type = $query_type;
                    break;
                }
            }
        }

        if (!$type)
        {
            throw new ConnectionInvalidQueryException('The query is of an unknown type: ' . $query);
        }

        $this->queryType = $type;
        $this->query     = $query;
    }

    public function execute()
    {
        if (!$this->isConnected())
        {
            $this->connect('default');
        }

        foreach ($this->boundParams as $param)
        {
            switch ($param['type'])
            {
                case Connection::TYPE_INTEGER:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], $this->escape((int) $param['value']), $this->query
                    );
                    break;
                case Connection::TYPE_STRING:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], '\'' . $this->escape((string) $param['value']) . '\'', $this->query
                    );
                    break;
                case Connection::TYPE_IDENTIFIER:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], '`' . $this->escape((string) $param['value']) . '`', $this->query
                    );
                    break;
                case Connection::TYPE_BOOLEAN:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], '\'' . $this->escape($param['value']) . '\'', $this->query
                    );
                    break;
                case Connection::TYPE_DATE:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], '\'' . $this->escape((string) $param['value']) . '\'', $this->query
                    );
                    break;
                case Connection::TYPE_RAW:
                    $this->query = str_replace(
                            ':' . $param['placeholder'], $this->escape((string) $param['value']), $this->query
                    );
                    break;

                default:
                    throw new ConnectionInvalidValueTypeException();
                    break;
            }
        }

        $this->boundParams = array();

        // First get the query type (read/write).
        if (in_array($this->queryType, $this->readQueryTypes))
        {
            $connection = $this->readConnectionRegistryConfig;
        }
        elseif (in_array($this->queryType, $this->writeQueryTypes))
        {
            $connection = $this->writeConnectionRegistryConfig;
        }
        else
        {
            throw new ConnectionException('The prepare() method must be called before execute()');
        }

        $pdo = Registry::getInstance()->get($connection['key']);

        if (is_null($pdo))
        {
            $pdo = new \PDO(
                    'mysql:host=' . $connection['hostname'] . ';dbname=' . $connection['database'], $connection['username'], $connection['password'], array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION)
            );

            Registry::getInstance()->set($connection['key'], $pdo);
        }

        $this->statement = $pdo->prepare($this->query);

        try
        {
            $success = $this->statement->execute();

            switch ($this->queryType)
            {

                case 'select':
                case 'show tables':
                    $return = $this->statement->fetchAll(\PDO::FETCH_ASSOC);
                    break;
                case 'show create table':
                    $return = $this->statement->fetch(\PDO::FETCH_ASSOC);
                    break;
                default:
                    $return = $success;
            }

            $error_info = $pdo->errorInfo();
        }
        catch (PDOException $e)
        {
            throw new ConnectionException('Database error while executing the query "' . $this->description . '": ' . $e->getMessage(), 0, $e);
        }

        if ($error_info[0] != '00000')
        {
            throw new ConnectionException('Database error while executing the query "' . $this->description . '": ' . $error_info[2]);
        }

        return $return;
    }

    public function bindParam($param, $value, $type)
    {
        $this->boundParams[] = array(
            'placeholder' => $param,
            'value'       => $value,
            'type'        => $type,
        );
    }

    protected function escape($string)
    {
        $charactersToEscape = array(
            '\\',
            ';',
            '\'',
            '"',
            '@',
        );

        foreach ($charactersToEscape as $char)
        {
            $string = str_replace($char, '\\' . $char, $string);
        }

        return $string;
    }

    /**
     * Gets the id of the last inserted record.
     * 
     * @throws 
     * 
     * @return integer
     */
    public function getLastInsertId()
    {
        /**
         * @todo Cache the return value of this in the Registry.
         */
        $sql = <<<SQL
SELECT LAST_INSERT_ID() AS last_insert_id
SQL;

        $this->prepare($sql, 'Gets the last insert id');

        $result = $this->execute();

        if (!is_numeric($result[0]['last_insert_id']) || $result[0]['last_insert_id'] < 1)
        {
            throw new PdoConnectionNoPreviousInsertException();
        }

        return $result[0]['last_insert_id'];
    }

}

class PdoConnectionException extends \Exception
{
    
}

class PdoConnectionNoPreviousInsertException extends PdoConnectionException
{
    
}

?>
