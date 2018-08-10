<?php
/**
 * Concierge Controller
 * @author: Huynh
 **/

namespace App\Controller\Management;

use App\Utilities\AuthDriver;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\I18n\Time;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class ConciergeController extends ManagementController
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
     * Index page /management/concierge/list
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function index()
    {
        $session = $this->request->session();
        $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;
        $this->clearSearch('search015');
        $conciergeTable = TableRegistry::get('Concierges');
        /* If there is request, update session */
        if ($this->request->is('post') && !empty($this->request->data)) {
            /* if search request */
            $data = $this->request->data;
            if ($data['type'] == '1') {
                $session->write('search015.name', !empty($data['search_name'])?$data['search_name']:'');

                $session->write('search015.orderby', null);
                $session->write('search015.page', null);
            }
            if ($data['type'] == '2' && !empty($data['delete_id'])) {
                $deleteConcierg = $conciergeTable->find()
                    ->contain([
                        'ConciergeInformations',
                        'Acounts'
                    ])
                    ->where(['Concierges.id' => $data['delete_id']]);
                if ($deleteConcierg->count() > 0) {
                    $now = Time::now();
                    $deleteConcierg = $deleteConcierg->first();
                    $deleteConcierg->avail_flg = 0;
                    $deleteConcierg->modified_id = $loginId;
                    $deleteConcierg->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                    $ciTable = TableRegistry::get('ConciergeInformations');
                    $accountTable = TableRegistry::get('Acounts');
                    $deleteTransaction = $conciergeTable->getConnection()->transactional(function () use (
                        $conciergeTable,
                        $deleteConcierg,
                        $ciTable,
                        $accountTable,
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
                        $account = $deleteConcierg->acount;
                        $account->avail_flg = 0;
                        $account->modified_id = $loginId;
                        $account->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                        if (!$accountTable->save($account, ['atomic' => false])) {
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
                }
            }
        } else {
            if (empty($this->request->query) &&
                (!empty($session->read('search015.orderby')) || !empty($session->read('search015.page')))) {
                $queryRedirect = ['action' => 'index'];
                $queryRedirect['page'] = !empty($session->read('search015.page')) ? $session->read('search015.page') : 1;
                if (!empty($session->read('search015.orderby'))) {
                    $order = explode(' ', $session->read('search015.orderby'));
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
            if (!in_array($this->request->query['sort'], ['id', 'Acounts.id', 'name', 'Concierges.name'])) {
                $this->request->query['sort'] = 'id';
            }
            if (strtolower($this->request->query['direction']) != "asc" && strtolower($this->request->query['direction']) != "desc") {
                $this->request->query['direction'] = 'asc';
            }
            $session->write('search015.orderby', $this->request->query['sort'] . ' ' . $this->request->query['direction']);
        }
        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search015.page', $this->request->query['page']);
        } else {
            $session->write('search015.page', 1);
        }
        $searchName = !empty($session->read('search015.name')) ? $session->read('search015.name') : '';
        $orderby = !empty($session->read('search015.orderby')) ? $session->read('search015.orderby') : 'id asc';
        $page = !empty($session->read('search015.page')) ? $session->read('search015.page') : 1;

        $conditions = ['Concierges.avail_flg' => 1];
        if (!empty($searchName)) {
            $conditions['name LIKE'] = "%" . $searchName . "%";
        }
        $pagingConfig = [
            'page' => $page,
            'limit' => Configure::read('list_max_row'),
            'orderby' => $orderby
        ];
        $concierges = $conciergeTable->find()
            ->contain('ConciergeInformations')
            ->where($conditions);
        if ($concierges->count() < 1 && !empty($searchName) && !empty($this->request->data['type']) && $this->request->data['type'] == 1) {
            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_035'));
        }
        $order = explode(' ', $orderby);
        $pagingConfig['sort'] = $order[0];
        if (!empty($order[1])) {
            $pagingConfig['direction'] = $order[1];
        }
        try {
            $concierges = $this->Paginator->paginate($concierges, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'index',
                'page' => 1,
                'sort' => $pagingConfig['sort'],
                'direction' => !empty($pagingConfig['direction']) ? $pagingConfig['direction'] : ''
            ]);
        }
        $this->set('concierges', $concierges);
        $this->set(compact(['pagingConfig', 'searchName']));
    }
    /**
     * edit page /management/concierge/edit
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
        $conciergeTable = TableRegistry::get('Concierges');
        $accountTable = TableRegistry::get('Acounts');
        if ($this->request->is('post') && !empty($this->request->data)) {
            $concierge = null;
            $account = null;
            $now = Time::now();
            $data = $this->request->data;
            if (!empty($data['id'])) {
                $concierge = $conciergeTable->find()
                    ->contain([
                        'ConciergeInformations' => [
                            'sort' => ['ConciergeInformations.sort_no' => 'ASC'],
                            'queryBuilder' => function ($q) {
                                return $q->where(['ConciergeInformations.avail_flg' => 1]);
                            }
                        ],
                        'Acounts' => [
                            'queryBuilder' => function ($q) {
                                return $q->where(['Acounts.avail_flg' => 1]);
                            }
                        ]
                    ])
                    ->where([
                        'Concierges.avail_flg' => 1,
                        'Concierges.id' => $data['id']
                    ]);
                if ($concierge->count() > 0) {
                    $concierge = $concierge->first();
                    $account = $concierge->acount;
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_031':'MSG_030')));

                    return $this->redirect(['action' => 'index']);
                }
            } else {
                $account = $accountTable->newEntity();
                $account->authority = 3;
                $account->created_id = $loginId;
                $account->created_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                $concierge = $conciergeTable->newEntity();
                $concierge->created_id = $loginId;
                $concierge->created_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
            }
            $account->login_id = $data['login_id'];
            if (!empty($data['password'])) {
                $account->password = (new AuthDriver)->hashPassword($data['password']);
            }
            $account->account_name = $data['name'];
            //	2018/2/9変更
            //$account->authority = 3;
            $account->modified_id = $loginId;
            $account->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
            $ciTable = TableRegistry::get('ConciergeInformations');
            $accountValidator = $accountTable->validator('default');
            $conciergeValidator = $conciergeTable->validator('default');
            if (empty($data['password']) && !empty($account->password)) {
                $accountValidator->allowEmpty('password');
            }
            $errors = array_merge($accountValidator->errors($data), $conciergeValidator->errors($data));
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    foreach ($error as $e) {
                        $this->Flash->error($e);
                    }
                }

                return $this->redirect($this->referer());
            } else {
                $updateTransaction = $conciergeTable->getConnection()->transactional(function () use (
                    $conciergeTable,
                    $concierge,
                    $account,
                    $data,
                    $accountTable,
                    $ciTable,
                    $loginId,
                    $now
                ) {
                    if (!$accountTable->save($account, ['atomic' => false])) {
                        $conciergeTable->getConnection()->rollback();

                        return false;
                    }
                    $concierge->account_id = $account->id;
                    $concierge->name = $data['name'];
                    $concierge->name_e = $data['name_e'];
                    $concierge->career = !empty($data['career']) ? $data['career'] :'';
                    if (empty($data['id'])) {
                        $concierge->image_name = '';
                    }
                    $concierge->mailaddress = $data['mailaddress'];
                    $concierge->sort_no = $data['sort_no'];
                    $concierge->modified_id = $loginId;
                    $concierge->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                    if (!$conciergeTable->save($concierge, ['atomic' => false])) {
                        $conciergeTable->getConnection()->rollback();

                        return false;
                    }
                    if (!empty($data['image_name']) && !empty($data['image_name']['name'])) {
                        $filePath = WWW_ROOT . Configure::read('thumbnail_img_path');
                        /* delete if image exists */
                        if (!empty($concierge->image_name)) {
                            $file = new File($filePath . $concierge->image_name, false);
                            if ($file->exists()) {
                                $file->delete();
                            }
                        }
                        $newImage = $concierge->id .
                            /*date('ymdHis') .*/
                            strrchr($data['image_name']['name'], '.');
                        move_uploaded_file($data['image_name']['tmp_name'], $filePath . $newImage);
                        $concierge->image_name = $newImage;
                        if (!$conciergeTable->save($concierge, ['atomic' => false])) {
                            $conciergeTable->getConnection()->rollback();

                            return false;
                        }
                    }
                    $deleteList = explode(',', $data['delete_list']);
                    $sortNo = 0;
                    if (!empty($concierge->concierge_informations)) {
                        foreach ($concierge->concierge_informations as $ci) {
                            if (in_array($ci->id, $deleteList)) {
                                if (!$ciTable->delete($ci, ['atomic' => false])) {
                                    $conciergeTable->getConnection()->rollback();

                                    return false;
                                }
                            } else {
                                $sortNo++;
                                $ci->sort_no = $sortNo;
                                if (!$ciTable->save($ci, ['atomic' => false])) {
                                    $conciergeTable->getConnection()->rollback();

                                    return false;
                                }
                            }
                        }
                    }
                    foreach ($data as $key => $val) {
                        $sortNo++;
                        if ((strpos($key, 'add_language_value_0_') === 0 || strpos($key, 'add_keyword_value_0_') === 0) && !empty($val)) {
                            $newCi = $ciTable->newEntity();
                            $newCi->concierge_id = $concierge->id;
                            $newCi->info_type = (strpos($key, 'add_keyword_value_0_') === 0) ? 1 : 2;
                            $newCi->sort_no = $sortNo;
                            $newCi->info_text = $val;
                            $newCi->created_id = $loginId;
                            $newCi->created_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                            $newCi->modified_id = $loginId;
                            $newCi->modified_date = $now->i18nFormat('YYYY-MM-dd HH:mm:ss');
                            if (!$ciTable->save($newCi, ['atomic' => false])) {
                                $conciergeTable->getConnection()->rollback();

                                return false;
                            }
                        }
                    }

                    return true;
                });
                if ($updateTransaction) {
                    $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_028':'MSG_027')));

                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.' . (!empty($id) ? 'MSG_031':'MSG_030')));

                    return $this->redirect(['action' => 'index']);
                }
            }
        } else {
            if (!empty($id)) {
                $concierge = $conciergeTable->find()
                    ->contain([
                        'ConciergeInformations' => [
                            'sort' => ['ConciergeInformations.sort_no' => 'ASC'],
                            'queryBuilder' => function ($q) {
                                return $q->where(['ConciergeInformations.avail_flg' => 1]);
                            }
                        ],
                        'Acounts' => [
                            'queryBuilder' => function ($q) {
                                return $q->where(['Acounts.avail_flg' => 1]);
                            }
                        ]
                    ])
                    ->where([
                        'Concierges.avail_flg' => 1,
                        'Concierges.id' => $id
                    ]);
                if ($concierge->count() > 0) {
                    $concierge = $concierge->first();
                    $concierge->acount->password = '';
                    $this->set('id', $id);
                    $this->set('accountId', $concierge->account_id);
                    $this->set(compact('concierge'));
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
