<?php

namespace App\Test\TestCase\Controller\Mypage;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Mypage\NurseryController Test Case
 */
class NurseryControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.nursery_reserves',
        'app.nursery_schedule',
        'app.users',
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Name'
        ]);
        $this->get([
            'prefix' => 'mypage',
            'controller' => 'Nursery',
            'action' => 'index'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test index method
     *
     * @return void
     */
    public function testIndexCancel()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Name'
        ]);
        $data = [
            'nurse_reserve_id' => 1,
        ];
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Nursery', 'action' => 'index'], $data);
        $this->assertResponseOK();
        $nurseryReservesTable = TableRegistry::get('NurseryReserves');
        $countCancel = $nurseryReservesTable->find()
            ->where([
                'id' => $data['nurse_reserve_id'],
                'status' => 2,
            ])->count();
        $this->assertEquals($countCancel, 1);
    }
    /**
     * Test form method
     *
     * @return void
     */
    public function testForm()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Name'
        ]);
        $this->get(['prefix' => 'mypage', 'controller' => 'Nursery', 'action' => 'form']);
        $this->assertResponseOK();
    }
    /**
     * Test form method
     *
     * @return void
     */
    public function testAddSuccess()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Name'
        ]);

        $nurseryReservesTable = TableRegistry::get('NurseryReserves');
        $countOld = $nurseryReservesTable->find('all')->count();
        $data = [
            'purpose' => 1,
            'reserve_date' => '2017-11-09',
            'reserve_time_start' => '10:00',
            'reserve_time_end' => '13:00',
            'phone' => '111-1111-1111',
            'mailaddress' => 'test@test-test.com',
            'name1' => 'Name 1',
            'name_k1' => 'Name Kana 1',
            'age_year1' => 1,
            'age_month1' => 12,
            'sex1' => 1,
        ];
        $this->enableRetainFlashMessages();
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Nursery', 'action' => 'form'], $data);

//        $this->assertSession('一時保育を仮予約で、メール受信をもって本予約完了となります', 'Flash.flash.0.message');
        $countNew = $nurseryReservesTable->find('all')->count();
        $this->assertEquals($countOld + 1, $countNew);
    }
    /**
     * Test form method
     *
     * @return void
     */
    public function testAddFail()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Name'
        ]);

        $nurseryReservesTable = TableRegistry::get('NurseryReserves');
        $countOld = $nurseryReservesTable->find('all')->count();
        $data = [
            'purpose' => 1,
            'reserve_date' => '2017-11-09',
            'reserve_time_start' => '10:00',
            'reserve_time_end' => '13:00',
            'phone' => '123456789012345678901234567', /*test max 20*/
            'mailaddress' => 'test@test-test.com',
            'name1' => 'Name 1',
            'name_k1' => 'Name Kana 1',
            'age_year1' => 1,
            'age_month1' => 12,
            'sex1' => 1,
        ];
        $this->enableRetainFlashMessages();
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Nursery', 'action' => 'form'], $data);

        $this->assertSession(Configure::read('MESSAGE_NOTIFICATION.MSG_049'), 'Flash.flash.0.message');
        $countNew = $nurseryReservesTable->find('all')->count();
        $this->assertEquals($countOld, $countNew);
    }
}
