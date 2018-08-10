<?php

namespace App\Controller\Management;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Email\Email;
use Cake\ORM\TableRegistry;

class CounselNoteController extends ManagementController
{
    /**
     * Index page /admin/counsel_note/info
     * @return \Cake\Network\Response|void : boolean
     * @author: ThuanLe
     */
    public function info()
    {
        $user_id = (int)$this->request->getQuery('id');
        $dt = $this->request->getQuery('dt');
        $cn = (int)$this->request->getQuery('cn');
        $rs = (int)$this->request->getQuery('rs');

        if (!$user_id) {
            return $this->redirect(['controller' => 'reserve']);
        }

        $user = TableRegistry::get('Users')->find()
            ->select(['Users.id', 'Users.type', 'Users.name_last', 'Users.name_first', 'Users.nationality', 'Users.pref', 'Users.birthday', 'Users.created_at', 'Users.interest', 'Users.preparation', 'PreentreRequests.id', 'PreentreRequests.intention', 'PreentreRequests.business_type', 'PreentreRequests.business_type_text', 'PreentreRequests.business_style', 'PreentreRequests.business_style_text', 'PreentreRequests.problem01', 'PreentreRequests.problem02', 'PreentreRequests.problem03', 'PreentreRequests.problem04', 'PreentreRequests.problem05', 'PreentreRequests.problem06', 'PreentreRequests.problem07', 'PreentreRequests.problem08', 'PreentreRequests.problem09', 'PreentreRequests.problem10', 'PreentreRequests.problem11', 'PreentreRequests.problem12', 'PreentreRequests.problem13', 'PreentreRequests.problem14', 'PreentreRequests.problem15', 'PreentreRequests.problem16', 'PreentreRequests.problem17', 'PreentreRequests.problem18', 'PreentreRequests.problem19', 'PreentreRequests.problem20', 'PreentreRequests.problem21', 'PreentreRequests.problem22', 'PreentreRequests.problem99', 'PreentreRequests.problem_text', 'PreentreRequests.wish01', 'PreentreRequests.wish02', 'PreentreRequests.wish03', 'PreentreRequests.wish04', 'PreentreRequests.wish05', 'PreentreRequests.wish06', 'PreentreRequests.wish07', 'PreentreRequests.wish08', 'PreentreRequests.wish'])
            ->join([
                "PreentreRequests" => [
                    'table' => 'preentre_requests',
                    'type' => 'LEFT',
                    'conditions' => ["PreentreRequests.user_id = Users.id"],
                ]
            ])
            ->where(['Users.id' => $user_id])
            ->order(['Users.id' => 'DESC'])->first();
        if (!count($user)) {
            return $this->redirect(['controller' => 'reserve']);
        }

        $reserves = TableRegistry::get('Reserves')->find()
            ->select(['id', 'work_date', 'work_time_start'])
            ->where(['Reserves.user_id' => $user_id])
            ->order(['work_date' => 'DESC']);
        $this->set(['user' => $user, 'reserves' => $reserves, 'rs' => $rs, 'cn' => $cn, 'user_id' => $user_id]);
    }

