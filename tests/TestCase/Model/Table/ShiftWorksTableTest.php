<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShiftWorksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShiftWorksTable Test Case
 */
class ShiftWorksTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ShiftWorksTable
     */
    public $ShiftWorks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.shift_works'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ShiftWorks') ? [] : ['className' => ShiftWorksTable::class];
        $this->ShiftWorks = TableRegistry::get('ShiftWorks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ShiftWorks);

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
}
