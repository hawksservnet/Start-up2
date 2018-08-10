<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ConciergeInformationsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ConciergeInformationsTable Test Case
 */
class ConciergeInformationsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ConciergeInformationsTable
     */
    public $ConciergeInformations;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.concierge_informations',
        'app.concierges',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ConciergeInformations') ? [] : ['className' => ConciergeInformationsTable::class];
        $this->ConciergeInformations = TableRegistry::get('ConciergeInformations', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ConciergeInformations);

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
