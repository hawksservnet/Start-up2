<?php
/**
 * IndexController Controller
 **/

namespace App\Controller\Concierge;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class IndexController extends ConciergeController
{
    public $ID = -1;
    /**
     * Index method
     * @author TuanPS
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        //var_dump($this->request);
        $this->checkAuth();
        $id = (int)$this->request->getQuery('id');
        $dt = $this->request->getQuery('dt');
        if (isset($dt)) {
            //   if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dt)) {
            if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dt)) {
                $date = date('Y/m/d', strtotime($dt));
                $this->set('currentTime', $date);
                $initsdate = date('Y/m/d', strtotime('first day of this month', strtotime($date)));
                $initedate = date('Y/m/d', strtotime('last day of this month', strtotime($date)));
            } else {
                $now = new \DateTime();
                $this->set('currentTime', $now->format('Y-m-d'));
                $initsdate = date('Y/m/d', strtotime('first day of this month', strtotime($now->format('Y-m-d'))));
                $initedate = date('Y/m/d', strtotime('last day of this month', strtotime($now->format('Y-m-d'))));
            }
        } else {
            $now = new \DateTime();
            $this->set('currentTime', $now->format('Y-m-d'));
            $initsdate = date('Y/m/d', strtotime('first day of this month', strtotime($now->format('Y-m-d'))));
            $initedate = date('Y/m/d', strtotime('last day of this month', strtotime($now->format('Y-m-d'))));
        }
        //$tmprsvdate = date('Y-m-d H:i:s', strtotime('-10 minutes'));

        //$d->modify('last day of next month');
        //$endNextMonth = $d->format('Y-m-d');

        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $reservesTable = TableRegistry::get('Reserves');
        $conciergesQuery = $conciergesTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])
            ->where(['avail_flg' => 1])
            ->order(['sort_no' => 'ASC', 'id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;
        $array_query = [['Concierges.avail_flg' => 1]];

        $queryconcierges = $conciergesTable->find()->select(['id' => 'Concierges.id', 'title' => 'Concierges.name'])

            ->join(['ShiftWorks' => [
                'table' => 'shift_works',
                'type' => 'INNER',
                'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
            ->where(['avail_flg' => 1])
            ->andWhere(['ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate ])->distinct()
            ->order(['id' => 'ASC']);
        $now = new \DateTime();
        // Delete, Edit, Add
        if ($id > 0) {
            array_push($array_query, [['Concierges.id' => $id]]);
        }
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name', 'sid' => 'ShiftWorks.id', 'cid' => 'Concierges.id',
                'work_date' => 'ShiftWorks.work_date',
            'status' => 'ShiftWorks.status',
            'work_time_start' => 'ShiftWorks.work_time_start',
                'name' => 'Concierges.name', 'tmp_reserve_date' => 'ShiftWorks.tmp_reserve_date',
                'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
                'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
                'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end'
            ])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'INNER',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->where([
                    'ShiftWorks.work_date >=' => $initsdate,
                    'ShiftWorks.work_date <= ' => $initedate,
                    /*'ShiftWorks.tmp_reserve_date <= ' => $tmprsvdate, */
                    $array_query
                ])
                ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . $now->format('i') . " ) )")
                ->order(['ShiftWorks.work_date' => 'ASC', 'status_order' => 'ASC', 'ShiftWorks.work_time_start' => 'ASC', 'Concierges.id' => 'ASC' ]);

        //query data form data
        $mconcierges = $this->getConcierges();
        $reservesstatus = $this->reservesstatus($this->ID, $initsdate);
        if ($reservesstatus == 0) {
            $this->Flash->error('１か月の利用回数（同月内４回まで）を超えております。当月の追加の予約はできません。');
        }
        $d = new \DateTime();
        $d->modify('first day of next month');
        $firstNextMonth = $d->format('Y-m-d');
        $reservesstatusCurentMonth = $this->reservesstatus($this->ID, $now->format('Y-m-d'));
        $reservesstatusNextMonth = $this->reservesstatus($this->ID, $firstNextMonth);
        $this->set('reservesstatus', $reservesstatus);
        $this->set('reservesstatusNextMonth', $reservesstatusNextMonth);
        $filePath = Configure::read('thumbnail_img_path');
        $this->set('filePath', $filePath);
        $this->set('reservesstatusCurentMonth', $reservesstatusCurentMonth);
        $this->set('concierges', $mconcierges);
        $this->set(compact('arrconcierges'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
        $this->set(compact('queryconcierges'));
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
        if (Configure::read('superuser_id') == $this->getUserEmail($id)) {
            $reservesstatus = 1;

            return $reservesstatus;
        }
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
     * @param int $id of user
     * @return string
     */
    private function getUserEmail($id)
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
     * Check auth of user login
     * @author tuanvu
     * @return \Cake\Network\Response|void : boolean
     */
    private function checkAuth()
    {
        $session = $this->request->session();
        $this->ID = ($session->check('MYPAGE.ID'))?$session->read('MYPAGE.ID'):-1;
        if ($this->ID == -1) {
            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'users/login?h=104');
            //$this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));
        }
        $user_id = $this->ID;
        $this->set(compact('user_id'));
    }
}
