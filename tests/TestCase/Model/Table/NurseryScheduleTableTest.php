<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\NurseryScheduleTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\NurseryScheduleTable Test Case
 */
class NurseryScheduleTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\NurseryScheduleTable
     */
    public $NurserySchedule;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [

    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('NurserySchedule') ? [] : ['className' => NurseryScheduleTable::class];
        $this->NurserySchedule = TableRegistry::get('NurserySchedule', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->NurserySchedule);

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
