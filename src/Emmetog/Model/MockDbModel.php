<?php

namespace Emmetog\Model;

use Emmetog\Database\ConnectionException;
use Emmetog\Model\DatabaseModel;

/**
 * The Model which is used exclusively by the MockDb object to manage mock tables.
 */
class MockDbModel extends DatabaseModel
{

    /**
     * Gets the query that will recreate a table.
     * 
     * @param string $table The name of the table.
     * @param string $profile The profile of the original table.
     */
    public function getCreateTableQuery($table, $profile)
    {
        $this->db->connect($profile);

        $query = <<<QUERY
SHOW CREATE TABLE `:table`
QUERY;

        $query = str_replace(':table', $table, $query);

        $this->db->prepare(
                $query, 'Gets the query that will recreate the ' . $table . ' table'
        );

        $result = $this->db->execute();

        $create_table_query = $result['Create Table'];

        return $create_table_query;
    }

    /**
     * Creates a temporary table
     * 
     * @param string $table_definition The CREATE TABLE SQL query that will generate the table.
     * @return boolean True on success, false on failure.
     */
    public function createTable($table_definition)
    {
        // Modify the definition to make the table temporary.
        if (strpos($table_definition, 'CREATE TABLE') === false)
        {
            throw new ConnectionException('Invalid CREATE TABLE definition');
        }
        $table_definition = str_replace('CREATE TABLE', 'CREATE TEMPORARY TABLE', $table_definition);

        $this->db->connect('test');

        $this->db->prepare($table_definition, 'Creating a new temporary table');

        $result = $this->db->execute();

        return $result;
    }

    /**
     * Inserts data into a mocked table.
     * 
     * @param string $table_name The name of the table.
     * @param array $fields The array of field names.
     * @param array $data The data to insert, in the same order as the field names.
     */
    public function insertDataIntoMockedTable($table_name, array $fields, array $data)
    {
	if (count($data) === 0)
	{
	    return;
	}
	
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

        $this->db->connect('test');

        $this->db->prepare($query, 'Inserting initial mocked data into mocked ' . $table_name . ' table');

        $this->db->execute();
    }
    
    /**
     * Gets the PdoConnection object that this model is using to connect to the db.
     * 
     * @return PdoConnection
     */
    public function getDbObject()
    {
	return $this->db;
    }

}

?>
