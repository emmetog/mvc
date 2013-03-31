<?php

namespace Emmetog\Model;

use Emmetog\Model\Model;
use Emmetog\Database\PdoConnection;
use Emmetog\Config\ConfigException;

class DatabaseModel extends Model
{

    /**
     * @var PdoConnection
     */
    protected $db;

    protected function init() {
        
        $this->db = new PdoConnection($this->config);
        
        $profile = $this->config->getDatabaseConfig('profile');
        
        if(!$profile) {
            throw new ConfigException('No profile specified in database config file');
        }
        
        $this->db->connect($profile);
    }

}

?>
