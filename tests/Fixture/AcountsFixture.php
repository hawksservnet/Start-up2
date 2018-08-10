<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AcountsFixture
 *
 */
class AcountsFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'login_id' => ['type' => 'string', 'length' => 16, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'account_name' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'authority' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'avail_flg' => ['type' => 'integer', 'length' => 1, 'unsigned' => false, 'null' => false, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created_date' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        'modified_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'modified_date' => ['type' => 'timestamp', 'length' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'login_id' => 'Test1111',
            'password' => 'Abc45678',
            'account_name' => 'Account Test1',
            'authority' => 0,
            'avail_flg' => 1,
            'created_id' => 1,
            'created_date' => 1502873210,
            'modified_id' => 1,
            'modified_date' => 1502873210
        ],
        [
            'id' => 2,
            'login_id' => 'Test1112',
            'password' => 'Abc45678',
            'account_name' => 'Account Test2',
            'authority' => 1,
            'avail_flg' => 1,
            'created_id' => 1,
            'created_date' => 1502873210,
            'modified_id' => 1,
            'modified_date' => 1502873210
        ],
        [
            'id' => 3,
            'login_id' => 'Test1113',
            'password' => 'Abc33333',
            'account_name' => 'Account Test3',
            'authority' => 3,
            'avail_flg' => 1,
            'created_id' => 1,
            'created_date' => 1502873210,
            'modified_id' => 1,
            'modified_date' => 1502873210
        ],
        [
            'id' => 4,
            'login_id' => 'Test1114',
            'password' => 'Abc44444',
            'account_name' => 'Account Test4',
            'authority' => 3,
            'avail_flg' => 1,
            'created_id' => 1,
            'created_date' => 1502873210,
            'modified_id' => 1,
            'modified_date' => 1502873210
        ],
    ];
}
