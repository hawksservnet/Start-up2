<?php
namespace App\Test\TestCase\Controller;

use App\Utilities\AuthDriver;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\AccountController Test Case
 */
class AccountControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.acounts'
    ];

    /**
     * Test index method
     * @author huynh
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
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertResponseOK();
    }
    /**
     * Test index method
     * @author huynh
     * @return void
     */
    public function testIndexAuthority()
    {
        $redirectUrl = Configure::read('FUEL_ADMIN_URL') . 'admin/users';
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertRedirectContains(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 1
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 2
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 3
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 4
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ]);
        $this->assertRedirectContains($redirectUrl);
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
            'controller' => 'Account',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 1
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 2
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 3
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 4
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit'
        ]);
        $this->assertRedirectContains($redirectUrl);
        $this->session([
            'LOGIN.ID' => 1,
            'LOGIN.AUTH' => 0
        ]);
        $this->get([
            'prefix' => 'management',
            'controller' => 'Account',
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
            'search_login_id' => '11',
            'search_acount_name' => 'Test1',
            'search_authority' => 0
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index',
            'page' => 1,
            'sort' => 'login_id',
            'direction' => 'desc'
        ], $data);
        $this->assertResponseSuccess();
        $this->assertSession('11', 'search006_01.login_id');
        $this->assertSession('Test1', 'search006_01.acount_name');
        $this->assertSession(0, 'search006_01.authority');
        $this->assertSession(1, 'search006_01.page');
        $this->assertSession('login_id desc', 'search006_01.orderby');
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
        $oldCount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $data = [
            'type' => 2,
            'delete_id' => 1,
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'index'
        ], $data);
        $newCount = $accounts->find('all')
            ->where(['avail_flg' => 1])
            ->count();
        $this->assertEquals(($oldCount - 1), $newCount);
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
        $oldCount = $accounts->find('all')->count();
        $data = [
            'id' => 0,
            'account_name' => 'Account Name',
            'login_id' => 'testAddLoginId1',
            'password' => 'testAddPassword1',
            'authority' => 1
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit'
        ], $data);
        $query = $accounts->find()
            ->select(['login_id', 'password', 'authority'])
            ->where([
                'login_id' => $data['login_id'],
                'password' => (new AuthDriver)->hashPassword($data['password']),
                'authority' => $data['authority'],
            ]);
        $this->assertEquals(1, $query->count());
        $newCount = $accounts->find('all')->count();
        $this->assertEquals(($oldCount + 1), $newCount);
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
        $oldCount = $accounts->find('all')->count();
        $data = [
            'id' => 1,
            'login_id' => 'testEdit123',
            'account_name' => 'Account Name',
            'password' => 'testAddPassword1',
            'authority' => 1
        ];
        $this->enableCsrfToken();
        $this->post([
            'prefix' => 'management',
            'controller' => 'Account',
            'action' => 'edit',
            'id' => 1
        ], $data);
        $query = $accounts->find()
            ->select(['login_id', 'password', 'authority'])
            ->where([
                'id' => $data['id']
            ]);
        $this->assertEquals(1, $query->count());
        $this->assertEquals('testEdit123', $query->first()->login_id);
        $newCount = $accounts->find('all')->count();
        $this->assertEquals($oldCount, $newCount);
    }
}
