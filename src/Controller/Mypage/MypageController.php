<?php
/**
 * Mypage Controller
 **/

namespace App\Controller\Mypage;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class MypageController extends AppController
{
    /**
     * Initial function
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Paginator');
    }
    /**
     * before filter function
     * @param Event $event Event object
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $session = $this->request->session();
        if ($session->check('MYPAGE')) {
            $this->set('loginInfo', $session->read('MYPAGE'));
        }
        $this->viewBuilder()->layout('mypage');
    }
    /**
     * Ajax function to keep user session
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function ajax()
    {
        header('Access-Control-Allow-Origin: ' . substr(Configure::read('FUEL_ADMIN_URL'), 0, -1), true);
        header('Access-Control-Allow-Credentials: true');
        $this->viewBuilder()->layout('ajax');
        $response = ['source' => 'cake.mypage'];
        echo json_encode($response);
        exit;
    }
    /**
     * Get User Email Address From Login ID
     * @author huynh
     * @param int $id of user
     * @return \string Email Adress
     */
    protected function getUserEmail($id)
    {
        $userTable = TableRegistry::get('Users');
        $user = $userTable->find()
            ->where(['id' => $id])
            ->first();
        if (!empty($user->id)) {
            return $user->email;
        }

        return '';
    }

    /**
     * Ajax function to keep management session
     * @author: ThoVo
     * @param array $skipss of session search
     * @return \Cake\Network\Response|void : boolean
     */
    protected function clearSearch($skipss)
    {
        $session = $this->request->session();
        $array_ss = ['search112', 'search111'];
        foreach ($array_ss as $value) {
            if (!empty($skipss) && $skipss != $value) {
                $session->write($value, null);
            }
        }
    }
}
