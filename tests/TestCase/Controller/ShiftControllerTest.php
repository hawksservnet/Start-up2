<?php
namespace App\Test\TestCase\Controller;

use App\Controller\ReservesController;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ReservesController Test Case
 */
class ShiftControllerTest extends IntegrationTestCase
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
        $data = [
            'work_date' => '2017-08-17',
        ];
        $this->get([
            'prefix' => 'management',
            'controller' => 'Shift',
            'action' => 'index'
        ], $data['work_date']);
        $this->assertResponseOK();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexAdd()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $data = [
            // 'work_date' => '2017-09-05',
            '2017-08-28_10' => ['0' => '2'],
            '2017-08-28_10_Arr' => ['0' => '0_1'],
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Shift',
            'action' => 'index', '2017-08-28'
        ], $data);
        $this->assertResponseOK();
        $Shifts = TableRegistry::get('ShiftWorks');
        $UQ = $Shifts->find('all')->where(['id' => 3])->first();
        $this->assertEquals('1000', $UQ->work_time_start);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexUpdate()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $data = [
            // 'work_date' => '2017-08-17',
            '2017-08-21_10' => ['0' => '2'],
            '2017-08-21_10_Arr' => ['0' => '2_1'],
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Shift',
            'action' => 'index', '2017-08-21'
        ], $data);
        // $this->assertResponseOK();
        $Shifts = TableRegistry::get('ShiftWorks');
        $UQ = $Shifts->find('all')->where(['id' => 2])->first();
        $this->assertEquals(2, $UQ->concierge_id);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexDelete()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $data = [
            // 'work_date' => '2017-08-17',
            '2017-08-28_10' => ['0' => '0'],
            '2017-08-28_10_Arr' => ['0' => '1_2'],
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Shift',
            'action' => 'index', '2017-08-28'
        ], $data);
        $this->assertResponseOK();
        $Shifts = TableRegistry::get('ShiftWorks');
        $DQ = $Shifts->find('all')->count();
        // debug($DQ->toArray());
        $this->assertEquals(1, $DQ);
    }
}
