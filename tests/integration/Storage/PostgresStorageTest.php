<?php
namespace BBKBlog\Storage;

use PHPUnit\Framework\TestCase;

/**
 * Postgresql setup:
 *
 *  CREATE DATABASE "test_database";
 *  CREATE USER test_user WITH PASSWORD 'test_password';
 *  GRANT ALL PRIVILEGES ON DATABASE "test_database" to test_user;
 *
 */
class PostgresStorageTest extends TestCase
{
    private static $db;
    private $storage;
    private $inserted_id;

    public function testCanConstruct()
    {
        $this->startWithANewPostgresStorageAndAnEmptyPostTable();

        $this->assertInstanceOf(PostgresStorage::class, $this->storage);
    }

    public function testInsert()
    {
        $this->startWithANewPostgresStorageAndAnEmptyPostTable();

        $this->insertNewPost();
        
        $this->insertedPostExists();
    }

    public function testFindByIdReturnsRowWhenIdExists()
    {
        $this->startWithANewPostgresStorageWithThreePosts();

        $row = $this->storage->findById(self::getTestTableName(), 1);

        $this->postExistsWithId(1, $row);

        $row = $this->storage->findById(self::getTestTableName(), 2);

        $this->postExistsWithId(2, $row);

        $row = $this->storage->findById(self::getTestTableName(), 3);

        $this->postExistsWithId(3, $row);
    }

    public function testFindByIdReturnsNullWhenIdNotFound()
    {
        $this->startWithANewPostgresStorageAndAnEmptyPostTable();

        $row = $this->storage->findById(self::getTestTableName(), 4);

        $this->assertNull($row);
    }

    /* Setup */

    public static function setUpBeforeClass()
    {
        // Database connection
        self::$db = \ParagonIE\EasyDB\Factory::create(
            'pgsql:'.
            'host='.self::getTestDatabaseHost().';'.
            'port='.self::getTestDatabasePort().';'.
            'dbname='.self::getTestDatabaseName(),
            self::getTestDatabaseUser(),
            self::getTestDatabasePassword()
        );

        // Drop the test table
        self::$db->run("drop table if exists ".self::$db->escapeIdentifier(self::getTestTableName())."");
        
        // Create the test table
        self::$db->run("
            create table ".self::$db->escapeIdentifier(self::getTestTableName())." (
                id serial primary key,
                title text,
                content text,
                publish_date timestamp without time zone,
                draft boolean
            )");
    }

    public static function tearDownAfterClass()
    {
        // Drop the test table
        self::$db->run("drop table if exists ".self::$db->escapeIdentifier(self::getTestTableName())."");
    }

    private function startWithANewPostgresStorageAndAnEmptyPostTable()
    {
        // Empty the test table
        self::$db->run("truncate ".self::$db->escapeIdentifier(self::getTestTableName())."");
        
        // Create the storage object
        $this->storage = new PostgresStorage(
            self::getTestDatabaseName(),
            self::getTestDatabaseUser(),
            self::getTestDatabasePassword(),
            self::getTestDatabaseHost(),
            self::getTestDatabasePort()
        );
    }
    
    private function startWithANewPostgresStorageWithThreePosts()
    {
        $this->startWithANewPostgresStorageAndAnEmptyPostTable();

        self::$db->insertMany(self::getTestTableName(), $this->boolToString($this->getTestMultipleRowData()));
    }
    
    /* Assertions */

    private function insertNewPost()
    {
        $this->inserted_id = $this->storage->insert(self::getTestTableName(), $this->getTestRowData());

        $this->assertInternalType('int', $this->inserted_id);
    }

    private function postExistsWithId($id, $expected = [])
    {
        $row = self::$db->row('select * from '.self::$db->escapeIdentifier(self::getTestTableName()).' where id = ?', $id);

        $this->assertNotEmpty($row);

        if (!empty($expected)) {
            $this->assertEquals($expected, $row);
        }
    }

    private function insertedPostExists()
    {
        $this->assertInternalType('int', $this->inserted_id);

        $expected = $this->getTestRowData();
        $expected['id'] = $this->inserted_id;

        $this->postExistsWithId($this->inserted_id, $expected);
    }

    /* Helpers /*

    /**
     * PDO is annoying. Need to insert bools using strings but it returns them as bools >.<
     */
    private function boolToString($rows)
    {
        $multi = is_array(reset($rows));
        $rows = $multi ? $rows : [$rows];

        foreach ($rows as $num => $row) {
            foreach ($row as $name => $value) {
                if (gettype($value) === 'boolean') {
                    $rows[$num][$name] = $value ? 'true' : 'false';
                }
            }
        }
        
        return $multi ? $rows : $rows[0];
    }

    /* Test Data */

    private function getTestRowData()
    {
        return [
            'title' => 'Post Title',
            'content' => 'Some Content',
            'publish_date' => '2017-01-01 17:00:00',
            'draft' => true,
        ];
    }

    private function getTestMultipleRowData()
    {
        return [
            [
                'id' => 1,
                'title' => 'Post Title 1',
                'content' => 'Some Content 1',
                'publish_date' => '2017-01-01 17:00:00',
                'draft' => true,
            ],
            [
                'id' => 2,
                'title' => 'Post Title 2',
                'content' => 'Some Content 2',
                'publish_date' => '2017-01-02 17:00:00',
                'draft' => false,
            ],
            [
                'id' => 3,
                'title' => 'Post Title 3',
                'content' => 'Some Content 3',
                'publish_date' => '2017-01-03 17:00:00',
                'draft' => true,
            ],
        ];
    }

    private static function getTestTableName()
    {
        return 'posts';
    }

    /* Database connection info */

    private static function getTestDatabaseName()
    {
        return 'test_database';
    }

    private static function getTestDatabaseUser()
    {
        return 'test_user';
    }

    private static function getTestDatabasePassword()
    {
        return 'test_password';
    }

    private static function getTestDatabaseHost()
    {
        return 'postgres';
    }

    private static function getTestDatabasePort()
    {
        return '5432';
    }
}
