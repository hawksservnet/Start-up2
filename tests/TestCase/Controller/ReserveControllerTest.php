<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ReservesController;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ReservesController Test Case
 */
class ReserveControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.counsel_notes',
        'app.users',
        'app.preentre_requests',
        'app.shift_works',
        'app.concierges',
        'app.reserves'
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Reserve',
            'action' => 'index'
        ]);
        $this->assertResponseOK();

        $Reserves = TableRegistry::get('Reserves');
        $this->enableCsrfToken();
        $data = [
            'search' => '1',
            'concierges' => 1,
        ];
        $this->post([
            'prefix' => 'management',
            'controller' => 'Reserve',
            'action' => 'index',
            'page' => 1,
        ], $data);
        $this->assertResponseOK();
        $this->assertSession('1', 'search011.concierges');
        $this->assertSession(1, 'search011.page');
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexUpdateStatus()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $Reserves = TableRegistry::get('Reserves');
        $data = [
            'comment' => 'comment',
            'hid_rid' => '1',
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Reserve',
            'action' => 'index',
            'page' => 1,
        ], $data);
        $StatQ = $Reserves->find('all')->where(['id' => 1])->first();
        $this->assertEquals(9, $StatQ->reserve_status);
    }
}
