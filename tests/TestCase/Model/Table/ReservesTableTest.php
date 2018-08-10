<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ReservesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ReservesTable Test Case
 */
class ReservesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ReservesTable
     */
    public $Reserves;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.reserves',
        'app.concierges',
        'app.concierge_informations',
        'app.users',
        'app.counsel_notes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Reserves') ? [] : ['className' => ReservesTable::class];
        $this->Reserves = TableRegistry::get('Reserves', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Reserves);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
