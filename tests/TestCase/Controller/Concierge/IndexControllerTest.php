<?php
namespace App\Test\TestCase\Controller\Concierge;

use App\Controller\Concierge\IndexController;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\Concierge\IndexController Test Case
 */
class IndexControllerTest extends IntegrationTestCase
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
        'app.concierge_informations'
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
            'MYPAGE.NAME' => 'Tuan'
        ]);
        $this->get([
            'prefix' => 'concierge',
            'controller' => 'Index',
            'action' => 'index',
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test index concierge
     *
     * @return void
     */
    public function testIndexconcierges()
    {
        $this->session([
            'MYPAGE.ID' => 1,
            'MYPAGE.NAME' => 'Tuan'
        ]);
        $conciergesTable = TableRegistry::get('Concierges');
        $mconcierges = $conciergesTable->find()
            ->contain([
                'ConciergeInformations' => [
                    'sort' => ['ConciergeInformations.sort_no' => 'ASC'],
                    'queryBuilder' => function ($q) {
                        return $q->where(['ConciergeInformations.avail_flg' => 1]);
                    }
                ]
            ])
            ->where([
                'Concierges.id' => 1,
                'Concierges.avail_flg' => 1
            ]);
        $this->assertEquals(1, $mconcierges->count());
    }
}
