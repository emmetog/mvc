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
        /*
         * We don't use the config to create the PdoConnection object because
         * if we did it would complain of 'unmocked model' errors in UTs.
         */
        $this->db = new PdoConnection();
        $this->db->setConfig($this->config);
    }

}

?>