    /**
     * Index page /admin/counsel_note/note
     * @return \Cake\Network\Response|void : boolean
     * @author: ThuanLe
     */
    public function note()
    {
        $user_id = (int)$this->request->getQuery('id');
        $reserves_id = (int)$this->request->getQuery('rs');

        $session = $this->request->session();
        $loginId = ($session->check('LOGIN.ID')) ? $session->read('LOGIN.ID') : 0;

        if (!isset($user_id)) {
            return $this->redirect(['controller' => 'reserve']);
        }
        if (!$reserves_id) {
            $cn = (int)$this->request->getQuery('cn');

            return $this->redirect([
                'controller' => 'CounselNote',
                'action' => 'info',
                '?' => ['id' => $user_id, 'cn' => !empty($cn) ? $cn : '']
            ]);
        }
        if ($this->request->is('post')) {
            $mailinfos = [];
            $data = $this->request->data;
            if (!$data['status'] || !$data['work_time_start'] || !$data['work_time_end']) {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));

                return $this->redirect(['controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => $data['id'], 'rs' => $data['rs']]]);
            }
            if ($data['status'] == 'r_3') {
                if (!$data['anser1'] || !$data['anser2'] || !$data['anser3'] || !$data['anser4'] || !is_numeric($data['evaluate1']) || $data['evaluate1'] > 5 || !is_numeric($data['evaluate2']) || $data['evaluate2'] > 5 || !is_numeric($data['evaluate3']) || $data['evaluate3'] > 5 || !is_numeric($data['evaluate4']) || $data['evaluate4'] > 5 || !is_numeric($data['evaluate5']) || $data['evaluate5'] > 20) {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));

                    return $this->redirect(['controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => $data['id'], 'rs' => $data['rs']]]);
                }
            }

            $reservesTable = TableRegistry::get('Reserves');
            $shiftWorksTable = TableRegistry::get('ShiftWorks');
            $counselNotesTable = TableRegistry::get('CounselNotes');
            $updateTransaction = $reservesTable->getConnection()->transactional(function () use ($data, $reservesTable, $shiftWorksTable, $counselNotesTable, $loginId) {
                $type = explode('_', $data['status']);
                $status = $type[1];
                $type = $type[0];
                try {
                    $entity = $reservesTable->get($data['rs']);
                    if ($type == 'c') {
                        $entity->cancel_status = $status;
                        $entity->reserve_status = 9;
                        $entity->cancel_date = date('Y-m-d H:i:s');
                    } elseif ($type == 'r') {
                        $entity->reserve_status = $status;
                    }
                    $entity->modified_id = $loginId;
                    $entity->modified_date = date('Y-m-d H:i:s');
                    if (!$reservesTable->save($entity, ['atomic' => false])) {
                        $reservesTable->getConnection()->rollback();

                        return false;
                    }
                } catch (RecordNotFoundException $e) {
                    $reservesTable->getConnection()->rollback();

                    return false;
                }

                $status_shift = 1;
                if ($type == 'c') {
                    $status_shift = 0;
                }
                $entity_s = $shiftWorksTable->find('all')
                    ->where(['concierge_id' => $entity->concierge_id, 'work_date' => date('Y-m-d', strtotime($entity->work_date)), 'work_time_start' => $entity->work_time_start])->first();
                if (!empty($entity_s->id)) {
                    $entity_s->status = $status_shift;
                    $entity_s->modified_id = $loginId;
                    $entity_s->modified_date = date('Y-m-d H:i:s');
                    if (!$shiftWorksTable->save($entity_s, ['atomic' => false])) {
                        $reservesTable->getConnection()->rollback();

                        return false;
                    }
                } else {
                    if ($entity->reserve_classification == Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')) {
                        $reservesTable->getConnection()->rollback();

                        return false;
                    }
                }
                try {
                    $data_save['work_time_start'] = str_replace(':', '', $data['work_time_start']);
                    $data_save['work_time_end'] = str_replace(':', '', $data['work_time_end']);
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
                    $data_save['question5_1text'] = $data['question5_1text'];
                    $data_save['question6_1text'] = '';
                    if (strpos(',' . $data_save['question6_1'] . ',', ',14,') !== false) {
                        $data_save['question6_1text'] = $data['question6_1text'];
                    }
                    $data_save['question9_1text'] = $data['question9_1text'];
                    $data_save['question7_1'] = $data['question7_1'];
                    $data_save['preentre_update'] = isset($data['preentre_update']) ? $data['preentre_update'] : 0;

                    $entity_cn = $counselNotesTable->get($data['counsel_notes_id']);
                    $entity_cn->reserve_id = $data['rs'];
                    $entity_cn->work_time_start = $data_save['work_time_start'];
                    $entity_cn->work_time_end = $data_save['work_time_end'];
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
                    $entity_cn->question5_1text = $data_save['question5_1text'];
                    $entity_cn->question6_1text = $data_save['question6_1text'];
                    $entity_cn->question9_1text = $data_save['question9_1text'];
                    $entity_cn->question7_1 = $data_save['question7_1'];
                    $entity_cn->preentre_update = $data_save['preentre_update'];
                    $entity_cn->anser1 = $data['anser1'];
                    $entity_cn->anser2 = $data['anser2'];
                    $entity_cn->anser3 = $data['anser3'];
                    $entity_cn->anser4 = $data['anser4'];
                    $entity_cn->evaluate1 = !empty($data['evaluate1']) ? $data['evaluate1'] : 0;
                    $entity_cn->evaluate2 = !empty($data['evaluate2']) ? $data['evaluate2'] : 0;
                    $entity_cn->evaluate3 = !empty($data['evaluate3']) ? $data['evaluate3'] : 0;
                    $entity_cn->evaluate4 = !empty($data['evaluate4']) ? $data['evaluate4'] : 0;
                    $entity_cn->evaluate5 = !empty($data['evaluate5']) ? $data['evaluate5'] : 0;
                    $entity_cn->modified_id = $loginId;
                    $entity_cn->modified_date = date('Y-m-d H:i:s');

                    if (!$counselNotesTable->save($entity_cn, ['atomic' => false])) {
                        $reservesTable->getConnection()->rollback();

                        return false;
                    }
                } catch (RecordNotFoundException $e) {
                    $reservesTable->getConnection()->rollback();

                    return false;
                }

                return true;
            });
            if ($updateTransaction == true) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
                if ($data['status'] == 'c_9') {
                    /*Data Send Email*/
                    $ConciergesTable = TableRegistry::get('Concierges');
                    $UsersTable = TableRegistry::get('Users');
                    $entity = $reservesTable->get($data['rs']);
                    $Qcon = $ConciergesTable->get($entity->concierge_id);
                    $Quser = $UsersTable->get($data['id']);
                    $mailinfos['user_name'] = $Quser->name_last . ' ' . $Quser->name_first;
                    $mailinfos['work_date'] = $entity->work_date;
                    $mailinfos['work_time_start'] = $entity->work_time_start;
                    $mailinfos['work_time_end'] = $entity->work_time_end;
                    $mailinfos['comment'] = $data['comment']?$data['comment']:'';
                    $mailinfos['pre_concierge_name'] = $Qcon->name;
                    $mailinfos['email'] = $Quser->email;
                    if ($mailinfos['email']) {
                        $this->sendEmail(
                            Configure::read('from_mailaddress'),
                            $mailinfos['email'],
                            '【スタートアップハブ東京】問診キャンセルのお知らせ',
                            $mailinfos,
                            'email_reserves_update',
                            null,
                            Configure::read('admin_mailaddress')
                        );
                    }
                }
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }

            return $this->redirect(['controller' => 'CounselNote', 'action' => 'note', '?' => ['id' => $data['id'], 'rs' => $data['rs']]]);
        }

