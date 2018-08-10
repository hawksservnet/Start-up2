<?php
namespace App\Test\TestCase\Controller;

use App\Utilities\AuthDriver;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\ConciergeController Test Case
 */
class ConciergeControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     * @author huynh
     * @var array
     */
    public $fixtures = [
        'app.concierges',
        'app.acounts',
        'app.concierge_informations'
    ];

    /**
     * Test index method
     * @author huynh
     * @return void
     */
    public function testIndexWithAuthority()
    {
        $redirectUrl = Configure::read('FUEL_ADMIN_URL') . 'admin/users';
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertRedirectContains(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 1
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 2
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 3
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 4
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertResponseOK();
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test index method
     * @author huynh
     * @return void
     */
    public function testEditWithAuthority()
    {
        $redirectUrl = Configure::read('FUEL_ADMIN_URL') . 'admin/users';
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 1
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 2
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 3
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 4
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertResponseOK();
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test index method
     * @author huynh
     * @return void
     */
    public function testIndexSearch()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $data = [
            'type' => 1,
            'search_name' => '11',
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index',
            'page' => 1,
            'sort' => 'name',
            'direction' => 'desc'
        ], $data);
        $this->assertResponseSuccess();
        $this->assertSession('11', 'search015.name');
        $this->assertSession(1, 'search015.page');
        $this->assertSession('name desc', 'search015.orderby');
    }
    /**
     * Test delete method
     * @author huynh
     * @return void
     */
    public function testDelete()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $accounts = TableRegistry::get('Acounts');
        $concierges = TableRegistry::get('Concierges');
        $ci = TableRegistry::get('ConciergeInformations');
        $oldAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $data = [
            'type' => 2,
            'delete_id' => 1,
        ];
        $delCi = $ci->find('all')
            ->where([
                'avail_flg' => 1,
                'concierge_id' => $data['delete_id']
                ])
            ->count();
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'index'
        ], $data);
        $newAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $this->assertEquals(($oldAccount - 1), $newAccount);
        $this->assertEquals(($oldConcierge - 1), $newConcierge);
        $this->assertEquals(($oldCi - $delCi), $newCi);
    }
    /**
     * Test add method
     * @author huynh
     * @return void
     */
    public function testAdd()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $accounts = TableRegistry::get('Acounts');
        $concierges = TableRegistry::get('Concierges');
        $ci = TableRegistry::get('ConciergeInformations');
        $oldAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $data = [
            'id' => 0,
            'name' => 'ドルモン',
            'name_e' => 'ConciergTest',
            'career' => 'career test test',
            'login_id' => 'AccountTest',
            'sort_no' => '3',
            'mailaddress' => 'email@mail-domain.test',
            'password' => 'AccountPassword1',
            'add_keyword_value_0_111' => 'KeywordTest',
            'add_language_value_0_222' => 'LanguageTest',
            'image_name' => '',
            'delete_list' => '0'
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit'
        ], $data);
        $query = $accounts->find()
            ->select('id')
            ->where([
                'login_id' => $data['login_id'],
                'password' => (new AuthDriver)->hashPassword($data['password']),
                'authority' => 3,
            ]);
        $this->assertEquals(1, $query->count());
        $conciergeQuery = $concierges->find('all')
            ->where([
                'avail_flg' => 1,
                'name' => 'ドルモン',
                'account_id' => $query->first()->id
            ]);
        $this->assertEquals(1, $conciergeQuery->count());
        $ciQuery = $ci->find('all')
            ->where([
                'avail_flg' => 1,
                'info_type' => 1,
                'concierge_id' => $conciergeQuery->first()->id
            ]);
        $this->assertEquals('KeywordTest', $ciQuery->first()->info_text);
        $this->assertEquals(1, $ciQuery->count());
        $ciQuery = $ci->find('all')
            ->where([
                'avail_flg' => 1,
                'info_type' => 2,
                'concierge_id' => $conciergeQuery->first()->id
            ]);
        $this->assertEquals('LanguageTest', $ciQuery->first()->info_text);
        $this->assertEquals(1, $ciQuery->count());
        $newAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $this->assertEquals(($oldAccount + 1), $newAccount);
        $this->assertEquals(($oldConcierge + 1), $newConcierge);
        $this->assertEquals(($oldCi + 2), $newCi);
    }
    /**
     * Test edit method
     * @author huynh
     * @return void
     */
    public function testEdit()
    {
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $accounts = TableRegistry::get('Acounts');
        $concierges = TableRegistry::get('Concierges');
        $ci = TableRegistry::get('ConciergeInformations');
        $oldAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $oldCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $data = [
            'id' => 1,
            'name' => 'ナルト',
            'name_e' => 'ConciergTest',
            'career' => 'career test test',
            'login_id' => 'AccountTest',
            'password' => 'AcountPassword1',
            'sort_no' => '3',
            'mailaddress' => 'email@mail-domain.test',
            'add_keyword_value_0_111' => 'KeywordTest',
            'add_language_value_0_222' => 'LanguageTest',
            'delete_list' => '0,1,2',
            'image_name' => ''
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Concierge',
            'action' => 'edit',
            'id' => 1
        ], $data);
        $query = $accounts->find()
            ->where([
                'login_id' => $data['login_id'],
                'password' => (new AuthDriver)->hashPassword($data['password']),
                'authority' => 3,
            ]);
        $this->assertEquals(1, $query->count());
        $conciergeQuery = $concierges->find('all')
            ->where([
                'avail_flg' => 1,
                'name' => 'ナルト',
                'account_id' => $query->first()->id,
                'id' => $data['id']
            ]);
        $this->assertEquals(1, $conciergeQuery->count());
        $ciQuery = $ci->find('all')
            ->where([
                'avail_flg' => 1,
                'info_type' => 1,
                'concierge_id' => $data['id']
            ]);
        $this->assertEquals('KeywordTest', $ciQuery->first()->info_text);
        $this->assertEquals(1, $ciQuery->count());
        $ciQuery = $ci->find('all')
            ->where([
                'avail_flg' => 1,
                'info_type' => 2,
                'concierge_id' => $data['id']
            ]);
        $this->assertEquals('LanguageTest', $ciQuery->first()->info_text);
        $this->assertEquals(1, $ciQuery->count());
        $newAccount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newConcierge = $concierges->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $newCi = $ci->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $this->assertEquals(($oldAccount), $newAccount);
        $this->assertEquals(($oldConcierge), $newConcierge);
        $this->assertEquals(($oldCi), $newCi);
    }
}
