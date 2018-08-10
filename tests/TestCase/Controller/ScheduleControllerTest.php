<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ScheduleController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ScheduleController Test Case
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
        'app.users'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        /*
        $this->markTestIncomplete('Not implemented yet.');
        */
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        /*
        $this->markTestIncomplete('Not implemented yet.');
        */
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        /*
        $this->markTestIncomplete('Not implemented yet.');
        */
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        /*
        $this->markTestIncomplete('Not implemented yet.');
        */
    }

    /**
     * Test month method
     *
     * @return void
     */
    public function testMonth()
    {
        $this->session([
            'LOGIN.ID' => 12,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Schedule',
            'action' => 'month',
            '?' => ['id' => '0', 'dt' => '2017-08-21']
        ]);
        //$this->get('/management/schedule/day?id=0&dt=2017-08-21');
        $this->assertResponseOK();
    }

    /**
     * Test week method
     *
     * @return void
     */
    public function testWeek()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Schedule',
            'action' => 'week',
            '?' => ['id' => '0', 'dt' => '2017-08-14']
        ]);
        //$this->get('/management/schedule/day?id=0&dt=2017-08-21');
        $this->assertResponseOK();

        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 3
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Schedule',
            'action' => 'week',
            '?' => ['id' => '0', 'dt' => '2017-08-14']
        ]);
        //$this->get('/management/schedule/day?id=0&dt=2017-08-21');
        $ok = $this->assertResponseOK();
        //ar_dump($ok);
    }

    /**
     * Test day method
     *
     * @return void
     */
    public function testDay()
    {
        $this->session([
            'LOGIN.ID' => 12,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Schedule',
            'action' => 'day',
            '?' => ['id' => '0', 'dt' => '2017-08-21']
        ]);
        //$this->get('/management/schedule/day?id=0&dt=2017-08-21');
        $this->assertResponseOK();
    }
}
