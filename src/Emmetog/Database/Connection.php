<?php

namespace Emmetog\Database;

use Emmetog\Database\ConnectionInterface;
use Emmetog\Config\HasConfig;

abstract class Connection implements ConnectionInterface
{

    use HasConfig;

    const TYPE_INTEGER = 101;
    const TYPE_STRING = 102;
    const TYPE_DATE = 103;
    const TYPE_BOOLEAN = 104;
    const TYPE_IDENTIFIER = 105;
    const TYPE_RAW = 106;

}

class ConnectionException extends \Exception
{
    
}

class ConnectionInvalidValueTypeException extends ConnectionException
{
    
}

class ConnectionInvalidParamNameException extends ConnectionException
{
    
}

class ConnectionInvalidQueryException extends ConnectionException
{
    
}

class ConnectionQueryNotPreparedException extends ConnectionException
{
    
}

?>
