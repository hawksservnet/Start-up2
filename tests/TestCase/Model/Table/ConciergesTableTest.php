<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ConciergesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ConciergesTable Test Case
 */
class ConciergesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ConciergesTable
     */
    public $Concierges;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.concierges',
        'app.acounts',
        'app.concierge_informations',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Concierges') ? [] : ['className' => ConciergesTable::class];
        $this->Concierges = TableRegistry::get('Concierges', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Concierges);

        parent::tearDown();
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
