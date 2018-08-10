<?php

namespace App\Test\TestCase\Controller\Mypage;

use App\Controller\Mypage\ReserveController;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Mypage\ReserveController Test Case
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
            'MYPAGE.ID' => 2,
            'MYPAGE.NAME' => 'Hero'
        ]);
        $this->get([
            'prefix' => 'mypage',
            'controller' => 'Reserve',
            'action' => 'index'
        ]);
        $this->assertResponseOK();
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
            'MYPAGE.NAME' => 'Hero'
        ]);
        $this->get(['prefix' => 'mypage', 'controller' => 'Reserve', 'action' => 'form', '?' => ['id' => '1', 'his' => '106', 'cn' => '1']]);
        $this->assertResponseOK();

        $reserve = TableRegistry::get('Reserves');
        $oldCount = $reserve->find('all')->count();
        $data = [
            'reserves' => '1',
            'step' => '2',
            'work_date' => '2017-08-28',
            'work_time_start' => '1000',
            'work_time_end' => '',
            'concierge_id' => '1',
            'concierge_name' => 'dart',
            'user_id' => '1',
            'user_name' => 'テスト アカウント1',
            'user_type' => '1',
            'achieve' => '2',
            'achieve_text' => ['a', 'b', 'c', 'd'],
            'question8_1' => '3',
            'question8_1text' => '',
            'question8_2' => '4',
            'question8_2text' => 'question8_2text',
            'question8_3' => '',
            'question8_3text' => '',
            'question8_4' => '',
            'question8_4text' => '',
            'question9_1text' => '',
            'question1_1' => '1',
            'question2_1' => '2',
            'question3_1' => ['1', '3', '5', '9'],
            'question3_1text' => 'question3_1text',
            'question3_2' => ['1', '2', '5'],
            'question3_2text' => 'question3_2text',
            'question4_1' => '3',
            'question6_1' => ['1', '2', '3'],
            'question6_1text' => 'question 6-1 text',
            'question5_1text' => 'あなたが考えている事業の概略を可能な範囲で記入してください',
            'question7_1' => '4',
        ];
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Reserve', 'action' => 'form', '?' => ['id' => '1', 'his' => '106', 'cn' => '1']], $data);
        $this->assertResponseOK();
/*
        $data = [
            'reserves' => '1',
            'work_date' => date('Y-m-d', time('+1 day')),
            'work_time_start' => '1000',
            'work_time_end' => '',
            'concierge_id' => '1',
            'concierge_name' => 'dart',
            'user_id' => '1',
            'user_name' => 'テスト アカウント1',
            'user_type' => '1',
            'achieve' => '2',
            'achieve_text' => 'a|b|c|d',
            'question8_1' => '3',
            'question8_1text' => '',
            'question8_2' => '4',
            'question8_2text' => 'question8_2text',
            'question8_3' => '',
            'question8_3text' => '',
            'question8_4' => '',
            'question8_4text' => '',
            'question9_1text' => '',
            'question1_1' => '1',
            'question2_1' => '2',
            'question3_1' => '1,3,5,9',
            'question3_1text' => 'question3_1text',
            'question3_2' => '1,2,5',
            'question3_2text' => 'question3_2text',
            'question4_1' => '3',
            'question6_1' => '1,2,3',
            'question6_1text' => 'question 6-1 text',
            'question5_1text' => 'あなたが考えている事業の概略を可能な範囲で記入してください',
            'question7_1' => '4',
        ];
        $this->session([
            'data_save.time' => strtotime('+10 minutes', time()),
            'data_save.data' => $data
        ]);
        $data1 = ['step' => '3', 'test' => '1'];
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Reserve', 'action' => 'form', '?' => ['id' => '1', 'his' => '106', 'cn' => '1']], $data1);
        $this->assertResponseOK();
        $newCount = $reserve->find('all')->count();
        $this->assertEquals($oldCount, $newCount);

        $data['achieve'] = '2';
        $data['achieve_text'] = 'a1|b2|c2|d2';
        $this->session([
            'data_save.time' => strtotime('+10 minutes', time()),
            'data_save.data' => $data
        ]);
        $data1 = ['step' => '3', 'test' => '1'];
        $this->enableCsrfToken();
        $this->post(['prefix' => 'mypage', 'controller' => 'Reserve', 'action' => 'form', '?' => ['id' => '1', 'his' => '106', 'rs' => '2', 'cn' => '1']], $data1);
        $this->assertResponseOK();
        $query = TableRegistry::get('CounselNotes')->find()
            ->select()
            ->where([
                'id' => 2
            ]);
        $this->assertEquals(1, $query->count());
*/
    }
}
