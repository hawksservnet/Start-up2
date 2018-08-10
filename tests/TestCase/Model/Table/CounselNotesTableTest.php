<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CounselNotesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CounselNotesTable Test Case
 */
class CounselNotesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\CounselNotesTable
     */
    public $CounselNotes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
        $config = TableRegistry::exists('CounselNotes') ? [] : ['className' => CounselNotesTable::class];
        $this->CounselNotes = TableRegistry::get('CounselNotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CounselNotes);

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