        $reserve_data = TableRegistry::get('Reserves')->find()
            ->select(['Users.id', 'Users.type', 'Users.name_last', 'Users.name_first',
                'Reserves.id', 'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_end', 'Reserves.reserve_classification', 'Reserves.reserve_status', 'Reserves.cancel_status', 'Reserves.concierge_id',
                'Concierges.name',
                'CounselNotes.id', 'CounselNotes.work_time_start', 'CounselNotes.work_time_end', 'CounselNotes.achieve', 'CounselNotes.achieve_text',
                'CounselNotes.question1_1', 'CounselNotes.question2_1', 'CounselNotes.question2_1text', 'CounselNotes.question3_1', 'CounselNotes.question3_1text',
                'CounselNotes.question3_2', 'CounselNotes.question3_2text', 'CounselNotes.question4_1', 'CounselNotes.question5_1text',
                'CounselNotes.question6_1text', 'CounselNotes.question9_1text', 'CounselNotes.question6_1', 'CounselNotes.question7_1',
                'CounselNotes.question8_1', 'CounselNotes.question8_1text', 'CounselNotes.question8_2', 'CounselNotes.question8_2text',
                'CounselNotes.question8_3', 'CounselNotes.question8_3text', 'CounselNotes.question8_4', 'CounselNotes.question8_4text',
                'CounselNotes.anser1', 'CounselNotes.anser2', 'CounselNotes.anser3', 'CounselNotes.anser4', 'CounselNotes.evaluate1',
                'CounselNotes.evaluate2', 'CounselNotes.evaluate3', 'CounselNotes.evaluate4', 'CounselNotes.evaluate5', 'CounselNotes.preentre_update'])
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
            ->join(['ShiftWorks' => ['table' => 'shift_works',
                'type' => 'LEFT',
                'conditions' => ['Reserves.work_date = ShiftWorks.work_date', 'Reserves.work_time_start = ShiftWorks.work_time_start', 'Reserves.concierge_id = ShiftWorks.concierge_id']],
                'Concierges' => ['table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => ['Concierges.avail_flg = 1', 'Concierges.id = Reserves.concierge_id']],
                'CounselNotes' => ['table' => 'counsel_notes',
                    'type' => 'LEFT',
                    'conditions' => ['Reserves.id = CounselNotes.reserve_id']],
                'Users' => ['table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => ['Reserves.user_id = Users.id']]
            ])
            ->where(['Reserves.id' => $reserves_id, 'Reserves.user_id' => $user_id])
            ->order(['Reserves.id' => 'DESC'])->first();
        if (empty($reserve_data->id)) {
            return $this->redirect(['controller' => 'reserve']);
        }

        $reserves = TableRegistry::get('Reserves')->find()
            ->select(['id', 'work_date', 'work_time_start', 'reserve_status'])
//            ->where(['Reserves.concierge_id' => $reserve_data['concierge_id'], 'Reserves.user_id' => $user_id])
            ->where(['Reserves.user_id' => $user_id])
            ->order(['work_date' => 'DESC', 'Reserves.work_time_start' => 'DESC', 'Reserves.reserve_status' => 'ASC', 'Reserves.user_id' => 'ASC', 'Reserves.id' => 'DESC']);

        $this->set(['reserve_data' => $reserve_data, 'reserves' => $reserves, 'page_current' => $user_id . '_' . $reserves_id, 'rs' => $reserves_id]);
    }
}
