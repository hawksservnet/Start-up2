<?php
/**
 * Account Controller
 * @author: Huynh
 **/

namespace App\Controller\Management;

use App\Utilities\AuthDriver;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class AccountController extends ManagementController
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
    }
    /**
     * Index page /management/account
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function index()
    {
        $session = $this->request->session();
        $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;
        $loginAuth = ($session->check('LOGIN.AUTH'))?$session->read('LOGIN.AUTH'):'';
        $this->clearSearch('search006_01');
        $accountTable = TableRegistry::get('Acounts');
        /* If there is request, update session */
        if ($this->request->is('post') && !empty($this->request->data)) {
            /* if search request */
            $data = $this->request->data;
            if ($data['type'] == '1') {
                $session->write('search006_01.login_id', !empty($data['search_login_id'])?$data['search_login_id']:'');
                $session->write('search006_01.acount_name', !empty($data['search_acount_name'])?$data['search_acount_name']:'');
                if ($loginAuth != 4) {
                    $session->write('search006_01.authority', $data['search_authority']);
                }

                $session->write('search006_01.orderby', null);
                $session->write('search006_01.page', null);
            }
            if ($data['type'] == '2' && !empty($this->request->data['delete_id'])) {
                $deleteAccount = $accountTable->find()
                    ->where(['id' => $this->request->data['delete_id']]);
                if ($deleteAccount->count() > 0) {
                    $now = Time::now();
                    $deleteAccount = $deleteAccount->first();
                    $deleteAccount->avail_flg = 0;
                    $deleteAccount->modified_id = $loginId;
                    $deleteAccount->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');

                    if ($deleteAccount->authority != 3) {
                        if ($accountTable->save($deleteAccount)) {
                            $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_029'));
                        } else {
                            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_032'));
                        }
                    } else {
                        $conciergeTable = TableRegistry::get('Concierges');
                        $deleteConcierg = $conciergeTable->find()
                            ->contain([
                                'ConciergeInformations',
                            ])
                            ->where(['Concierges.account_id' => $deleteAccount->id]);
                        if ($deleteConcierg->count() > 0) {
                            $deleteConcierg = $deleteConcierg->first();
                            $deleteConcierg->avail_flg = 0;
                            $deleteConcierg->modified_id = $loginId;
                            $deleteConcierg->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                            $ciTable = TableRegistry::get('ConciergeInformations');
                            $deleteTransaction = $conciergeTable->getConnection()->transactional(function () use (
                                $conciergeTable,
                                $deleteConcierg,
                                $ciTable,
                                $accountTable,
                                $deleteAccount,
                                $loginId,
                                $now
                            ) {
                                foreach ($deleteConcierg->concierge_informations as $ci) {
                                    $ci->avail_flg = 0;
                                    $ci->modified_id = $loginId;
                                    $ci->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                                    if (!$ciTable->save($ci, ['atomic' => false])) {
                                        $conciergeTable->getConnection()->rollback();

                                        return false;
                                    }
                                }
                                if (!$accountTable->save($deleteAccount, ['atomic' => false])) {
                                    $conciergeTable->getConnection()->rollback();

                                    return false;
                                }
                                if (!$conciergeTable->save($deleteConcierg, ['atomic' => false])) {
                                    $conciergeTable->getConnection()->rollback();

                                    return false;
                                }

                                return true;
                            });
                            if ($deleteTransaction) {
                                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_029'));
                            } else {
                                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_032'));
                            }
                        } else {
                            if ($accountTable->save($deleteAccount)) {
                                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_029'));
                            } else {
                                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_032'));
                            }
                        }
                    }
                }
            }
        } else {
            if (empty($this->request->query) &&
                (!empty($session->read('search006_01.orderby')) || !empty($session->read('search006_01.page')))) {
                $queryRedirect = ['action' => 'index'];
                $queryRedirect['page'] = !empty($session->read('search006_01.page')) ? $session->read('search006_01.page') : 1;
                if (!empty($session->read('search006_01.orderby'))) {
                    $order = explode(' ', $session->read('search006_01.orderby'));
                    $queryRedirect['sort'] = $order[0];
                    if (!empty($order[1])) {
                        $queryRedirect['direction'] = $order[1];
                    }
                }

                return $this->redirect($queryRedirect);
            }
        }
        if (!empty($this->request->query['sort'])) {
            /* validation */
            if (!in_array($this->request->query['sort'], ['id', 'Acounts.id', 'login_id', 'Acounts.login_id', 'account_name', 'Acounts.account_name', 'authority', 'Acounts.authority'])) {
                $this->request->query['sort'] = 'id';
            }
            if (strtolower($this->request->query['direction']) != "asc" && strtolower($this->request->query['direction']) != "desc") {
                $this->request->query['direction'] = 'asc';
            }
            $session->write('search006_01.orderby', $this->request->query['sort'] . ' ' . $this->request->query['direction']);
        }
        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search006_01.page', $this->request->query['page']);
        } else {
            $session->write('search006_01.page', 1);
        }
        $searchLoginId = !empty($session->read('search006_01.login_id')) ? $session->read('search006_01.login_id') : '';
        $searchAccountName = !empty($session->read('search006_01.acount_name')) ? $session->read('search006_01.acount_name') : '';
        $searchAuthority = !is_null($session->read('search006_01.authority')) ? $session->read('search006_01.authority') : -1;
        $orderby = !empty($session->read('search006_01.orderby')) ? $session->read('search006_01.orderby') : 'id asc';
        $page = !empty($session->read('search006_01.page')) ? $session->read('search006_01.page') : 1;

        $conditions = ['Acounts.avail_flg' => 1];
        if (!empty($searchLoginId)) {
            $conditions['login_id LIKE'] = "%" . $searchLoginId . "%";
        }
        if (!empty($searchAccountName)) {
            $conditions['account_name LIKE'] = "%" . $searchAccountName . "%";
        }
        if ($loginAuth == 4) {
            $conditions['authority'] = 3;
        } elseif ($searchAuthority >= 0) {
            $conditions["authority"] = $searchAuthority;
        }
        $pagingConfig = [
            'page' => $page,
            'limit' => Configure::read('list_max_row'),
            'orderby' => $orderby
        ];
        $accounts = $accountTable->find()
            ->where($conditions);
        if ($accounts->count() < 1 &&
            (!empty($searchLoginId) || !empty($searchAccountName) || !empty($searchAuthority)) &&
            !empty($this->request->data['type']) &&
            $this->request->data['type'] == 1) {
            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_035'));
        }
        $order = explode(' ', $orderby);
        $pagingConfig['sort'] = $order[0];
        if (!empty($order[1])) {
            $pagingConfig['direction'] = $order[1];
        }
        try {
            $accounts = $this->Paginator->paginate($accounts, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'index',
                'page' => 1,
                'sort' => $pagingConfig['sort'],
                'direction' => !empty($pagingConfig['direction']) ? $pagingConfig['direction'] : ''
            ]);
        }
        $this->set('accounts', $accounts);
        $this->set(compact(['pagingConfig', 'searchLoginId', 'searchAccountName', 'searchAuthority']));
    }
    /**
     * edit page /management/edit
     * @param int|null $id Identify of account
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function edit($id = null)
    {
        $session = $this->request->session();
        $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;

        if (!empty($id) && !preg_match('/^\d+$/', $id)) {
            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_034'));

            return $this->redirect(['action' => 'index']);
        }
        $accountTable = TableRegistry::get('Acounts');
        if ($this->request->is('post') && !empty($this->request->data)) {
            $account = null;
            $now = Time::now();
            $data = $this->request->data;
            if (!empty($data['id'])) {
                $account = $accountTable->find()
                    ->where([
                        'avail_flg' => 1,
                        'id' => $data['id']
                    ]);
                if ($account->count() > 0) {
                    $account = $account->first();
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_031':'MSG_030')));

                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $account = $accountTable->newEntity();
                $account->created_id = $loginId;
                $account->created_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
            }
            $account->login_id = $data['login_id'];
            if (!empty($data['password'])) {
                $account->password = (new AuthDriver)->hashPassword($data['password']);
            }
            $account->account_name = $data['account_name'];
            $account->authority = $data['authority'];
            $account->modified_id = $loginId;
            $account->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
            $accountValidator = $accountTable->validator('default');
            if (empty($data['password']) && !empty($account->password)) {
                $accountValidator->allowEmpty('password');
            }
            $errors = $accountValidator->errors($data);
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    foreach ($error as $e) {
                        $this->Flash->error($e);
                    }
                }
                $this->set('id', !empty($id) ? $id:0);
                $this->set('account_name', $data['account_name']);
                $this->set('login_id', $data['login_id']);
                $this->set('password', $data['password']);
                $this->set('authority', $data['authority']);
            } else {
                if ($accountTable->save($account)) {
                    $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_028':'MSG_027')));

                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_031':'MSG_030')));

                    return $this->redirect(['action' => 'index']);
                }
            }
        } else {
            if (!empty($id)) {
                $accounts = $accountTable->find()
                    ->where([
                        'avail_flg' => 1,
                        'id' => $id
                    ]);
                if ($accounts->count() > 0) {
                    $account = $accounts->first();
                    $this->set('id', $id);
                    $this->set('account_name', $account->account_name);
                    $this->set('login_id', $account->login_id);
                    $this->set('authority', $account->authority);
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_034'));

                    return $this->redirect(['action' => 'index']);
                }
            }
        }
    }
    /**
     * Ajax function
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function ajax()
    {
        $this->viewBuilder()->layout('ajax');
        $response = ['status' => true];
        if (!empty($this->request->data['type'])) {
            if ($this->request->data['type'] == "loginIdExist") {
                $conditions = [
                    'login_id' => $this->request->data['login_id'],
                    'avail_flg' => 1,
                ];
                if (!empty($this->request->data['id'])) {
                    $conditions['id <>'] = $this->request->data['id'];
                }
                if ($this->userExists($conditions)) {
                    $response = ['status' => false];
                }
            }
        }
        $this->set(compact('response'));
        $this->set('_serialize', 'response');
    }
}
