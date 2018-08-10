<?php
/**
 * Management Controller
 * @author: Huynh
 **/

namespace App\Controller\Management;

use App\Controller\AppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ManagementController extends AppController
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
     * @return \Cake\Network\Response|void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $session = $this->request->session();
        if ($session->check('LOGIN')) {
            $this->set('loginInfo', $session->read('LOGIN'));

            $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;
            $loginAuth = ($session->check('LOGIN.AUTH'))?$session->read('LOGIN.AUTH'):'';
            /* 初期処理 */
            if ($this->request->params['action'] != 'ajax') {
                if (!$loginId || $loginAuth === '') {
                    /* still cannot Flash to Fuel */
                    // $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));

                    return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
                } else {
                    $screenKey = $this->request->params['controller'] . '|' . $this->request->params['action'];
                    $screenAuth = Configure::read('SCREEN_AUTHENTICATION')[$screenKey];
                    if (isset($screenAuth) && !in_array($loginAuth, $screenAuth)) {
                        return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/users');
                    }
                }
            }
        } else {
            if ($this->request->params['action'] != 'ajax') {
                /* still cannot Flash to Fuel */
                // $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));

                return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
            }
        }
        $this->viewBuilder()->layout('management');
    }
    /**
     * Ajax function to keep management session
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function ajax()
    {
        header('Access-Control-Allow-Origin: ' . substr(Configure::read('FUEL_ADMIN_URL'), 0, -1), true);
        header('Access-Control-Allow-Credentials: true');
        $this->viewBuilder()->layout('ajax');
        $response = ['source' => 'cake.management'];
        echo json_encode($response);
        exit;
    }
    /**
     * @param array $conditions Condition for checking user
     * @return bool
     */
    protected function userExists($conditions)
    {
        $user = TableRegistry::get('Acounts');
        $data = $user->find()
            ->select(['id'])
            ->where($conditions);

        if ($data->count() > 0) {
            return true; // EXISTS
        } else {
            return false; // NOT EXISTS
        }
    }

    /**
     * Ajax function to keep management session
     * @author: ThoVo
     * @param array $skipss Condition for checking user
     * @return \Cake\Network\Response|void : boolean
     */
    protected function clearSearch($skipss)
    {
        $session = $this->request->session();
        $array_ss = ['search006_01', 'search011', 'search015', 'search021', 'search112', 'search111'];
        foreach ($array_ss as $value) {
            if (!empty($skipss) && $skipss != $value) {
                $session->write($value, null);
            }
        }
    }
}
