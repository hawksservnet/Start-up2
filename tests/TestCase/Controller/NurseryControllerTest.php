<?php
namespace App\Test\TestCase\Controller;

use App\Controller\NurseryController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\NurseryController Test Case
 */
class NurseryControllerTest extends IntegrationTestCase
{

    public $fixtures = [
        'app.nursery_schedule',
        'app.nursery_reserves'
    ];
    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test month method
     *
     * @return void
     */
    public function reserve()
    {
        $this->session([
            'LOGIN.ID' => 12,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Nursery',
            'action' => 'reserve'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test month method
     *
     * @return void
     */
    public function schedule()
    {
        $this->session([
            'LOGIN.ID' => 12,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Nursery',
            'action' => 'schedule'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test month method
     *
     * @return void
     */
    public function setting()
    {
        $this->session([
            'LOGIN.ID' => 12,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Nursery',
            'action' => 'setting'
        ]);
        $this->assertResponseOK();
    }
}
