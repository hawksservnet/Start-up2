<?php
/**
 * Mypage Controller
 **/

namespace App\Controller\Mypage;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class ReserveController extends MypageController
{
    /**
     * Index page /mypage/reserve/list
     * @author: Dat
     * @return \Cake\Network\Response|void : boolean
     */
    public function index()
    {
        $ReservesTable = TableRegistry::get('Reserves');
        $ShiftsTable = TableRegistry::get('ShiftWorks');
        $session = $this->request->session();
        $this->clearSearch('search111');
        $loginId = ($session->check('MYPAGE.ID')) ? $session->read('MYPAGE.ID') : 0;
        $loginName = ($session->check('MYPAGE.NAME')) ? $session->read('MYPAGE.NAME') : '';

        if (!$loginId || $loginName === '') {
            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));

            // return $this->redirect($this->Url->build(['controller' => 'reserve', 'action' => 'login']));
        }
        $data = $this->request->data;

        if ($this->request->is('post') && isset($this->request->data)) {
            $updateTransaction = $ReservesTable->getConnection()->transactional(function () use ($data, $ReservesTable, $ShiftsTable) {
                $now = date('Y-m-d H:i:s');
                $Qupdate = $ReservesTable->get($data['hid_rid']);
                $Qupdate->reserve_status = 9;
                $Qupdate->cancel_status = 1;
                $Qupdate->modified_id = 0;
                $Qupdate->modified_date = $now;
                $Qupdate->cancel_date = $now;
                if (!$ReservesTable->save($Qupdate, ['atomic' => false])) {
                    $ReservesTable->getConnection()->rollback();

                    return false;
                } else {
                    $QupdateS = $ShiftsTable->get($data['hid_sid']);
                    $QupdateS->status = 0;
                    $QupdateS->modified_id = 0;
                    $QupdateS->modified_date = $now;
                    if (!$ShiftsTable->save($QupdateS, ['atomic' => false])) {
                        $ReservesTable->getConnection()->rollback();

                        return false;
                    } else {
                        $reservesTable = TableRegistry::get('Reserves');
                        $counselNotesTable = TableRegistry::get('CounselNotes');
                        $shiftWorksTable = TableRegistry::get('ShiftWorks');

                        $queryName = $reservesTable->find()
                            ->select(['Users.id', 'Users.email', 'Users.name_last', 'Users.name_first', 'Reserves.reserve_status', 'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_end', 'Concierges.name', 'CounselNotes.question5_1text'])
                            ->join(['ShiftWorks' => ['table' => 'shift_works',
                                'type' => 'LEFT',
                                'conditions' => ['Reserves.work_date = ShiftWorks.work_date', 'Reserves.work_time_start = ShiftWorks.work_time_start', 'Reserves.concierge_id = ShiftWorks.concierge_id']],
                                'Concierges' => ['table' => 'concierges',
                                    'type' => 'LEFT',
                                    'conditions' => ['Concierges.avail_flg = 1', 'Concierges.id = ShiftWorks.concierge_id']],
                                'CounselNotes' => ['table' => 'counsel_notes',
                                    'type' => 'LEFT',
                                    'conditions' => ['Reserves.id = CounselNotes.reserve_id']],
                                'Users' => ['table' => 'users',
                                    'type' => 'LEFT',
                                    'conditions' => ['Reserves.user_id = Users.id']]])
                            ->where(['Reserves.id' => $data['hid_rid'], 'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')])->first();
                        $work_time_start = substr($queryName->work_time_start, 0, 2) . ':' . substr($queryName->work_time_start, 2, 2);
                        $work_time_end = substr($queryName->work_time_end, 0, 2) . ':' . substr($queryName->work_time_end, 2, 2);

                        $mailinfos['user_name'] = $queryName->Users['name_last'] . ' ' . $queryName->Users['name_first'];
                        $mailinfos['user_id'] = $queryName->Users['id'];
                        $mailinfos['email'] = $queryName->Users['email'];
                        $mailinfos['work_date'] = date("Y-m-d", strtotime($queryName->work_date));
                        $mailinfos['work_time_start'] = $work_time_start;
                        $mailinfos['work_time_end'] = $work_time_end;
                        $mailinfos['pre_concierge_name'] = $queryName->Concierges['name'];
                        if ($mailinfos['email']) {
                            $this->sendEmail(
                                Configure::read('from_mailaddress'),
                                $mailinfos['email'],
                                '【Startup Hub Tokyo】コンシェルジュ相談のキャンセルをお受けいたしました。',
                                $mailinfos,
                                'email_nursery_reserves_cancel',
                                null,
                                Configure::read('admin_mailaddress')
                            );
                        }
                    }
                }

                return true;
            });

            if ($updateTransaction) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }
        }

        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search111.page', $this->request->query['page']);
        } else {
            if (!empty($session->read('search111.page'))) {
                $queryRedirect['page'] = !empty($session->read('search111.page')) ? $session->read('search111.page') : 1;

                return $this->redirect($queryRedirect);
            }
            $session->write('search111.page', 1);
        }
        $page = !empty($session->read('search111.page')) ? $session->read('search111.page') : 1;

        $ShiftsTable = TableRegistry::get('ShiftWorks');
        $queryReserves = $ShiftsTable->find()->select(['sid' => 'ShiftWorks.id', 'work_date' => 'ShiftWorks.work_date',
            'work_time_start' => 'ShiftWorks.work_time_start', 'rid' => 'Reserves.id',
            'user_id' => 'Reserves.user_id', 'r_status' => 'Reserves.reserve_status', 'c_status' => 'Reserves.cancel_status',
            'cid' => 'Concierges.id', 'cname' => 'Concierges.name'
        ])
            ->join(['Reserves' => ['table' => 'reserves', 'type' => 'LEFT',
                'conditions' => ['ShiftWorks.concierge_id = Reserves.concierge_id', 'ShiftWorks.work_date = Reserves.work_date',
                    'ShiftWorks.work_time_start = Reserves.work_time_start']]])
            ->join(['Concierges' => ['table' => 'concierges', 'type' => 'LEFT',
                'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
            ->where(['Reserves.user_id' => $loginId, 'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')])
            ->order(['ShiftWorks.work_date' => 'DESC', 'ShiftWorks.work_time_start' => 'DESC', 'Reserves.reserve_status' => 'ASC', 'Reserves.user_id' => 'ASC', 'Reserves.id' => 'DESC']);

        $pagingConfig = [
            'page' => $page,
            'limit' => Configure::read('list_max_row'),
        ];

        try {
            $reserves = $this->Paginator->paginate($queryReserves, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'index',
                'page' => 1,
            ]);
        }

        $this->set(compact(['pagingConfig', 'reserves']));
    }

    /**
     * Form method
     * @author: ThuanLe
     * @return \Cake\Http\Response|void
     */
    public function form()
    {
        $sid = (int)$this->request->getQuery('id');
        $his = (int)$this->request->getQuery('his');
        $rs = (int)$this->request->getQuery('rs');
        $dt = $this->request->getQuery('dt');
        $cn = !empty($this->request->getQuery('cn')) ? (int)$this->request->getQuery('cn') : 0;

        $this->set(['sid' => $sid, 'his' => $his, 'rs' => $rs, 'dt' => $dt, 'reserves' => $cn]);
        $session = $this->request->session();
        $mypage_Id = ($session->check('MYPAGE.ID')) ? $session->read('MYPAGE.ID') : 0;
        $mypage_Name = ($session->check('MYPAGE.NAME')) ? $session->read('MYPAGE.NAME') : '';

        if (!$mypage_Id || $mypage_Name === '') {
            //$this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));

            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'users/login');
        }

        if (!$sid) {
            return $this->redirect(['controller' => 'Reserve', 'action' => 'index']);
        }

        $this->set(['isSupperUser' => (Configure::read('superuser_id') == $this->getUserEmail($mypage_Id))]);

        $reservesTable = TableRegistry::get('Reserves');
        $counselNotesTable = TableRegistry::get('CounselNotes');
        $shiftWorksTable = TableRegistry::get('ShiftWorks');

        if ($this->request->is('post')) {
            $data = $this->request->data;

            if (($data['step'] == 2) || ($data['step'] == 3)) {
                //errcheck
                //予約日時超過チェック
                if ($data['step'] == 2) {
                    $data_load = [];
                    $data_load['work_date'] = $data['work_date'];
                    $data_load['work_time_start'] = $data['work_time_start'];
                }
                if ($data['step'] == 3) {
                    $data_save = $session->read('data_save.data');
                    $data_load = [];
                    $data_load['work_date'] = $data_save['work_date'];
                    $data_load['work_time_start'] = $data_save['work_time_start'];
                }

                $workDateTine = strtotime($data_load['work_date'] . ' ' . substr($data_load['work_time_start'], 0, 2) . ':' . substr($data_load['work_time_start'], 2, 2));
                if (time() > $workDateTine) {
                    /*
                     * Notice: Undefined variable: reserve_flg
                     **/
                    $data_load['reserve_flg'] = isset($reserve_flg) ? $reserve_flg : null;
                    //ヘッダ情報
                    if ($data['step'] == 2) {
                        $data_load['work_time_end'] = $data['work_time_end'];
                        $data_load['concierge_id'] = $data['concierge_id'];
                        $data_load['concierge_name'] = $data['concierge_name'];
                        $data_load['user_id'] = $data['user_id'];
                        $data_load['user_name'] = $data['user_name'];
                        $data_load['user_type'] = $data['user_type'];
                    }
                    if ($data['step'] == 3) {
                        $data_load['work_time_end'] = $data_save['work_time_end'];
                        $data_load['concierge_id'] = $data_save['concierge_id'];
                        $data_load['concierge_name'] = $data_save['concierge_name'];
                        $data_load['user_id'] = $data_save['user_id'];
                        $data_load['user_name'] = $data_save['user_name'];
                        $data_load['user_type'] = $data_save['user_type'];
                    }
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_054'));

                    $this->set(['data_load' => $data_load]);
                    $data['step'] = null; //この後の処理を回避
                    $this->render('form');
                }
            }

            if ($data['step'] == 1) {
                if ($session->check('data_save') && $session->read('data_save.time') >= time()) {
                    $data_load = $session->read('data_save.data');
                    $this->set(['data_load' => $data_load]);
                    if ($this->checkShiftWorkBooked($sid) && empty($rs)) {
                        $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_064'));
                        $this->render('form_thanks');

                        return;
                    }
                    $this->render('form');
                } else {
                    return $this->redirect(['controller' => 'Reserve', 'action' => 'form', '?' => ['id' => $sid, 'his' => $his, 'rs' => $rs]]);
                }
            } elseif ($data['step'] == 2) {
                $data_save = [];
                $data_save['reserves'] = $data['reserves'];
                $data_save['work_date'] = $data['work_date'];
                $data_save['work_time_start'] = $data['work_time_start'];
                $data_save['work_time_end'] = $data['work_time_end'];
                $data_save['concierge_id'] = $data['concierge_id'];
                $data_save['concierge_name'] = $data['concierge_name'];
                $data_save['user_id'] = $data['user_id'];
                $data_save['user_name'] = $data['user_name'];
                $data_save['user_type'] = $data['user_type'];
                $data_save['question8_1'] = isset($data['question8_1']) ? $data['question8_1'] : '';
                $data_save['question8_1text'] = '';
                $data_save['question8_2'] = '';
                $data_save['question8_2text'] = '';
                $data_save['question8_3'] = '';
                $data_save['question8_3text'] = '';
                $data_save['question8_4'] = '';
                $data_save['question8_4text'] = '';

                if ($data_save['question8_1'] == '3') {
                    $data_save['question8_2'] = (isset($data['question8_2']) && is_array($data['question8_2'])) ? implode(',', $data['question8_2']) : '';
                    if (strpos(',' . $data_save['question8_2'] . ',', ',4,') !== false) {
                        $data_save['question8_2text'] = $data['question8_2text'];
                    }
                } elseif ($data_save['question8_1'] == '4') {
                    $data_save['question8_3'] = (isset($data['question8_3']) && is_array($data['question8_3'])) ? implode(',', $data['question8_3']) : '';
                    if (strpos(',' . $data_save['question8_3'] . ',', ',4,') !== false) {
                        $data_save['question8_3text'] = $data['question8_3text'];
                    }
                } elseif ($data_save['question8_1'] == '5') {
                    $data_save['question8_4'] = (isset($data['question8_4']) && is_array($data['question8_4'])) ? implode(',', $data['question8_4']) : '';
                    if (strpos(',' . $data_save['question8_4'] . ',', ',5,') !== false) {
                        $data_save['question8_4text'] = $data['question8_4text'];
                    }
                } elseif ($data_save['question8_1'] == '6') {
                    $data_save['question8_1text'] = $data['question8_1text'];
                }

                $data_save['question1_1'] = isset($data['question1_1']) ? $data['question1_1'] : '';
                $data_save['question2_1'] = isset($data['question2_1']) ? $data['question2_1'] : '';
                $data_save['question3_1'] = (isset($data['question3_1']) && is_array($data['question3_1'])) ? implode(',', $data['question3_1']) : '';
                $data_save['question3_1text'] = '';
                $data_save['question3_2'] = '';
                $data_save['question3_2text'] = '';
                if (strpos(',' . $data_save['question3_1'] . ',', ',9,') !== false) {
                    $data_save['question3_1text'] = $data['question3_1text'];
                }
                if (strpos(',' . $data_save['question3_1'] . ',', ',5,') !== false) {
                    $data_save['question3_2'] = (isset($data['question3_2']) && is_array($data['question3_2'])) ? implode(',', $data['question3_2']) : '';
                    if (strpos(',' . $data_save['question3_2'] . ',', ',5,') !== false) {
                        $data_save['question3_2text'] = $data['question3_2text'];
                    }
                }

                $data_save['question4_1'] = $data['question4_1'];
                $data_save['question6_1'] = (isset($data['question6_1']) && is_array($data['question6_1'])) ? implode(',', $data['question6_1']) : '';
                $data_save['question6_1text'] = '';
                if (strpos(',' . $data_save['question6_1'] . ',', ',14,') !== false) {
                    $data_save['question6_1text'] = $data['question6_1text'];
                }
                $data_save['question5_1text'] = $data['question5_1text'];
                $data_save['question7_1'] = $data['question7_1'];
                $data_save['question9_1text'] = $data['question9_1text'];
                $data_save['preentre_update'] = isset($data['preentre_update']) ? $data['preentre_update'] : 0;

                $session->write('data_save.time', strtotime('+10 minutes', time()));
                $session->write('data_save.data', $data_save);

                $this->set(['data_save' => $data_save]);
                if ($this->checkShiftWorkBooked($sid) && empty($rs)) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_064'));
                    $this->render('form_thanks');

                    return;
                }
                $this->render('form_confirm');
            } elseif ($data['step'] == 3) {
                if ($this->checkShiftWorkBooked($sid) && empty($rs)) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_064'));
                    $this->render('form_thanks');

                    return;
                }

                if ($session->check('data_save') && $session->read('data_save.time') >= time()) {
                    $data_save = $session->read('data_save.data');
                    $this->set('data_save', $data_save);

                    if ($rs > 0) {
                        $updateTransaction = $reservesTable->getConnection()->transactional(function () use ($data_save, $reservesTable, $shiftWorksTable, $counselNotesTable, $mypage_Id, $sid, $rs) {
                            try {
                                $entity_sw = $shiftWorksTable->get($sid);
                                $entity_sw->status = '1';
                                $entity_sw->modified_id = '0';
                                $entity_sw->modified_date = date('Y-m-d H:i:s');
                                if (!$shiftWorksTable->save($entity_sw, ['atomic' => false])) {
                                    $reservesTable->getConnection()->rollback();

                                    return false;
                                }
                            } catch (RecordNotFoundException $e) {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }

                            $entity_cn = $counselNotesTable->find('all')
                                ->where(['CounselNotes.reserve_id' => $rs])
                                ->first();
                            if (count($entity_cn) > 0) {
                                $entity_cn->question8_1 = $data_save['question8_1'];
                                $entity_cn->question8_1text = $data_save['question8_1text'];
                                $entity_cn->question8_2 = $data_save['question8_2'];
                                $entity_cn->question8_2text = $data_save['question8_2text'];
                                $entity_cn->question8_3 = $data_save['question8_3'];
                                $entity_cn->question8_3text = $data_save['question8_3text'];
                                $entity_cn->question8_4 = $data_save['question8_4'];
                                $entity_cn->question8_4text = $data_save['question8_4text'];
                                $entity_cn->question1_1 = $data_save['question1_1'];
                                $entity_cn->question2_1 = $data_save['question2_1'];
                                $entity_cn->question3_1 = $data_save['question3_1'];
                                $entity_cn->question3_1text = $data_save['question3_1text'];
                                $entity_cn->question3_2 = $data_save['question3_2'];
                                $entity_cn->question3_2text = $data_save['question3_2text'];
                                $entity_cn->question4_1 = $data_save['question4_1'];
                                $entity_cn->question6_1 = $data_save['question6_1'];
                                $entity_cn->question6_1text = $data_save['question6_1text'];
                                $entity_cn->question5_1text = $data_save['question5_1text'];
                                $entity_cn->question7_1 = $data_save['question7_1'];
                                $entity_cn->question9_1text = $data_save['question9_1text'];
                                $entity_cn->preentre_update = isset($data_save['preentre_update']) ? $data_save['preentre_update'] : 0;
                                $entity_cn->modified_id = $mypage_Id;
                                $entity_cn->modified_date = date('Y-m-d H:i:s');
                                if (!$counselNotesTable->save($entity_cn, ['atomic' => false])) {
                                    $reservesTable->getConnection()->rollback();

                                    return false;
                                }
                            } else {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }

                            return true;
                        });
                    } else {
                        $reserve_id = 0;
                        $updateTransaction = $reservesTable->getConnection()->transactional(function () use ($data_save, $reservesTable, $shiftWorksTable, $counselNotesTable, $mypage_Id, $sid, &$reserve_id) {
                            try {
                                $entity_sw = $shiftWorksTable->get($sid);
                                $entity_sw->status = '1';
                                $entity_sw->modified_id = '0';
                                $entity_sw->modified_date = date('Y-m-d H:i:s');
                                $entity_sw->tmp_reserve_id = '';
                                $entity_sw->tmp_reserve_date = date('Y-m-d H:i:s', strtotime('-11 minutes'));
                                if (!$shiftWorksTable->save($entity_sw, ['atomic' => false])) {
                                    $reservesTable->getConnection()->rollback();

                                    return false;
                                }
                            } catch (RecordNotFoundException $e) {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }

                            try {
                                $work_time_start = substr($data_save['work_time_start'], 0, 2) . ':' . substr($data_save['work_time_start'], 2, 2) . ':00';
                                $work_time_end = date('Hi', strtotime('+40 minutes', strtotime($work_time_start)));
                                $entity_rv = $reservesTable->newEntity();
                                $entity_rv->work_date = date('Y-m-d', strtotime($data_save['work_date']));
                                $entity_rv->work_time_start = $data_save['work_time_start'];
                                $entity_rv->work_time_end = $work_time_end;
                                $entity_rv->concierge_id = $data_save['concierge_id'];
                                $entity_rv->user_id = $mypage_Id;
                                $entity_rv->reserve_status = '1';
                                $entity_rv->cancel_status = '0';
                                $entity_rv->created_id = '0';
                                $entity_rv->modified_id = '0';
                                $entity_rv->modified_date = date('Y-m-d H:i:s');
                                if (!$reservesTable->save($entity_rv, ['atomic' => false])) {
                                    $reservesTable->getConnection()->rollback();

                                    return false;
                                }
                            } catch (\PDOException $e) {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }

                            try {
                                $reserve_id = $entity_rv->id;
                                $entity_cn = $counselNotesTable->newEntity();
                                $entity_cn->reserve_id = $reserve_id;
                                $entity_cn->work_time_start = $data_save['work_time_start'];
                                $entity_cn->work_time_end = $work_time_end;
                                $entity_cn->question8_1 = $data_save['question8_1'];
                                $entity_cn->question8_1text = $data_save['question8_1text'];
                                $entity_cn->question8_2 = $data_save['question8_2'];
                                $entity_cn->question8_2text = $data_save['question8_2text'];
                                $entity_cn->question8_3 = $data_save['question8_3'];
                                $entity_cn->question8_3text = $data_save['question8_3text'];
                                $entity_cn->question8_4 = $data_save['question8_4'];
                                $entity_cn->question8_4text = $data_save['question8_4text'];
                                $entity_cn->question1_1 = $data_save['question1_1'];
                                $entity_cn->question2_1 = $data_save['question2_1'];
                                $entity_cn->question3_1 = $data_save['question3_1'];
                                $entity_cn->question3_1text = $data_save['question3_1text'];
                                $entity_cn->question3_2 = $data_save['question3_2'];
                                $entity_cn->question3_2text = $data_save['question3_2text'];
                                $entity_cn->question4_1 = $data_save['question4_1'];
                                $entity_cn->question6_1 = $data_save['question6_1'];
                                $entity_cn->question6_1text = $data_save['question6_1text'];
                                $entity_cn->question5_1text = $data_save['question5_1text'];
                                $entity_cn->question9_1text = $data_save['question9_1text'];
                                $entity_cn->question7_1 = $data_save['question7_1'];
                                $entity_cn->preentre_update = isset($data_save['preentre_update']) ? $data_save['preentre_update'] : 0;
                                $entity_cn->created_id = 0;
                                $entity_cn->modified_id = 0;
                                $entity_cn->modified_date = date('Y-m-d H:i:s');
                                if (!$counselNotesTable->save($entity_cn, ['atomic' => false])) {
                                    $reservesTable->getConnection()->rollback();

                                    return false;
                                }
                            } catch (\PDOException $e) {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }

                            return true;
                        });

                        if ($updateTransaction == true && !isset($data['test'])) {
                            $queryName = $reservesTable->find()
                                ->select(['Users.id', 'Users.email', 'Users.name_last', 'Users.name_first',
                                    'Reserves.reserve_status', 'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_end',
                                    'Concierges.name', 'Concierges.mailaddress', 'sex' => 'Users.sex', 'birthday' => 'Users.birthday',
                                    'CounselNotes.question5_1text'])
                                ->join(['ShiftWorks' => ['table' => 'shift_works',
                                    'type' => 'LEFT',
                                    'conditions' => ['Reserves.work_date = ShiftWorks.work_date', 'Reserves.work_time_start = ShiftWorks.work_time_start', 'Reserves.concierge_id = ShiftWorks.concierge_id']],
                                    'Concierges' => ['table' => 'concierges',
                                        'type' => 'LEFT',
                                        'conditions' => ['Concierges.avail_flg = 1', 'Concierges.id = ShiftWorks.concierge_id']],
                                    'CounselNotes' => ['table' => 'counsel_notes',
                                        'type' => 'LEFT',
                                        'conditions' => ['Reserves.id = CounselNotes.reserve_id']],
                                    'Users' => ['table' => 'users',
                                        'type' => 'LEFT',
                                        'conditions' => ['Reserves.user_id = Users.id']]])
                                ->where(['Reserves.id' => $reserve_id])->first();

                            $work_time_start = substr($queryName->work_time_start, 0, 2) . ':' . substr($queryName->work_time_start, 2, 2);
                            $work_time_end = substr($queryName->work_time_end, 0, 2) . ':' . substr($queryName->work_time_end, 2, 2);
                            $sex = '';
                            if ($queryName->sex == 1) {
                                $sex = '男性';
                            } elseif ($queryName->sex == 2) {
                                $sex = '女性';
                            }
                            $mailinfos['user_name'] = $queryName->Users['name_last'] . ' ' . $queryName->Users['name_first'];
                            $mailinfos['user_id'] = $queryName->Users['id'];
                            $mailinfos['user_gender'] = $sex;
                            $mailinfos['user_birthday'] = (int)date('Y') - (int)date("Y", strtotime($queryName->birthday));
                            $mailinfos['email'] = $queryName->Users['email'];
                            $mailinfos['work_date'] = date("Y-m-d", strtotime($queryName->work_date));
                            $mailinfos['work_time_start'] = $work_time_start;
                            $mailinfos['work_time_end'] = $work_time_end;
                            $mailinfos['pre_concierge_name'] = $queryName->Concierges['name'];
                            $mailinfos['comment'] = $queryName->CounselNotes['question5_1text'];
                            if ($mailinfos['email']) {
                                $this->sendEmail(
                                    Configure::read('from_mailaddress'),
                                    $mailinfos['email'],
                                    '【Startup Hub Tokyo】コンシェルジュ相談のお申込みありがとうございます。',
                                    $mailinfos,
                                    'email_mypage_reserves_register',
                                    null,
                                    null
                                );
                            }
                            $mailinfos['data_save'] = $data_save;

                            $conciergeEmail = (!empty($queryName->Concierges['mailaddress'])) ? $queryName->Concierges['mailaddress'] : null;
                            if ($conciergeEmail) {
                                $this->sendEmail(
                                    Configure::read('from_mailaddress'),
                                    $conciergeEmail,
                                    '【Startup Hub Tokyo】コンシェルジュ相談お申込みがありました。',
                                    $mailinfos,
                                    'email_mypage_reserves_admin',
                                    null,
                                    Configure::read('admin_mailaddress')
                                );
                            }
                        }
                    }
                    if ($updateTransaction == true) {
                        $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_038'));
                    } else {
                        $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_039'));
                    }

                    $session->delete('data_save');
                    $this->render('form_thanks');
                } else {
                    return $this->redirect(['controller' => 'Reserve', 'action' => 'form', '?' => ['id' => $sid, 'his' => $his, 'rs' => $rs]]);
                }
            }
        }

        if ($rs > 0) {
            $reserve_data = $reservesTable->find()
                ->select(['Users.id', 'Users.type', 'Users.name_last', 'Users.name_first',
                    'Reserves.reserve_status', 'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_end',
                    'Concierges.id', 'Concierges.name',
                    'CounselNotes.id', 'CounselNotes.work_time_start', 'CounselNotes.work_time_end', 'CounselNotes.achieve',
                    'CounselNotes.achieve_text', 'CounselNotes.question1_1', 'CounselNotes.question2_1', 'CounselNotes.question2_1text',
                    'CounselNotes.question3_1', 'CounselNotes.question3_1text', 'CounselNotes.question3_2', 'CounselNotes.question3_2text',
                    'CounselNotes.question4_1', 'CounselNotes.question5_1text', 'CounselNotes.question6_1', 'CounselNotes.question6_1text',
                    'CounselNotes.question7_1', 'CounselNotes.question8_1', 'CounselNotes.question8_1text', 'CounselNotes.question8_2',
                    'CounselNotes.question8_2text', 'CounselNotes.question8_3', 'CounselNotes.question8_3text', 'CounselNotes.question8_4',
                    'CounselNotes.question8_4text', 'CounselNotes.question9_1text', 'CounselNotes.preentre_update'])
                ->join(['ShiftWorks' => ['table' => 'shift_works',
                    'type' => 'LEFT',
                    'conditions' => ['Reserves.work_date = ShiftWorks.work_date', 'Reserves.work_time_start = ShiftWorks.work_time_start', 'Reserves.concierge_id = ShiftWorks.concierge_id']],
                    'Concierges' => ['table' => 'concierges',
                        'type' => 'LEFT',
                        'conditions' => ['Concierges.avail_flg = 1', 'Concierges.id = ShiftWorks.concierge_id']],
                    'CounselNotes' => ['table' => 'counsel_notes',
                        'type' => 'LEFT',
                        'conditions' => ['Reserves.id = CounselNotes.reserve_id']],
                    'Users' => ['table' => 'users',
                        'type' => 'LEFT',
                        'conditions' => ['Reserves.user_id = Users.id']]])
                ->where(['Reserves.id' => $rs, 'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')])->first();
            if (!count($reserve_data)) {
                return $this->redirect(['controller' => 'Reserve', 'action' => 'index']);
            }

            $data_load = [];
            $data_load['reserves'] = !empty($cn) ? $cn : 0;
            $data_load['work_date'] = $reserve_data->work_date;
            $data_load['work_time_start'] = $reserve_data->work_time_start;
            $data_load['work_time_end'] = $reserve_data->work_time_end;
            $data_load['concierge_id'] = $reserve_data->Concierges['id'];
            $data_load['concierge_name'] = $reserve_data->Concierges['name'];
            $data_load['user_id'] = $reserve_data->Users['id'];
            $data_load['user_name'] = $reserve_data->Users['name_last'] . ' ' . $reserve_data->Users['name_first'];
            $data_load['user_type'] = $reserve_data->Users['type'];
            $data_load['question8_1'] = $reserve_data->CounselNotes['question8_1'];
            $data_load['question8_1text'] = $reserve_data->CounselNotes['question8_1text'];
            $data_load['question8_2'] = $reserve_data->CounselNotes['question8_2'];
            $data_load['question8_2text'] = $reserve_data->CounselNotes['question8_2text'];
            $data_load['question8_3'] = $reserve_data->CounselNotes['question8_3'];
            $data_load['question8_3text'] = $reserve_data->CounselNotes['question8_3text'];
            $data_load['question8_4'] = $reserve_data->CounselNotes['question8_4'];
            $data_load['question8_4text'] = $reserve_data->CounselNotes['question8_4text'];
            $data_load['question1_1'] = $reserve_data->CounselNotes['question1_1'];
            $data_load['question2_1'] = $reserve_data->CounselNotes['question2_1'];
            $data_load['question3_1'] = $reserve_data->CounselNotes['question3_1'];
            $data_load['question3_1text'] = $reserve_data->CounselNotes['question3_1text'];
            $data_load['question3_2'] = $reserve_data->CounselNotes['question3_2'];
            $data_load['question3_2text'] = $reserve_data->CounselNotes['question3_2text'];
            $data_load['question4_1'] = $reserve_data->CounselNotes['question4_1'];
            $data_load['question6_1'] = $reserve_data->CounselNotes['question6_1'];
            $data_load['question6_1text'] = $reserve_data->CounselNotes['question6_1text'];
            $data_load['question5_1text'] = $reserve_data->CounselNotes['question5_1text'];
            $data_load['question9_1text'] = $reserve_data->CounselNotes['question9_1text'];
            $data_load['question7_1'] = $reserve_data->CounselNotes['question7_1'];
            $data_load['reserve_status'] = $reserve_data->reserve_status;
            $data_load['preentre_update'] = $reserve_data->CounselNotes['preentre_update'];

            $this->set(['data_load' => $data_load]);
        } else {
            $shiftworks_data = $shiftWorksTable->find()
                ->select(['ShiftWorks.id', 'ShiftWorks.work_date', 'ShiftWorks.work_time_start', 'ShiftWorks.concierge_id', 'Concierges.name'])
                ->join(['Concierges' => ['table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => ['Concierges.avail_flg = 1', 'Concierges.id = ShiftWorks.concierge_id']]])
                ->where(['ShiftWorks.id' => $sid])->first();

            $user_data = TableRegistry::get('Users')->find()
                ->select(['Users.id', 'Users.type', 'Users.name_last', 'Users.name_first'])
                ->where(['Users.id' => $mypage_Id])->first();

            if (!count($shiftworks_data) || !count($user_data)) {
                return $this->redirect(['controller' => 'Reserve', 'action' => 'index']);
            }

            $data_load = [];
            $data_load['reserves'] = !empty($cn) ? $cn : 0;
            $data_load['work_date'] = $shiftworks_data->work_date;
            $data_load['work_time_start'] = $shiftworks_data->work_time_start;
            $data_load['work_time_end'] = '';
            $data_load['concierge_id'] = $shiftworks_data->concierge_id;
            $data_load['concierge_name'] = $shiftworks_data->Concierges['name'];
            $data_load['user_id'] = $user_data->id;
            $data_load['user_name'] = $user_data->name_last . ' ' . $user_data->name_first;
            $data_load['user_type'] = $user_data->type;
            $data_load['question8_1'] = '';
            $data_load['question8_1text'] = '';
            $data_load['question8_2'] = '';
            $data_load['question8_2text'] = '';
            $data_load['question8_3'] = '';
            $data_load['question8_3text'] = '';
            $data_load['question8_4'] = '';
            $data_load['question8_4text'] = '';
            $data_load['question1_1'] = '';
            $data_load['question2_1'] = '';
            $data_load['question3_1'] = '';
            $data_load['question3_1text'] = '';
            $data_load['question3_2'] = '';
            $data_load['question3_2text'] = '';
            $data_load['question4_1'] = '';
            $data_load['question6_1'] = '';
            $data_load['question6_1text'] = '';
            $data_load['question5_1text'] = '';
            $data_load['question9_1text'] = '';
            $data_load['question7_1'] = '';
            $data_load['reserve_status'] = 0;
            $data_load['preentre_update'] = 0;

            //errcheck
            $err_flg = 0;
            $superuser = 0;
            if (Configure::read('superuser_id') == $this->getUserEmail($mypage_Id)) {
                $superuser = 1;
            }

            //予約日時超過チェック
            $workDateTine = strtotime($data_load['work_date'] . ' ' . substr($data_load['work_time_start'], 0, 2) . ':' . substr($data_load['work_time_start'], 2, 2));
            if (time() > $workDateTine) {
                $err_flg = 1;
            }
            //月刊予約件数超過チェック
            if ($superuser == 0) {
                $date = date('Y/m/d', strtotime($shiftworks_data->work_date));
                $this->set('currentTime', $date);
                $initsdate = date('Y/m/d', strtotime('first day of this month', strtotime($date)));
                $initedate = date('Y/m/d', strtotime('last day of this month', strtotime($date)));
                $reservesstatus = $this->reservesstatus($user_data->id, $initsdate);
                if ($reservesstatus == 0) {
                    $data_load['reserve_flg'] = 1;
                    $err_flg = 2;
                } else {
                    //同一時間帯予約チェック
                    $reserve_cnt = $reservesTable->find()
                        ->where([
                            'work_date' => $shiftworks_data->work_date,
                            'work_time_start' => $shiftworks_data->work_time_start,
                            'user_id' => $user_data->id,
                            'reserve_status' => 1
                        ]);
                    $data_load['reserve_flg'] = 0;
                    if ($reserve_cnt->count() > 0) {
                        $data_load['reserve_flg'] = 1;
                        $err_flg = 3;
                    }
                }
            }
            if ($err_flg == 0) {
                //仮予約中データチェック
                $tmprsvdate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
                $reserve_cnt = $shiftWorksTable->find()
                    ->where([
                        'id' => $sid,
                        'tmp_reserve_date >=' => $tmprsvdate,
                        'tmp_reserve_id <>' => $mypage_Id,
                        'status' => 0
                    ]);
                $data_load['reserve_flg'] = 0;
                if ($reserve_cnt->count() > 0) {
                    $data_load['reserve_flg'] = 1;
                    $err_flg = 4;
                }
            }
            if ($err_flg == 0) {
                //予約済データチェック
                $tmprsvdate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
                $reserve_cnt = $shiftWorksTable->find()
                    ->where([
                        'id' => $sid,
                        'status' => 1
                    ]);
                $data_load['reserve_flg'] = 0;
                if ($reserve_cnt->count() > 0) {
                    $data_load['reserve_flg'] = 1;
                    $err_flg = 5;
                }
            }
            $this->set(['data_load' => $data_load]);

            //エラー表示
            if (!$this->request->is('post')) {
                if ($err_flg == 1) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_054'));
                }
                if ($err_flg == 2) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_056'));
                }
                if ($err_flg == 3) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_055'));
                }
                if ($err_flg == 4) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_057'));
                }
                if ($err_flg == 5) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_058'));
                }
                if ($err_flg == 0) {
                    $updateTransaction = $reservesTable->getConnection()->transactional(function () use ($reservesTable, $shiftWorksTable, $sid, $mypage_Id) {
                        try {
                            $entity_sw = $shiftWorksTable->get($sid);
                            $entity_sw->tmp_reserve_date = date('Y-m-d H:i:s');
                            $entity_sw->tmp_reserve_id = $mypage_Id;
                            if (!$shiftWorksTable->save($entity_sw, ['atomic' => false])) {
                                $reservesTable->getConnection()->rollback();

                                return false;
                            }
                        } catch (RecordNotFoundException $e) {
                            $reservesTable->getConnection()->rollback();

                            return false;
                        }

                        return true;
                    });
                }
            }
        }
    }
    /**
     * Check auth of user Reserves Status
     * @author thovo
     * @param int $id of user
     * @param string $date day of month
     * @return \Cake\Network\Response|void : boolean
     */
    private function reservesstatus($id, $date)
    {
        $initsdate = date('Y/m/d', strtotime('first day of this month', strtotime($date)));
        $initedate = date('Y/m/d', strtotime('last day of this month', strtotime($date)));
        $countReserves = TableRegistry::get('Reserves');

        $query = $countReserves->find();
        $query->select(['Reserves.id', $query->func()->count('Reserves.id')])
            ->where(['Reserves.user_id' => $id, 'Reserves.work_date >=' => $initsdate, 'Reserves.work_date <= ' => $initedate, 'Reserves.reserve_status <> ' => 9,
                'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')])
            ->group(['Reserves.id']);
        $total = $query->count();

        if ($total >= 4) {
            $reservesstatus = 0;
        } else {
            $reservesstatus = 1;
        }

        return $reservesstatus;
    }

    /**
     * Check shift_work book allready
     * @author thovo
     * @param int $sid ID of shift work
     * @return \Cake\Network\Response|void : boolean
     */
    private function checkShiftWorkBooked($sid)
    {
        $shiftWorksTable = TableRegistry::get('ShiftWorks');
        $shiftworkItem = $shiftWorksTable->get($sid);
        if (isset($shiftworkItem)) {
            if ($shiftworkItem->status == 1) {
                return true;
            }
        }

        return false;
    }
}
