<?php
/**
 * Concierge Controller
 * @author: Huynh
 **/

namespace App\Controller\Concierge;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class ConciergeController extends AppController
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
        $this->viewBuilder()->layout('concierge');
    }

    /**
     * Check auth of user login
     * @param int $id null
     * @author tuanvu
     * @return \Cake\Network\Response|void : boolean
     */
    protected function getConcierges($id = null)
    {
        if ($id != null) {
            $conditions = 'Concierges.id =' . $id;
        } else {
            $conditions = "";
        }
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
                $conditions,
                'Concierges.avail_flg' => 1
            ])->order(['sort_no' => 'ASC', 'id' => 'ASC'])->toArray();

        return $mconcierges;
    }
}
