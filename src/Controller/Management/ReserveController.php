<?php

/**
 * Account Controller
 * @author: Dat
 **/

namespace App\Controller\Management;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Filesystem\File;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class ReserveController extends ManagementController
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
     * Initial function
     * @param Event $event Event object
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
    /**
     * Index page /admin/shift/list
     * @param null $id Start date
     * @author: Dat
     * @return \Cake\Network\Response|void : boolean
     */
    public function index($id = null)
    {
        $session = $this->request->session();
        $this->clearSearch('search011');
        $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;
        $loginAuth = ($session->check('LOGIN.AUTH'))?$session->read('LOGIN.AUTH'):'';
        $edit = 'see';
        if ($loginAuth == 0 || $loginAuth == 4 || $loginAuth == 1 || $loginAuth == 5) { //System and Manager
            $edit = 'see';
        } elseif ($loginAuth == 3) { // Concierge
            $edit = 'cena';
        } else {
            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        }
        $this->set(compact('edit'));

        $ReservesTable = TableRegistry::get('Reserves');
        $conciergeTable = TableRegistry::get('Concierges');
        $counselNotesTable = TableRegistry::get('CounselNotes');
        $queryConcier = $conciergeTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->where(['avail_flg' => '1'])->order(['id' => 'ASC']);
        $data = $queryConcier->toArray();
        $arrConcier = ['0' => ''] + $data;
        $this->set(compact('arrConcier'));

        $data = $this->request->data;
        if ($this->request->is('post') && !empty($data['hid_csv']) && $data['hid_csv'] == 1) {
            $conditions_CSV = [];
            if (isset($data['arrChk']) || (!empty($data['download_all']) && $data['download_all'] == '1')) {
                if ((!empty($data['download_all']) && $data['download_all'] == '1')) {
                    $conditions_CSV = json_decode($data['csvwhere'], true);
                } else {
                    $conditions_CSV['Reserves.id IN'] = $data['arrChk']; // old is user_id
                }
                $queryCSV = $ReservesTable->find()
                    ->select(['Reserves.id', 'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_end', 'Reserves.reserve_classification', 'Reserves.concierge_id', 'Reserves.user_id', 'Reserves.reserve_status', 'Reserves.cancel_status', 'Reserves.created_date',
                        'Users.id', 'member_type' => 'Users.type', 'sex' => 'Users.sex', 'birthday' => 'Users.birthday',
                        'name_last' => 'Users.name_last', 'name_first' => 'Users.name_first',
                        'Concierges.id', 'Concierges.name',
                        'CounselNotes.id', 'CounselNotes.work_time_start', 'CounselNotes.work_time_end', 'CounselNotes.question1_1',
                        'CounselNotes.question2_1', 'CounselNotes.question2_1text', 'CounselNotes.question3_1', 'CounselNotes.question3_1text',
                        'CounselNotes.question3_2', 'CounselNotes.question3_2text', 'CounselNotes.question4_1', 'CounselNotes.question5_1text',
                        'CounselNotes.question6_1text', 'CounselNotes.question9_1text', 'CounselNotes.question6_1', 'CounselNotes.question7_1',
                        'CounselNotes.question8_1', 'CounselNotes.question8_1text', 'CounselNotes.question8_2', 'CounselNotes.question8_2text',
                        'CounselNotes.question8_3', 'CounselNotes.question8_3text', 'CounselNotes.question8_4', 'CounselNotes.question8_4text',
                        'CounselNotes.anser1', 'CounselNotes.anser2', 'CounselNotes.anser3', 'CounselNotes.anser4', 'CounselNotes.evaluate1',
                        'CounselNotes.evaluate2', 'CounselNotes.evaluate3', 'CounselNotes.evaluate4', 'CounselNotes.evaluate5',
                        'preentre_update' => 'CounselNotes.preentre_update'
                    ])
                    ->contain(['CsvImportInfos' => [
                        'fields' => [
                            'csv_user_id' => 'CsvImportInfos.user_id',
                            'csv_user_name' => 'CsvImportInfos.user_name',
                            'csv_user_sex' => 'CsvImportInfos.user_sex',
                            'csv_user_age' => 'CsvImportInfos.user_age',
                            'csv_member_classification' => 'CsvImportInfos.member_classification',
                            'csv_responsible_concierge' => 'CsvImportInfos.responsible_concierge',
                        ]
                    ]])
                    ->join([
                        'Concierges' => [
                            'table' => 'concierges',
                            'type' => 'LEFT',
                            'conditions' => ['Reserves.concierge_id = Concierges.id']
                        ],
                        'Users' => [
                            'table' => 'users',
                            'type' => 'LEFT',
                            'conditions' => ['Reserves.user_id = Users.id']
                        ],
                        'CounselNotes' => [
                            'table' => 'counsel_notes',
                            'type' => 'LEFT',
                            'conditions' => ['Reserves.id = CounselNotes.reserve_id']
                        ],
                    ])
                    ->where($conditions_CSV)
                    ->order(['Reserves.work_date' => 'DESC', 'Reserves.work_time_start' => 'DESC', 'Reserves.reserve_status' => 'ASC', 'Users.id' => 'ASC', 'Reserves.id' => 'DESC']);
                if ($queryCSV->count() > 0) {
                    Configure::write('debug', false);
                    $dataCSV = $queryCSV->toArray();
                    $this->autoRender = false;
                    ini_set('max_execution_time', 6000);
                    $temp_memory = fopen("php://output", 'w');
                    fprintf($temp_memory, chr(0xEF) . chr(0xBB) . chr(0xBF));
                    $this->myfputcsv($temp_memory, [
                        '予約ID', '予約状況', 'キャンセル区分', '予約日', '予約時刻',
                        '予約終了時間', '予約区分', '実施開始時刻', '実施終了時刻', 'ステータス',
                        'ユーザID', '姓', '名',
                        '会員種別', '性別', '年齢', '利用回数', 'プレアントレ更新希望',
                        'コンシェルジュID', 'コンシェルジュ名',
                        'コンシェルジュサービスを知った手段', 'コンシェルジュサービスを知った手段：その他',
                        'コンシェルジュサービスを知った手段２', 'コンシェルジュサービスを知った手段２：その他',
                        '起業への意思決定状況',
                        '現在のお仕事の状況',
                        '起業に関する現在の悩み', '起業に関する現在の悩み：その他',
                        '起業に関する現在の悩み２', '起業に関する現在の悩み２：その他',
                        '想定している顧客層',
                        '業種について教えてください', '業種について教えてください：その他',
                        '事業の概略',
                        '第三者に見せるための事業計画',
                        '今回の相談の概要',
                        '事業内容', '現状把握', '相談内容とアドバイス', '初見・対応・特記事項',
                        '事業選択の必然性', '事業アイデアの魅力', '事業アイデアの実現性', '経営遂行能力', '合計', '登録日時'
                    ], ',', '"');
                    header('HTTP/1.1 200 OK');
                    header('Date: ' . date('D M j G:i:s T Y'));
                    header('Last-Modified: ' . date('D M j G:i:s T Y'));
                    header("Content-type: application/vnd . ms-excel;charset=Shift_JIS");
                    header('Content-Disposition: attachement;filename="Reserve_' . date('YmdHis') . '.csv";');
                    $arr_question1 = Configure::read('QUESTION1');
                    $arr_question2 = Configure::read('QUESTION2');
                    $arr_question3 = Configure::read('QUESTION3');
                    $arr_question4 = Configure::read('QUESTION4');
                    $arr_question5 = Configure::read('QUESTION5');
                    $arr_question6 = Configure::read('QUESTION6');
                    $arr_question7 = Configure::read('QUESTION7');
                    $arr_question8 = Configure::read('QUESTION8');
                    $arr_question9 = Configure::read('QUESTION9');
                    $arr_question10 = Configure::read('QUESTION10');
                    $arr_question11 = Configure::read('QUESTION11');
                    $membershipTypes = Configure::read('USER_TYPES');
                    $countUsers = json_decode($data['time_use'], true);
                    $config_status = Configure::read('QUESTION_STATUS');

                    foreach ($dataCSV as $item) {
                        $q8_2 = '';
                        $q8_2_other = '';
                        if (!empty($item['id'])) {
                            if (!empty($item['CounselNotes']['question8_2'])) {
                                $q8_2 = $this->__convertQuestions($arr_question2, !empty($item['CounselNotes']['question8_2']) ? $item['CounselNotes']['question8_2'] : '');
                                $q8_2_other = !empty($item['CounselNotes']['question8_2text']) ? $item['CounselNotes']['question8_2text'] : '';
                            }
                            if (!empty($item['CounselNotes']['question8_3'])) {
                                $q8_2 = $this->__convertQuestions($arr_question3, !empty($item['CounselNotes']['question8_3']) ? $item['CounselNotes']['question8_3'] : '');
                                $q8_2_other = !empty($item['CounselNotes']['question8_3text']) ? $item['CounselNotes']['question8_3text'] : '';
                            }
                            if (!empty($item['CounselNotes']['question8_4'])) {
                                $q8_2 = $this->__convertQuestions($arr_question4, !empty($item['CounselNotes']['question8_4']) ? $item['CounselNotes']['question8_4'] : '');
                                $q8_2_other = !empty($item['CounselNotes']['question8_4text']) ? $item['CounselNotes']['question8_4text'] : '';
                            }

                            $statusVal = '';
                            if ($item['reserve_status'] != 9) {
                                $statusVal = 'r_' . $item['reserve_status'];
                            } else {
                                if ($item['cancel_status']) {
                                    $statusVal = 'c_' . $item['cancel_status'];
                                }
                            }
                            $userId = "";
                            $userNameLast = "";
                            $userNameFirst = "";
                            $userSex = "";
                            $userAge = "";
                            $userType = "";
                            if ($item['reserve_classification'] == Configure::read('RESERVE_CLASSIFICATION.FROM_RECEPTION') && (empty($item['csv_user_id']) || !is_numeric($item['csv_user_id']))) {
                                $userId = $this->pregReplaceCsv($item['csv_user_id']);
                                $userNameLast = "";
                                $userNameFirst = $this->pregReplaceCsv($item['csv_user_name']);
                                $userSex = str_replace('性', '', $item['csv_user_sex']);
                                $userAge = str_replace('歳代', '', $item['csv_user_age']);
                                $userType = $this->pregReplaceCsv($item['csv_member_classification']);
                            } else {
                                $userId = $item['user_id'];
                                $userNameLast = $this->pregReplaceCsv($item['name_last']);
                                $userNameFirst = $this->pregReplaceCsv($item['name_first']);
                                $userSex = $item['sex'] ? Configure::read('SEX')[$item['sex']] : '';
                                $userAge = $item['birthday'] ? date_diff(date_create($item['birthday']), date_create('today'))->y : '';
                                $userType = $this->pregReplaceCsv($membershipTypes[$item['member_type']]);
                            }
                            $this->myfputcsv($temp_memory, [
                                $item['id'],
                                $item['reserve_status'],
                                $item['cancel_status'],
                                date("Y-m-d", strtotime($item['work_date'])),
                                $item['work_time_start'] ? substr_replace($item['work_time_start'], ':', 2, 0) : '',
                                $item['work_time_end'] ? substr_replace($item['work_time_end'], ':', 2, 0) : '',
                                $item['reserve_classification'] ? Configure::read('RESERVE_CLASSIFICATION.CLASSIFICATION')[$item['reserve_classification']] : '',
                                $item['CounselNotes']['work_time_start'] ? substr_replace($item['CounselNotes']['work_time_start'], ':', 2, 0) : '',
                                $item['CounselNotes']['work_time_end'] ? substr_replace($item['CounselNotes']['work_time_end'], ':', 2, 0) : '',
                                $this->pregReplaceCsv($config_status[$statusVal]),
                                $userId,
                                $userNameLast,
                                $userNameFirst,
                                $userType,
                                $userSex,
                                $userAge,
                                $countUsers[$item['user_id']],
                                $item['preentre_update'] == 1 ? '希望' : '',
                                $item['concierge_id'],
                                $this->pregReplaceCsv($item['Concierges']['name']),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question1, !empty($item['CounselNotes']['question8_1']) ? $item['CounselNotes']['question8_1'] : '')),
                                !empty($item['CounselNotes']['question8_1text']) ? $item['CounselNotes']['question8_1text'] : '',
                                $this->pregReplaceCsv($q8_2),
                                $this->pregReplaceCsv($q8_2_other),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question5, !empty($item['CounselNotes']['question1_1']) ? $item['CounselNotes']['question1_1'] : '')),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question6, !empty($item['CounselNotes']['question2_1']) ? $item['CounselNotes']['question2_1'] : '')),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question7, !empty($item['CounselNotes']['question3_1']) ? $item['CounselNotes']['question3_1'] : '')),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['question3_1text']) ? $item['CounselNotes']['question3_1text'] : ''),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question8, !empty($item['CounselNotes']['question3_2']) ? $item['CounselNotes']['question3_2'] : '')),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['question3_2text']) ? $item['CounselNotes']['question3_2text'] : ''),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question9, !empty($item['CounselNotes']['question4_1']) ? $item['CounselNotes']['question4_1'] : '')),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question10, !empty($item['CounselNotes']['question6_1']) ? $item['CounselNotes']['question6_1'] : '')),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['question6_1text']) ? $item['CounselNotes']['question6_1text'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['question5_1text']) ? $item['CounselNotes']['question5_1text'] : ''),
                                $this->pregReplaceCsv($this->__convertQuestions($arr_question11, !empty($item['CounselNotes']['question7_1']) ? $item['CounselNotes']['question7_1'] : '')),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['question9_1text']) ? $item['CounselNotes']['question9_1text'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['anser1']) ? $item['CounselNotes']['anser1'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['anser2']) ? $item['CounselNotes']['anser2'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['anser3']) ? $item['CounselNotes']['anser3'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['anser4']) ? $item['CounselNotes']['anser4'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['evaluate1']) ? $item['CounselNotes']['evaluate1'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['evaluate2']) ? $item['CounselNotes']['evaluate2'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['evaluate3']) ? $item['CounselNotes']['evaluate3'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['evaluate4']) ? $item['CounselNotes']['evaluate4'] : ''),
                                $this->pregReplaceCsv(!empty($item['CounselNotes']['evaluate5']) ? $item['CounselNotes']['evaluate5'] : ''),
                                date_format($item['created_date'], 'Y-m-d H:i:s'),
                            ], ',', '"');
                        }
                    }
                    //fpassthru($temp_memory);

                    fclose($temp_memory);
                    //Clear the last empty row.
                    $out = ob_get_contents();
                    ob_end_clean();
                    echo trim($out);
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_051'));
                }
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_052'));
            }
        }

        if ($this->request->is('post') && !empty($this->request->data['search'])) {
            $session->write('search011.username', isset($data['username'])?$data['username']:'');
            $session->write('search011.time_start', isset($data['time_start'])?$data['time_start']:'');
            $session->write('search011.time_end', isset($data['time_end'])?$data['time_end']:'');
            $session->write('search011.concierges', isset($data['concierges'])?$data['concierges']:'');
        }
        /*
                if (!$this->request->is('post')) {
                    $session->write('search011.username', '');
                    $session->write('search011.time_start', '');
                    $session->write('search011.time_end', '');
                    $session->write('search011.concierges', '');
                }
        */
        $username = !is_null($session->read('search011.username')) ? $session->read('search011.username') : '';
        $timeStart = !is_null($session->read('search011.time_start')) ? $session->read('search011.time_start') : '';
        $timeEnd = !is_null($session->read('search011.time_end')) ? $session->read('search011.time_end') : '';
        $concierge = !is_null($session->read('search011.concierges')) ? $session->read('search011.concierges') : 0;

        $this->set(compact(['username', 'timeStart', 'timeEnd', 'concierge']));

        $conditions['Concierges.avail_flg'] = 1;
        if ($edit == 'see' && $concierge > 0) {
            $conditions["Reserves.concierge_id"] = $concierge;
        } elseif ($edit == 'cena') {
            $loginConcierg = $conciergeTable->find()
                ->where([
                    'Concierges.account_id' => $loginId,
                    'Concierges.avail_flg' => 1,
                ]);
            if ($loginConcierg->count() > 0) {
                $conditions["Reserves.concierge_id"] = $loginConcierg->first()->id;
            }
        }

        if (!empty($username)) {
//            $conditions["CONCAT(name_last,' ',name_first) LIKE"] = '%' . $username . '%';
//            $conditions["CONCAT(hiragana_name_last,' ',hiragana_name_first) LIKE"] = '%' . $username . '%';
            $conditions["CONCAT(hiragana_name_last,hiragana_name_first) LIKE"] = '%' . $username . '%';
        }
        if (!empty($timeStart)) {
            $conditions["Reserves.work_date >="] = date('Y-m-d', strtotime($timeStart));
        }
        if (!empty($timeEnd)) {
            $conditions["Reserves.work_date <="] = date('Y-m-d', strtotime($timeEnd));
        }

        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search011.page', $this->request->query['page']);
        } else {
            $session->write('search011.page', 1);
        }
        $page = !empty($session->read('search011.page')) ? $session->read('search011.page') : 1;

        $ShiftWorksTable = TableRegistry::get('ShiftWorks');

        if (isset($data['status']) && empty($data['hid_csv']) && isset($data['arrChk'])) { // change stat
            $updateTransaction = $ReservesTable->getConnection()->transactional(function () use ($data, $ReservesTable, $loginId, $ShiftWorksTable) {
                $arrSize = count($data['arrChk']);
                for ($i = 0; $i < $arrSize; $i++) {
                    $shiftStatus = 1;
                    $Qupdate = $ReservesTable->get($data['arrChk'][$i]);
                    if (substr($data['status'], 0, 1) == 'r') {
                        $Qupdate->reserve_status = substr($data['status'], 2, 2);
                        // $Qupdate->cancel_status = '0';
                    } else {
                        $Qupdate->cancel_status = substr($data['status'], 2, 2);
                        $Qupdate->reserve_status = '9';
                        $Qupdate->cancel_date = date('Y-m-d H:i:s');
                        $shiftStatus = 0;
                    }
                    $Qupdate->modified_id = $loginId;
                    $Qupdate->modified_date = date('Y-m-d H:i:s');
                    if (!$ReservesTable->save($Qupdate, ['atomic' => false])) {
                        $ReservesTable->getConnection()->rollback();

                        return false;
                    } else {
                        $QupdateShift = $ShiftWorksTable->find('all')->where(['concierge_id' => $Qupdate->concierge_id,
                            'work_date' => date('Y-m-d', strtotime($Qupdate->work_date)),
                            'work_time_start' => $Qupdate->work_time_start])->first();
                        if (count($QupdateShift) > 0) {
                            $QupdateShift->status = $shiftStatus;
                            $QupdateShift->modified_id = $loginId;
                            $QupdateShift->modified_date = date('Y-m-d H:i:s');
                            //if (substr($data['status'], 0, 1) == 'c' && !$ShiftWorksTable->save($QupdateShift, ['atomic' => false])) {
                            if (!$ShiftWorksTable->save($QupdateShift, ['atomic' => false])) {
                                $ReservesTable->getConnection()->rollback();

                                return false;
                            }
                        } else {
                            if ($Qupdate->reserve_classification == Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')) {
                                $ReservesTable->getConnection()->rollback();

                                return false;
                            }
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

        if ($this->request->is('post') && isset($this->request->data['comment'])) { // change statNG CANCEL NG
            $mailinfos = [];
            $updateTransaction2 = $ReservesTable->getConnection()->transactional(function () use ($data, $ReservesTable, $loginId, &$mailinfos, $ShiftWorksTable) {
                $Qupdate = $ReservesTable->get($data['hid_rid']);

                $ConciergesTable = TableRegistry::get('Concierges');
                $UsersTable = TableRegistry::get('Users');
                $Qcon = $ConciergesTable->get($Qupdate->concierge_id);
                $Quser = $UsersTable->get($Qupdate->user_id);

                $mailinfos['user_name'] = $Quser->name_last . ' ' . $Quser->name_first;
                $mailinfos['work_date'] = $Qupdate->work_date;
                $mailinfos['work_time_start'] = $Qupdate->work_time_start;
                $mailinfos['work_time_end'] = $Qupdate->work_time_end;
                $mailinfos['comment'] = $data['comment'];
                $mailinfos['pre_concierge_name'] = $Qcon->name;
                $mailinfos['email'] = $Quser->email;

                $Qupdate->reserve_status = '9';
                $Qupdate->cancel_status = '9';
                $Qupdate->modified_id = $loginId;
                $Qupdate->modified_date = date('Y-m-d H:i:s');
                $Qupdate->cancel_date = date('Y-m-d H:i:s');
                if (!$ReservesTable->save($Qupdate, ['atomic' => false])) {
                    $ReservesTable->getConnection()->rollback();

                    return false;
                } else {
                    $QupdateShift = $ShiftWorksTable->find('all')->where(['concierge_id' => $Qupdate->concierge_id,
                        'work_date' => date('Y-m-d', strtotime($Qupdate->work_date)),
                        'work_time_start' => $Qupdate->work_time_start])->first();
                    if (count($QupdateShift) > 0) {
                        $QupdateShift->status = '0';
                        $QupdateShift->modified_id = $loginId;
                        $QupdateShift->modified_date = date('Y-m-d H:i:s');
                        if (!$ShiftWorksTable->save($QupdateShift, ['atomic' => false])) {
                            $ReservesTable->getConnection()->rollback();

                            return false;
                        }
                    } else {
                        if ($Qupdate->reserve_classification == Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')) {
                            $ReservesTable->getConnection()->rollback();

                            return false;
                        }
                    }
                }

                return true;
            });

            if ($updateTransaction2) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
                $this->sendEmail(
                    Configure::read('from_mailaddress'),
                    $mailinfos['email'],
                    '【スタートアップハブ東京】問診キャンセルのお知らせ',
                    $mailinfos,
                    'email_reserves_update',
                    null,
                    Configure::read('admin_mailaddress')
                );
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }
        }
        $queryManage = $ReservesTable->find()
            ->select(['name' => 'Concierges.name', 'cid' => 'Concierges.id',
            // 'sid' => 'ShiftWorks.id',
            // 'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start',
            'work_date' => 'Reserves.work_date', 'work_time_start' => 'Reserves.work_time_start',
            'reserve_classification' => 'Reserves.reserve_classification',
            'r_status' => 'Reserves.reserve_status', 'c_status' => 'Reserves.cancel_status',
            'name_last' => 'Users.name_last',
            'name_first' => 'Users.name_first', 'user_id' => 'Reserves.user_id',
            'sex' => 'Users.sex', 'birthday' => 'Users.birthday', 'pref' => 'Users.pref',
            'rid' => 'Reserves.id', 'preentre_update' => ' IFNULL(CounselNotes.preentre_update,0)'
            ])
            ->contain(['CsvImportInfos' => [
                'fields' => [
                    'csv_user_id' => 'CsvImportInfos.user_id',
                    'csv_user_name' => 'CsvImportInfos.user_name',
                    'csv_user_sex' => 'CsvImportInfos.user_sex',
                    'csv_user_age' => 'CsvImportInfos.user_age',
                    'csv_member_classification' => 'CsvImportInfos.member_classification',
                    'csv_responsible_concierge' => 'CsvImportInfos.responsible_concierge',
                ]
            ]])
            // ->join(['ShiftWorks' => ['table' => 'shift_works', 'type' => 'LEFT',
            //       'conditions' => ['ShiftWorks.concierge_id = Reserves.concierge_id', 'ShiftWorks.work_date = Reserves.work_date',
            //       'ShiftWorks.work_time_start = Reserves.work_time_start']]])
            ->join(['Concierges' => ['table' => 'concierges', 'type' => 'INNER',
                'conditions' => 'Reserves.concierge_id = Concierges.id']])
            ->join(['CounselNotes' => ['table' => 'counsel_notes', 'type' => 'LEFT',
                'conditions' => ['Reserves.id = CounselNotes.reserve_id']]])
            ->join(['Users' => ['table' => 'users', 'type' => 'LEFT',
                'conditions' => 'Reserves.user_id = Users.id']])
            ->where($conditions)
            ->order(['work_date' => 'DESC', 'work_time_start' => 'DESC', 'r_status' => 'ASC', 'user_id' => 'ASC', 'rid' => 'DESC']);
        // ->group(['user_id']);

        $pagingConfig = [
            'page' => $page,
            'limit' => 200, /* Configure::read('list_max_row'), */
            'maxLimit' => 200,
        ];

        $countUsers = [];
        foreach ($queryManage as $q) {
            /*
            $countUsers[$q->user_id] = $ReservesTable->find()
                ->where(['Reserves.user_id' => $q->user_id, 'Reserves.reserve_status <>' => 9])
                //->group(['Reserves.user_id'])
            // if group by user_id, the result is always 1 or 0
                ->count('Reserves.user_id');
            */
            if ($q->r_status != 9) {
                if (empty($countUsers[$q->user_id])) {
                    $countUsers[$q->user_id] = 1;
                } else {
                    $countUsers[$q->user_id] = $countUsers[$q->user_id] + 1;
                }
            } else {
                if (empty($countUsers[$q->user_id])) {
                    $countUsers[$q->user_id] = 0;
                }
            }
        }
        $this->set(compact('countUsers'));
        $this->set('csvwhere', json_encode($conditions));

        try {
            $concierges = $this->Paginator->paginate($queryManage, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'index',
                'page' => 1,
            ]);
        }

        $this->set(compact(['pagingConfig', 'concierges']));

        // cancel old position

        if (isset($data['arrChk'])) {
            $this->set('arrChkRe', $data['arrChk']);
        } else {
            $this->set('arrChkRe', '');
        }

        if (isset($data['reason'])) {
            $arrRe['time'] = $data['time_in'];
            $arrRe['date'] = $data['date_in'];
            $arrRe['name'] = $data['name_in'];
            $arrRe['con'] = $data['con_in'];
            $arrRe['rid'] = $data['rid_in'];
            $arrRe['stat'] = $data['stat_in'];
            $this->set(compact('arrRe'));
            $this->set('reason', $data['reason']);
        } else {
            $this->set('reason', '0');
        }
    }

    /**
     * @param array $definition Questions
     * @param string $value DB value
     * @return string
     */
    private function __convertQuestions($definition, $value)
    {
        if (empty($value)) {
            return '';
        }
        $arrVal = explode(',', $value);
        $res = '';
        $arrRes = [];
        if (!empty($arrVal)) {
            foreach ($arrVal as $val) {
                if (!empty($val) && !empty($definition[$val])) {
                    $arrRes[] = $definition[$val];
                }
            }
            if (!empty($arrRes)) {
//                $res = implode(' ', $arrRes);
                $res = implode(',', $arrRes);
            }
        }

        return $res;
    }
    /**
     * Import Reserve data from CSV
     * @author PL.Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function importReserve()
    {
        $session = $this->request->session();
        $loginId = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;
        $data = $this->request->data;
        if ($this->request->is('post') && !empty($data['csv_name']) && !empty($data['csv_name']['name'])) {
            $acceptFileType = ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'];
            if (!in_array($data['csv_name']['type'], $acceptFileType)) {
                $this->Flash->error('Invalid File Type');

                return;
            }
            $tmpCSVName = Configure::read('RESERVE_CLASSIFICATION.CSV_UPLOAD_PATH') . DS . 'csv_upload_' . date('YmdHis') . '.csv';
            /*
             * set time limit
             * upload large file
             * */
            ini_set('max_execution_time', 6000);
            if (!move_uploaded_file($data['csv_name']['tmp_name'], $tmpCSVName)) {
                $this->Flash->error('Failed to upload file.');

                return;
            }
            $arrFieldReserves = [
                'csv_time',
                'work_date',
                'work_time_start',
                'work_time_end',
                'corresponding_time',
                'responsible_concierge',
                'user_id',
                'user_name',
                'user_name_kana',
                'user_sex',
                'user_age',
                'user_address1',
                'user_address2',
                'user_email',
                'user_phone',
                'member_classification',
                'preentre_update', /*$reserve_data->Users['type']==2*/
                'preentre_member_card_no',
                'question9_1text',
                'question1_1',
                'question2_1',
                'question2_1_other', /*2-その他 ※不要セル*/
                'question3_1',
                'question3_1text',
                'question3_2',
                'question3_2text',
                'question4_1',
                'question6_1',
                'question6_1text',
                'question5_1text',
                'question7_1',
                'question8_1',
                'question8_1text',
                'question8_2',
                'question8_2text',
                'question8_3',
                'question8_3text',
                'question8_4',
                'question8_4text',
                'anser1',
                'anser2',
                'anser3',
                'anser4',
                'evaluate1',
                'evaluate2',
                'evaluate3',
                'evaluate4'
                ];
            $dataReserves = $this->importCsvToArray($tmpCSVName, $arrFieldReserves);
            $total = count($dataReserves);
            $total_row_insert = 0;
            $ReservesTable = TableRegistry::get('Reserves');
            $counselNotesTable = TableRegistry::get('CounselNotes');
            $failData = $this->validateData($dataReserves, $total);
            if (!empty($failData)) {
                $this->Flash->Error('It failed to import CSV, some records are not valid');
                $this->set(compact(['failData']));
            } else {
                $updateTrans = $ReservesTable->getConnection()->transactional(function () use ($total, $dataReserves, $ReservesTable, $counselNotesTable, &$total_row_insert, $loginId, &$failData) {
                    $arr_question1 = Configure::read('QUESTION1');
                    $arr_question2 = Configure::read('QUESTION2');
                    $arr_question3 = Configure::read('QUESTION3');
                    $arr_question4 = Configure::read('QUESTION4');
                    $arr_question5 = Configure::read('QUESTION5');
                    $arr_question6 = Configure::read('QUESTION6');
                    $arr_question7 = Configure::read('QUESTION7');
                    $arr_question8 = Configure::read('QUESTION8');
                    $arr_question9 = Configure::read('QUESTION9');
                    $arr_question10 = Configure::read('QUESTION10');
                    $arr_question11 = Configure::read('QUESTION11');
                    for ($i = 0; $i < $total; $i++) {
                        $entityReserve = $ReservesTable->newEntity();
                        $workDate = date('Y-m-d', strtotime($dataReserves[$i]['work_date']));
                        $entityReserve->work_date = $workDate;
                        $workTimeStart = $this->reFormatReserveTime($dataReserves[$i]['work_time_start']);
                        $entityReserve->work_time_start = $workTimeStart;
                        $workTimeEnd = $this->reFormatReserveTime($dataReserves[$i]['work_time_end']);
                        $entityReserve->work_time_end = $workTimeEnd;
                        $conciergeId = $this->findConciergeFromName($dataReserves[$i]['responsible_concierge']);
                        $entityReserve->concierge_id = $conciergeId;
                        $userId = $dataReserves[$i]['user_id'];
                        $entityReserve->user_id = (!is_numeric($userId) || empty($userId)) ? 0 : $userId;
                        $entityReserve->reserve_status = 3;
                        $entityReserve->cancel_status = 0;
                        $entityReserve->reserve_classification = Configure::read('RESERVE_CLASSIFICATION.FROM_RECEPTION');
                        $entityReserve->created_id = $loginId;
                        if (!$ReservesTable->save($entityReserve, ['atomic' => false])) {
                            $ReservesTable->getConnection()->rollback();

                            return false;
                        } else {
                            $csvImportInfoTable = TableRegistry::get('CsvImportInfos');
                            $csvImport = $csvImportInfoTable->newEntity($dataReserves[$i]);
                            $csvImport->reserve_id = $entityReserve->id;
                            if (!$csvImportInfoTable->save($csvImport, ['atomic' => false])) {
                                $ReservesTable->getConnection()->rollback();

                                return false;
                            }
                            $entityNotes = $counselNotesTable->newEntity();
                            $entityNotes->reserve_id = $entityReserve->id;
                            $entityNotes->work_time_start = $this->reFormatReserveTime($dataReserves[$i]['work_time_start']);
                            $entityNotes->work_time_end = $this->reFormatReserveTime($dataReserves[$i]['work_time_end']);
                            $q11 = str_replace(' ', '　', trim($dataReserves[$i]['question1_1']));
                            $entityNotes->question1_1 = array_search($q11, $arr_question5);
                            $q21 = trim($dataReserves[$i]['question2_1']);
                            $entityNotes->question2_1 = array_search($q21, $arr_question6);
                            $q31s = explode(',', $dataReserves[$i]['question3_1']);
                            $q3_1 = [];
                            foreach ($q31s as $q31) {
                                $q31 = trim($q31);
                                if (!empty($q31)) {
                                    $q3_1[] = array_search($q31, $arr_question7);
                                }
                            }
                            $entityNotes->question3_1 = !empty($q3_1) ? implode(',', $q3_1) : '';
                            $entityNotes->question3_1text = $dataReserves[$i]['question3_1text'];
                            $q32s = explode(',', $dataReserves[$i]['question3_2']);
                            $q3_2 = [];
                            foreach ($q32s as $q32) {
                                $q32 = trim($q32);
                                if (!empty($q32)) {
                                    $q3_2[] = array_search($q32, $arr_question8);
                                }
                            }
                            $entityNotes->question3_2 = !empty($q3_2) ? implode(',', $q3_2) : '';
                            $entityNotes->question3_2text = $dataReserves[$i]['question3_2text'];
                            $entityNotes->question4_1 = array_search(trim($dataReserves[$i]['question4_1']), $arr_question9);
                            $entityNotes->question5_1text = $dataReserves[$i]['question5_1text'];
                            $q61s = explode(',', $dataReserves[$i]['question6_1']);
                            $q6_1 = [];
                            foreach ($q61s as $q61) {
                                $q61 = trim($q61);
                                if (!empty($q61)) {
                                    $q6_1[] = array_search($q61, $arr_question10);
                                }
                            }
                            $entityNotes->question6_1 = !empty($q6_1) ? implode(',', $q6_1) : '';
                            $entityNotes->question6_1text = $dataReserves[$i]['question6_1text'];
                            $entityNotes->question7_1 = array_search(trim($dataReserves[$i]['question7_1']), $arr_question11);
                            $entityNotes->question8_1 = array_search(trim($dataReserves[$i]['question8_1']), $arr_question1);
                            $entityNotes->question8_1text = $dataReserves[$i]['question8_1text'];
                            $q82s = explode(',', $dataReserves[$i]['question8_2']);
                            $q8_2 = [];
                            foreach ($q82s as $q82) {
                                $q82 = trim($q82);
                                if (!empty($q82)) {
                                    $q8_2[] = array_search($q82, $arr_question2);
                                }
                            }
                            $entityNotes->question8_2 = !empty($q8_2) ? implode(',', $q8_2) : '';
                            $entityNotes->question8_2text = $dataReserves[$i]['question8_2text'];
                            $q83s = explode(',', $dataReserves[$i]['question8_3']);
                            $q8_3 = [];
                            foreach ($q83s as $q83) {
                                $q83 = str_replace(' ', '　', trim($q83));
                                if (!empty($q83)) {
                                    $q8_3[] = array_search($q83, $arr_question3);
                                }
                            }
                            $entityNotes->question8_3 = !empty($q8_3) ? implode(',', $q8_3) : '';
                            $entityNotes->question8_3text = $dataReserves[$i]['question8_3text'];
                            $q84s = explode(',', $dataReserves[$i]['question8_4']);
                            $q8_4 = [];
                            foreach ($q84s as $q84) {
                                $q84 = str_replace(' ', '　', trim($q84));
                                if (!empty($q84)) {
                                    $q8_4[] = array_search($q84, $arr_question4);
                                }
                            }
                            $entityNotes->question8_4 = !empty($q8_4) ? implode(',', $q8_4) : '';
                            $entityNotes->question8_4text = $dataReserves[$i]['question8_4text'];
                            $entityNotes->question9_1text = $dataReserves[$i]['question9_1text'];
                            $entityNotes->anser1 = $dataReserves[$i]['anser1'];
                            $entityNotes->anser2 = $dataReserves[$i]['anser2'];
                            $entityNotes->anser3 = $dataReserves[$i]['anser3'];
                            $entityNotes->anser4 = $dataReserves[$i]['anser4'];
                            $entityNotes->evaluate1 = !empty($dataReserves[$i]['evaluate1']) ? intval($dataReserves[$i]['evaluate1']) : 0;
                            $entityNotes->evaluate2 = !empty($dataReserves[$i]['evaluate2']) ? intval($dataReserves[$i]['evaluate2']) : 0;
                            $entityNotes->evaluate3 = !empty($dataReserves[$i]['evaluate3']) ? intval($dataReserves[$i]['evaluate3']) : 0;
                            $entityNotes->evaluate4 = !empty($dataReserves[$i]['evaluate4']) ? intval($dataReserves[$i]['evaluate4']) : 0;
                            $entityNotes->evaluate5 = $entityNotes->evaluate1 + $entityNotes->evaluate2 + $entityNotes->evaluate3 + $entityNotes->evaluate4;
                            $entityNotes->preentre_update = strpos($dataReserves[$i]['preentre_update'], Configure::read('RESERVE_CLASSIFICATION.CSV_PREENTRE_VALUE')) !== false ? 1 : 0;

                            if (!$counselNotesTable->save($entityNotes, ['atomic' => false])) {
                                $counselNotesTable->getConnection()->rollback();

                                return false;
                            } else {
                                $total_row_insert ++;
                            }
                        }

                    }

                    return true;
                });
                if ($updateTrans) {
                    $this->Flash->success('Number of records has been inserted: ' . $total_row_insert);
                }
            }
        }
    }
    /**
     * @param string $handle Handle
     * @param array $fieldsarray A record
     * @param string $delimiter Delimiter
     * @param string $enclosure Enclosure
     * @return bool|int
     */
    public function myfputcsv($handle, $fieldsarray, $delimiter = "~", $enclosure = '"')
    {
        $glue = $enclosure . $delimiter . $enclosure;
        $string = preg_replace('~\R~u', "\r\n", PHP_EOL);

        return fwrite($handle, $enclosure . implode($glue, $fieldsarray) . $enclosure . $string);
    }
    /**
     * Find concierge ID from input Name
     * @param array $dataReserves data
     * @param int $total total item name
     * @return int|bool
     */
    protected function validateData($dataReserves, $total)
    {
        $arr_question1 = Configure::read('QUESTION1');
        $arr_question2 = Configure::read('QUESTION2');
        $arr_question3 = Configure::read('QUESTION3');
        $arr_question4 = Configure::read('QUESTION4');
        $arr_question5 = Configure::read('QUESTION5');
        $arr_question6 = Configure::read('QUESTION6');
        $arr_question7 = Configure::read('QUESTION7');
        $arr_question8 = Configure::read('QUESTION8');
        $arr_question9 = Configure::read('QUESTION9');
        $arr_question10 = Configure::read('QUESTION10');
        $arr_question11 = Configure::read('QUESTION11');
        $failData = [];
        for ($i = 0; $i < $total; $i++) {
            // check if concierge is not exists
            $conciergeId = $this->findConciergeFromName($dataReserves[$i]['responsible_concierge']);
            $failed = [];
            if (!$conciergeId) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '2',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '担当コンシェルジュ';
            }
            $q11 = str_replace(' ', '　', trim($dataReserves[$i]['question1_1']));
            if (!empty($q11) && array_search($q11, $arr_question5) === false) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '1. 起業への意思決定状況について教えてください。';
            }
            $q21 = trim($dataReserves[$i]['question2_1']);
            if (!empty($q21) && array_search($q21, $arr_question6) === false) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '2. 現在のお仕事の状況につて教えてください。';
            }
            $q31s = explode(',', $dataReserves[$i]['question3_1']);
            foreach ($q31s as $q31) {
                $q31 = trim($q31);
                if (!empty($q31) && array_search($q31, $arr_question7) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '3. 起業に関する現在の悩みを教えてください。(複数回答可)';
                    break;
                }
            }
            $q32s = explode(',', $dataReserves[$i]['question3_2']);
            foreach ($q32s as $q32) {
                $q32 = trim($q32);
                if (!empty($q32) && array_search($q32, $arr_question8) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '3-会社に必要な機能が足りない。';
                    break;
                }
            }
            $q41 = trim($dataReserves[$i]['question4_1']);
            if (!empty($q41) && array_search($q41, $arr_question9) === false) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '4. 想定している顧客層について教えてください。';
            }
            $q61s = explode(',', $dataReserves[$i]['question6_1']);
            foreach ($q61s as $q61) {
                $q61 = trim($q61);
                if (!empty($q61) && array_search($q61, $arr_question10) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '5. 1 業種について教えてください（複数選択化）';
                    break;
                }
            }
            $q71 = trim($dataReserves[$i]['question7_1']);
            if (!empty($q71) && array_search($q71, $arr_question11) === false) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '6. 第三者に見せるための事業計画(書)の作成状況について教えてください。';
            }
            $q81 = trim($dataReserves[$i]['question8_1']);
            if (!empty($q81) && array_search($q81, $arr_question1) === false) {
                if (empty($failed)) {
                    $failed = [
                        'no' => $i + 1,
                        'type' => '',
                        'record' => $dataReserves[$i],
                    ];
                }
                $failed['message'][] = '7. Startup Hub Tokyo のコンシェルジュサービスをどのようにして知りましたか？';
            }
            $q82s = explode(',', $dataReserves[$i]['question8_2']);
            foreach ($q82s as $q82) {
                $q82 = trim($q82);
                if (!empty($q82) && array_search($q82, $arr_question2) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '７－３第三者からの紹介を受けた。';
                    break;
                }
            }
            $q83s = explode(',', $dataReserves[$i]['question8_3']);
            foreach ($q83s as $q83) {
                $q83 = str_replace(' ', '　', trim($q83));
                if (!empty($q83) && array_search($q83, $arr_question3) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '７－４チラシや広告誌を見て知った。';
                    break;
                }
            }
            $q84s = explode(',', $dataReserves[$i]['question8_4']);
            foreach ($q84s as $q84) {
                $q84 = str_replace(' ', '　', trim($q84));
                if (!empty($q84) && array_search($q84, $arr_question4) === false) {
                    if (empty($failed)) {
                        $failed = [
                            'no' => $i + 1,
                            'type' => '',
                            'record' => $dataReserves[$i],
                        ];
                    }
                    $failed['message'][] = '７－５Web、SNSやメルマガの記事広告を見て知った。';
                    break;
                }
            }
            /*
             * check if user data is not exists
            $userId = $dataReserves[$i]['user_id'];
            if (!is_numeric($userId) || empty($userId) || !$this->checkUserExists(['id' => $userId])) {
                $failData[] = [
                    'no' => $i + 1,
                    'type' => '1',
                    'record' => $dataReserves[$i],
                    'message' => "User (ID) is undefined or not exists."
                ];
                continue;
            }
            */
            if (!empty($failed)) {
                $failData[] = $failed;
            }
        }

        return $failData;
    }
    /**
     * Find concierge ID from input Name
     * @param array $data concierge name
     * @return int|bool
     */
    protected function findConciergeFromName($data)
    {
        $conciergeTable = TableRegistry::get('Concierges');
        $concierge = $conciergeTable->find()
            ->where(['name' => $data, 'avail_flg' => '1']);
        if ($concierge->count() >= 1) {
            return $concierge->first()->id;
        }

        return 0;
    }
    /**
     * Find concierge ID from input Name
     * @param array $condition user id
     * @return int|bool
     */
    protected function checkUserExists($conditions)
    {
        $userTable = TableRegistry::get('Users');
        $user = $userTable->find()
            ->where($conditions);
        if ($user->count() > 0) {
            return true;
        }

        return false;
    }
    /**
     * Reformat reserve time
     * @param string $reserveTime time input 0:0:0
     * @return string
     */
    protected function reFormatReserveTime($reserveTime)
    {
        $reserveTimes = explode(':', $reserveTime);
        if (count($reserveTimes) > 1) {
            return str_pad($reserveTimes[0], 2, '0', STR_PAD_LEFT) . str_pad($reserveTimes[1], 2, '0', STR_PAD_LEFT);
        }

        return '';
    }

    /**
     * import data from csv
     * @param string|null $fileName define filename
     * @param string|null $arrField define data
     * @return array
     */
    protected function importCsvToArray($fileName, $arrField)
    {
        $data = [];
        $handle = fopen($fileName, "r");
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            //mb_convert_variables('utf-8', 'sjis-win', $row);
            $item = [];
            foreach ($header as $k => $head) {
                if (isset($arrField[$k])) {
                    $item[$arrField[$k]] = isset($row[$k]) ? $row[$k] : '';
                    /*
                    if ($item[$arrField[$k]] && strpos($item[$arrField[$k]], '""') !== false) {
                        $item[$arrField[$k]] = str_replace('""', '"', $item[$arrField[$k]]);
                    }
                    */
                }
            }
            array_push($data, $item);
        }
        fclose($handle);

        return $data;
    }

    /**
     * preg_replace string csv
     * @param string|null $content data will be re-format
     * @return string
     */
    private function pregReplaceCsv($content)
    {
        if (!empty($content)) {
            return preg_replace("/[\"]+/", '""', $content);
        }

        return '';
    }
}
