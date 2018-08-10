<?php

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\CounselNoteController Test Case
 */
class CounselNoteControllerTest extends IntegrationTestCase
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
     * Test info method
     *
     * @return void
     */
    public function testInfo()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get(['prefix' => 'management', 'controller' => 'CounselNote', 'action' => 'info', '?' => ['id' => '1', 'rs' => '1', 'dt' => '2017']]);
        $this->assertResponseOK();
    }

    /**
     * Test note method
     *
     * @return void
     */
    public function testNote()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get(['prefix' => 'management', 'controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => '1', 'rs' => '1']]);
        $this->assertResponseOk();

        $counsel_notes = TableRegistry::get('CounselNotes');
        $oldCount = $counsel_notes->find('all')->count();
        $data = [
            'id' => '1',
            'rs' => '1',
            'counsel_notes_id' => '1',
            'status' => 'c_3',
            'work_time_start' => '11:33',
            'work_time_end' => '13:33',
            'achieve' => '2',
            'achieve_text' => ['achieve1', 'achieve2', 'achieve3', 'achieve4'],
            'question1_1' => '1',
            'question2_1' => '2',
            'question3_1' => ['1', '5', '9'],
            'question3_1text' => 'その他',
            'question3_2' => ['1', '5'],
            'question3_2text' => 'その他',
            'question4_1' => '2',
            'question6_1' => ['1', '2', '3'],
            'question6_1text' => 'question 6.1 text',
            'question5_1text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'question7_1' => '2',
            'question8_1' => '3',
            'question8_1text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'question9_1text' => '',
            'question8_2' => '4',
            'question8_2text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'question8_3' => 'Lorem ipsum dolor sit amet',
            'question8_3text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'question8_4' => 'Lorem ipsum dolor sit amet',
            'question8_4text' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            'anser1' => 'anser1',
            'anser2' => 'anser2',
            'anser3' => 'anser3',
            'anser4' => 'anser4',
            'evaluate1' => '5',
            'evaluate2' => '6',
            'evaluate3' => '7',
            'evaluate4' => '8',
            'evaluate5' => '9',
            'button' => '1',
            'comment' => ''
        ];
        $this->enableCsrfToken();
        $this->post(['prefix' => 'management', 'controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => '1', 'rs' => '1']], $data);

        $data['button'] = '2';
        $this->enableCsrfToken();
        $this->post(['prefix' => 'management', 'controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => '1', 'rs' => '1']], $data);

        $query = $counsel_notes->find()
            ->select()
            ->where([
                'id' => $data['counsel_notes_id']
            ]);
        $this->assertEquals(1, $query->count());
        //$this->assertEquals('achieve1|achieve2|achieve3|achieve4', $query->first()->achieve_text);
        $newCount = $counsel_notes->find('all')->count();
        $this->assertEquals($oldCount, $newCount);
        //$this->assertResponseOk();
        //$this->assertRedirect(['controller' => 'CounselNote', 'action' => 'note', '1', '1']);
    }
}
