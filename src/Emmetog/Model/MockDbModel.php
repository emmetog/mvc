<?php

namespace Emmetog\Model;

use Emmetog\Model\Model;
use Emmetog\Database\ConnectionException;

class MockDbModel extends Model
{

    /**
     * Gets the query that will recreate the table.
     * 
     * @param string $table
     */
    public function getCreateTableQuery($table)
    {
        $query = <<<QUERY
SHOW CREATE TABLE `:table`
QUERY;

        $query = str_replace(':table', $table, $query);

        $this->db->prepare(
                $query,
                'Gets the query that will recreate the ' . $table . ' table'
        );

        $result = $this->db->execute();

        $create_table_query = $result['Create Table'];

        return $create_table_query;
    }

    /**
     * Creates a temporary table
     * @param type $table_definition
     * @return type
     */
    public function createTable($table_definition)
    {
        // Modify the definition to make the table temporary.
        if (strpos($table_definition, 'CREATE TABLE') === false)
        {
            throw new ConnectionException('Invalid CREATE TABLE definition');
        }
        $table_definition = str_replace('CREATE TABLE',
                'CREATE TEMPORARY TABLE', $table_definition);

        $this->db->prepare($table_definition, 'Creating a new temporary table');

        $result = $this->db->execute();

        return $result;
    }

    public function insertDataIntoMockedTable($table_name, $fields, $data)
    {
        $query = 'INSERT INTO `' . $table_name . '` (';

        foreach ($fields as $field)
        {
            $query .= '`' . $field . '`, ';
        }
        $query = substr($query, 0, strlen($query) - 2);

        $query .= ') VALUES ';

        foreach ($data as $entry)
        {
            $query .= '(';
            foreach ($entry as $value)
            {
                if (is_null($value))
                {
                    $query .= 'NULL, ';
                }
                else
                {
                    $query .= "'$value', ";
                }
            }
            $query = substr($query, 0, strlen($query) - 2);

            $query .= '), ';
        }
        $query = substr($query, 0, strlen($query) - 2);
        
        $this->db->prepare($query, 'Inserting initial mocked data into mocked '.$table_name.' table');

        $result = $this->db->execute();

        return $result;
    }

}

?>
