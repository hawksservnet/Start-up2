<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NurseryReservesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NurseryReservesTable Test Case
 */
class NurseryReservesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\NurseryReservesTable
     */
    public $NurseryReserves;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.nursery_reserves',
        'app.users'

    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('NurseryReserves') ? [] : ['className' => NurseryReservesTable::class];
        $this->NurseryReserves = TableRegistry::get('NurseryReserves', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NurseryReserves);

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
