<?php

/**
 * Shift Controller
 * @author: Dat
 **/

namespace App\Controller\Management;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Email\Email;
use Cake\ORM\TableRegistry;

class ShiftController extends ManagementController
{
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
     * @param string|null $start Start date
     * @author: Dat
     * @return \Cake\Network\Response|void : boolean
     */
    public function index($start = null)
    {
        $session = $this->request->session();
        $sessionID = ($session->check('LOGIN.ID'))?$session->read('LOGIN.ID'):0;

        // Make date
        $isMonday = date('D', strtotime($start));
        if ($start != null && $isMonday == 'Mon') {
            $initsdate = Date('Y/m/d', strtotime($start));
            $initedate = Date('Y/m/d', strtotime('+6 days', strtotime($start)));
        } else {
            $initsdate = Date('Y/m/d', strtotime('monday this week'));
            $initedate = Date('Y/m/d', strtotime('sunday this week'));
        }

        $this->set(compact('initsdate', 'initedate'));
        $conciersTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $ReservesTable = TableRegistry::get('Reserves');

        // Delete, Edit, Add
        if ($this->request->is('post')) {
            $data = $this->request->data;
            // pr($data);
            $arrMail = [];
            $countMail = 0;
            $updateTransaction = $ShiftWorkTable->getConnection()->transactional(function () use ($data, $ShiftWorkTable, $initsdate, $sessionID, &$arrMail, &$countMail, $ReservesTable) {
                for ($hour = 0; $hour < 12; $hour++) {
                    for ($day = 0; $day < 7; $day++) {
                        $name = date('Y-m-d', strtotime('+' . $day . ' days', strtotime($initsdate))) . '_' . ('10' + $hour);
                        $date = date('Y-m-d', strtotime('+' . $day . ' days', strtotime($initsdate)));
                        $time = ('10' + $hour) . '00';
                        $etime = ('10' + $hour) . '40';
                        if (isset($data[$name])) {
                            $cntsize = count($data[$name]);
                            $arr_add = [];
                            $arr_edit = [];
                            $arr_del = [];
                            $i_add = 0;
                            $i_edit = 0;
                            $i_del = 0;
                            for ($cnt = 0; $cnt < $cntsize; $cnt++) {
                                $shift = $data[$name][$cnt];
                                $hid_Arr = $data[$name . '_Arr'][$cnt];
                                $hid_Arr = explode('_', $hid_Arr);

                                // debug($hid_Arr);
                                if ($hid_Arr[0] == '0') {
                                    $arr_add[$i_add]['id'] = 0;
                                    $arr_add[$i_add]['shift'] = $shift;
                                    $i_add++;
                                } else {
                                    if ($hid_Arr[1] == '1') { // edit
                                        $arr_edit[$i_edit]['id'] = $hid_Arr[0];
                                        $arr_edit[$i_edit]['shift'] = $shift;
                                        $i_edit++;
                                    } elseif ($hid_Arr[1] == '2') { // delete
                                        $arr_del[$i_del]['id'] = $hid_Arr[0];
                                        $arr_del[$i_del]['shift'] = $shift;
                                        $i_del++;
                                    }
                                }
                            }
                            //del
                            if (count($arr_del)) {
                                foreach ($arr_del as $row) {
                                    if ($row['id'] != null) {
                                        $entity = $ShiftWorkTable->get($row['id']);
                                        if (!$ShiftWorkTable->delete($entity, ['atomic' => false])) {
                                            $ShiftWorkTable->getConnection()->rollback();

                                            return false;
                                        }
                                    }
                                }
                            }
                            //edit
                            if (count($arr_edit)) {
                                $data_bk = [];
                                foreach ($arr_edit as $row) {
                                    if ($row['shift'] > 0) {
                                        $entity = $ShiftWorkTable->get($row['id']);
                                        $data_bk[$row['id']] = $entity;
                                        if (!$ShiftWorkTable->delete($entity, ['atomic' => false])) {
                                            $ShiftWorkTable->getConnection()->rollback();

                                            return false;
                                        }
                                    }
                                }
                                foreach ($arr_edit as $row) {
                                    if ($row['shift'] > 0) {
                                        $oldData = $data_bk[$row['id']];

                                        $Qadd = $ShiftWorkTable->newEntity();
                                        $Qadd->id = $oldData->id;
                                        $Qadd->work_date = $oldData->work_date;
                                        $Qadd->work_time_start = $oldData->work_time_start;
                                        $Qadd->work_time_end = $oldData->work_time_end;

                                        if ($row['shift'] != $oldData->concierge_id) {
                                            $Qadd->work_time_end = $etime;
                                        }

                                        $Qadd->concierge_id = $row['shift'];
                                        $Qadd->status = $oldData->status;
                                        $Qadd->created_id = $oldData->created_id;
                                        $Qadd->created_date = $oldData->created_date;
                                        $Qadd->modified_id = $sessionID;
                                        $Qadd->modified_date = ($row['shift'] != $oldData->concierge_id) ? date('Y-m-d H:i:s') : $oldData->modified_date;
                                        $Qadd->tmp_reserve_date = $oldData->tmp_reserve_date;
                                        $Qadd->tmp_reserve_id = $oldData->tmp_reserve_id;
                                        if (!$ShiftWorkTable->save($Qadd, ['atomic' => false])) {
                                            $ShiftWorkTable->getConnection()->rollback();
                                            //echo $name . '<br>';

                                            return false;
                                        }
                                        if ($row['shift'] != $oldData->concierge_id && $oldData->status == 1) {
                                            // New update reserves
                                            if ($ShiftWorkTable->save($Qadd, ['atomic' => false])) {
                                                $QURe = $ReservesTable->updateAll(['concierge_id' => $row['shift'], 'modified_id' => $sessionID, 'modified_date' => date('Y-m-d H:i:s')], ['work_date' => $oldData->work_date, 'work_time_start' => $oldData->work_time_start, 'concierge_id' => $oldData->concierge_id]);
                                                if (!$QURe) {
                                                    $ShiftWorkTable->getConnection()->rollback();

                                                    return false;
                                                }
                                            }

                                            $arrMail[$countMail]['c_id'] = $row['shift'];
                                            $arrMail[$countMail]['s_id'] = $oldData->id;
                                            $arrMail[$countMail]['date'] = date('Y/m/d', strtotime('+' . $day . ' days', strtotime($initsdate)));
                                            $arrMail[$countMail]['time'] = substr($time, 0, 2) . ':' . substr($time, 2, 2);
                                            $arrMail[$countMail]['timeend'] = substr($oldData->work_time_end, 0, 2) . ':' . substr($oldData->work_time_end, 2, 2);
                                            $arrMail[$countMail++]['current_concierge'] = $oldData->concierge_id;
                                        }
                                    }
                                }
                            }
                            //add
                            if (count($arr_add)) {
                                foreach ($arr_add as $row) {
                                    if ($row['shift'] > 0) {
                                        $Qadd = $ShiftWorkTable->newEntity();
                                        $Qadd->work_date = $date;
                                        $Qadd->work_time_start = $time;
                                        $Qadd->work_time_end = $etime;
                                        $Qadd->concierge_id = $row['shift'];
                                        $Qadd->status = '0';
                                        $Qadd->created_id = $sessionID;
                                        $Qadd->modified_id = $sessionID;

                                        $Qadd->modified_date = date('Y-m-d H:i:s');
                                        if (!$ShiftWorkTable->save($Qadd, ['atomic' => false])) {
                                            $ShiftWorkTable->getConnection()->rollback();
                                            //echo $name . '<br>';

                                            return false;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                return true;
            });
            if ($updateTransaction) {
                for ($i = 0; $i < $countMail; $i++) {
                    $queryName = $ShiftWorkTable->find()->select(['email' => 'Users.email', 'user_name' => 'CONCAT(name_last, name_first)'])
                        ->join(['Reserves' => ['table' => 'reserves', 'type' => 'INNER',
                            'conditions' => [
                                'Reserves.concierge_id = ShiftWorks.concierge_id',
                                'ShiftWorks.work_date = Reserves.work_date',
                                'ShiftWorks.work_time_start = Reserves.work_time_start',
                            ]]])
                        ->join(['Users' => ['table' => 'users', 'type' => 'INNER',
                            'conditions' => 'Reserves.user_id = Users.id']])
                        ->where(['ShiftWorks.id' => $arrMail[$i]['s_id'], 'Reserves.reserve_status !=' => '9']);

                    if ($queryName->count() > 0) {
                        $queryName = $queryName->first();
                        $queryNameCurrent = $conciersTable->find()->select(['name' => 'Concierges.name'])
                            ->where(['Concierges.avail_flg' => 1, 'Concierges.id' => $arrMail[$i]['current_concierge']])->first();

                        $queryNameNew = $conciersTable->find()->select(['name' => 'Concierges.name'])
                            ->where(['Concierges.avail_flg' => 1, 'Concierges.id' => $arrMail[$i]['c_id']])->first();

                        $mailinfos['user_name'] = $queryName->user_name;
                        $mailinfos['email'] = $queryName->email;
                        $mailinfos['work_date'] = $arrMail[$i]['date'];
                        $mailinfos['work_start_time'] = $arrMail[$i]['time'];
                        $mailinfos['work_end_time'] = $arrMail[$i]['timeend'];
                        $mailinfos['pre_concierge_name'] = $queryNameCurrent->name;
                        $mailinfos['new_concierge_name'] = $queryNameNew->name;
                        if ($mailinfos['email']) {
                            $this->sendEmail(
                                Configure::read('from_mailaddress'),
                                $mailinfos['email'],
                                '【Startup Hub Tokyo】コンシェルジュ変更のお知らせ。',
                                $mailinfos,
                                'email_concierges_update',
                                null,
                                Configure::read('admin_mailaddress')
                            );
                        }
                    }
                }
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_033'));
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }
        }

        // Make list Concierges to show
        $queryConcier = $conciersTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])->where(['avail_flg' => '1'])->order(['id' => 'ASC']);
        $data = $queryConcier->toArray();
        $arrConcier = ['0' => ''] + $data;
        $this->set(compact('arrConcier'));

        // Query data from DB
        $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name', 'cid' => 'Concierges.id',
            'sid' => 'ShiftWorks.id',
            'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start', 'status' => 'ShiftWorks.status'])
            ->join(['Concierges' => [
                'table' => 'concierges',
                'type' => 'LEFT',
                'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
            ->where(['Concierges.avail_flg' => 1, 'ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate]);
        // debug($queryShift->toArray());

        $dataRender = [];
        $count = [];
        foreach ($queryShift as $q) {
            $date = date('Y-m-d', strtotime($q->work_date));
            $time = $q->work_time_start;
            // $time = substr($q->work_time_start, 0, 2) . '00';
            if (!isset($dataRender[$date][$time])) {
                $count[$date . $time] = 0;
            } else {
                $count[$date . $time]++;
            }

            $k = $count[$date . $time];

            $dataRender[$date][$time][$k]['cid'] = $q->cid;
            $dataRender[$date][$time][$k]['status'] = $q->status;
            $dataRender[$date][$time][$k]['sid'] = $q->sid;
        }

        // debug($dataRender);
        $this->set(compact('queryShift'));
        $this->set(compact('dataRender'));
    }
}
