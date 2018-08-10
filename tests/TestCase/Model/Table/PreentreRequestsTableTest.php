<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PreentreRequestsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PreentreRequestsTable Test Case
 */
class PreentreRequestsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PreentreRequestsTable
     */
    public $PreentreRequests;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.preentre_requests',
        //'app.users',
        //'app.event_requests',
        //'app.events',
        //'app.interview_answers',
        //'app.reserves',
        //'app.concierges',
        'app.acounts',
        //'app.concierge_informations',
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
        $config = TableRegistry::exists('PreentreRequests') ? [] : ['className' => PreentreRequestsTable::class];
        $this->PreentreRequests = TableRegistry::get('PreentreRequests', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PreentreRequests);

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
