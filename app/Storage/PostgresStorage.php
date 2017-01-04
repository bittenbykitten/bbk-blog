<?php
namespace BBKBlog\Storage;

class PostgresStorage implements StorageInterface
{
    protected $db;

    public function __construct($db_name, $db_user, $db_password, $db_host = 'localhost', $db_port = '5432')
    {
        $this->db = \ParagonIE\EasyDB\Factory::create(
            'pgsql:'.
            'host='.$db_host.';'.
            'port='.$db_port.';'.
            'dbname='.$db_name,
            $db_user,
            $db_password
        );
    }

    public function insert(string $table, array $row, string $id_column = 'id') : int
    {
        return $this->db->insertGet($table, $this->stringifyRowData($row), $id_column);
    }

    public function findById(string $table, int $id) : ?array
    {
        return $this->db->row('select * from ' . $this->db->escapeIdentifier($table) . ' where id = ?', $id);
    }

    protected function stringifyRowData(array $row) : array
    {
        $stringifiedRow = [];

        foreach ($row as $name => $value) {
            $strValue = null;
            if ($value === null) {
                $strValue = null;
            } elseif ($value instanceof \DateTimeInterface) {
                $strValue = $value->format('Y-m-d H:i:s');
            } elseif(gettype($value) === 'boolean') {
                $strValue = $value;//!$value ? true : false;
            } elseif(method_exists($value, '__toString')) {
                $strValue = $value->__toString();
            } else {
                $strValue = '' . $value;
            }

            $stringifiedRow[$name] = $strValue;
        }

        return $stringifiedRow;
    }
}
