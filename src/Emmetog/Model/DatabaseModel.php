<?php

namespace Emmetog\Model;

use Emmetog\Model\Model;
use Emmetog\Database\PdoConnection;

/**
 * A base class for any Models which access a database.
 */
class DatabaseModel extends Model
{

    /**
     * @var PdoConnection
     */
    protected $db;

    /**
     * Sets up the DatabaseModel for use.
     */
    protected function init()
    {
        $this->db = new PdoConnection($this->config);
    }

}

?>
