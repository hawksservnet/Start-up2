<?php
namespace App\Test\TestCase\Controller\Concierge;

use App\Controller\Concierge\ScheduleController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Concierge\ScheduleController Test Case
 */
class ScheduleControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.concierges',
        'app.shift_works',
        'app.reserves',
        'app.users',
        'app.concierge_informations',
        'app.counsel_notes'
    ];

    /**
     * Test month method
     *
     * @return void
     */
    public function testMonth()
    {
        $this->session([
            'MYPAGE.ID' => 12,
            'MYPAGE.NAME' => 'VO HONG THO'
        ]);
        $this->get([
            'prefix' => 'concierge',
            'controller' => 'Schedule',
            'action' => 'month',
        ]);
        $this->assertResponseOK();
    }

    /**
     * Test week method
     *
     * @return void
     */
    public function testWeek()
    {
        //$this->markTestIncomplete('Not implemented yet.');
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'VO HONG THO'
        ]);
        $this->get([
            'prefix' => 'concierge',
            'controller' => 'Schedule',
            'action' => 'week',
        ]);
        $this->assertResponseOK();
    }

    /**
     * Test day method
     *
     * @return void
     */
    public function testDay()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'VO HONG THO'
        ]);
        $this->get([
            'prefix' => 'concierge',
            'controller' => 'Schedule',
            'action' => 'day',
        ]);
        $this->assertResponseOK();
    }
}
