<?php
namespace App\Controller\Concierge;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

/**
 * Schedule Controller
 *
 *
 * @method \App\Model\Entity\Schedule[] paginate($object = null, array $settings = [])
 */
class ScheduleController extends ConciergeController
{

    public $ID = -1;
    private $NAME = "";

    /**
    Check auth of user login
     * @author thovo
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
    /**
    Check auth of user Reserves Status
     * @author thovo
     * @param int $id of user
     * @param string $date day of month
     * @param bool $msgflg Flag
     * @param string $addtext More Text
     * @return \Cake\Network\Response|void : object
     */
    private function reservesstatus($id, $date, $msgflg = true, $addtext = "")
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

        if ($addtext == "") {
            $addtext = "１か月";
        }

        if ($total >= 4) {
            $reservesstatus = 0;
            if ($msgflg) {
                $this->Flash->error($addtext . 'の利用回数（同月内４回まで）を超えております。当月の追加の予約はできません。');
            }
        } else {
            $reservesstatus = 1;
        }
        //$this->set(compact('reservesstatus'));
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
     * Show Month
     * Index page /management/schedule/month
     * @query string|null $id id of Concierges $dt dateime
     * @author ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function month()
    {
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

        $queryconcierges = $conciergesTable->find()->select(['id' => 'Concierges.id', 'title' => 'Concierges.name'])

            ->join(['ShiftWorks' => [
                'table' => 'shift_works',
                'type' => 'INNER',
                'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
            ->where(['avail_flg' => 1])
            ->andWhere(['ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate ])->distinct()
            ->order(['id' => 'ASC']);
        $now = new \DateTime();
        $array_query = [['Concierges.avail_flg' => 1]];
        // Delete, Edit, Add
        if ($id > 0) {
            array_push($array_query, [['Concierges.id' => $id]]);
        }
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
            // Query data from DB
            $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name', 'sid' => 'ShiftWorks.id',
                'work_date' => 'ShiftWorks.work_date',
                'status' => 'ShiftWorks.status',
                'work_time_start' => 'ShiftWorks.work_time_start', 'name' => 'Concierges.name',
                'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
                'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
               'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
            ])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'INNER',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->where([
                    'ShiftWorks.work_date >=' => $initsdate,
                    'ShiftWorks.work_date <= ' => $initedate,
                    /*'ShiftWorks.tmp_reserve_date <= ' => $tmprsvdate,*/
                    $array_query
                ])
                ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . "00 ) )")
                ->order(['ShiftWorks.work_date' => 'ASC', 'status_order' => 'ASC', 'ShiftWorks.work_time_start' => 'ASC', 'Concierges.id' => 'ASC' ]);

        $reservesstatus = $this->reservesstatus($this->ID, $initsdate);
        $this->set(compact('reservesstatus'));
        $this->set(compact('arrconcierges'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
        $this->set(compact('queryconcierges'));
    }

    /**
     * Show Week
     * Index page /management/schedule/week/
     * @query string|null $id id of Concierges $dt datetime
     * @author ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function week()
    {
        $this->checkAuth();
        $id = (int)$this->request->getQuery('id');
        $dt = $this->request->getQuery('dt');
        // Make date
        if (isset($dt)) {
            try {
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dt)) {
                    $dayofweek = date('w', strtotime($dt));
                    $date = Date('Y/m/d', strtotime($dt));
                    $this->set('currentTime', $date);
                    if ($dayofweek == 1) {
                        $initsdate = date('Y/m/d', strtotime($dt));
                        $initedate = date('Y/m/d', strtotime('+6 days', strtotime($dt)));
                    } else {
                        $initsdate = date('Y/m/d', strtotime('+' . (1 - $dayofweek) . ' days', strtotime($dt)));
                        $initedate = date('Y/m/d', strtotime('+6 days', strtotime($initsdate)));
                    }
                } else {
                    $initsdate = date('Y/m/d', strtotime('monday this week'));
                    $initedate = date('Y/m/d', strtotime('sunday this week'));
                }
            } catch (\Exception $e) {
                $initsdate = date('Y/m/d', strtotime('monday this week'));
                $initedate = date('Y/m/d', strtotime('sunday this week'));
            }
        } else {
            $this->set('currentTime', Date('Y/m/d'));
            $initsdate = date('Y/m/d', strtotime('monday this week'));
            $initedate = date('Y/m/d', strtotime('sunday this week'));
        }
        $initsmonth = date('m', strtotime($initsdate));
        $initemonth = date('m', strtotime($initedate));

        //$tmprsvdate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $now = new \DateTime();
        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $queryconcierges = $conciergesTable->find()->select(['id' => 'Concierges.id', 'title' => 'Concierges.name'])

            ->join(['ShiftWorks' => [
                'table' => 'shift_works',
                'type' => 'INNER',
                'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
            ->where(['avail_flg' => 1])
            ->andWhere(['ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate ])
            ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . $now->format('i') . " ) )")
            ->distinct()
            ->order(['id' => 'ASC']);
        $conciergesQuery = $conciergesTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])
            ->where(['avail_flg' => 1])
            ->order(['sort_no' => 'ASC', 'id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;
        $array_query = [];
        if ($id > 0) {
            array_push($array_query, [['Concierges.id' => $id ]]);
        }
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        // Query data from DB
        /** @var TYPE_NAME $queryShift */
        $queryShift = $ShiftWorkTable->find()->select([
            'name' => 'Concierges.name',
            'cid' => 'Concierges.id', 'sid' => 'ShiftWorks.id',
            'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start',
            'status' => 'ShiftWorks.status',
            'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
            'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
            'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
        ])
            ->join(['Concierges' => [
                'table' => 'concierges',
                'type' => 'INNER',
                'conditions' => [
                    'ShiftWorks.concierge_id = Concierges.id',
                    'Concierges.avail_flg = 1'
                ]]])
            ->where([
                'ShiftWorks.work_date >=' => $initsdate,
                'ShiftWorks.work_date <=' => $initedate,
                /*'ShiftWorks.tmp_reserve_date <= ' => $tmprsvdate,*/
                $array_query
            ])
            ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . $now->format('i') . " ) )")
            ->order(['ShiftWorks.work_date' => 'ASC', 'status_order' => 'ASC']);
/*
        $reservesstatus = $this->reservesstatus($this->ID, $initsdate);
        //メッセージセット済
        if ($reservesstatus == 0) {
            $msgflg = false;
        } else {
            $msgflg = true;
        }
        $reservesstatus2 = $this->reservesstatus($this->ID, $initedate, $msgflg);
*/
        $reservesstatus = $this->reservesstatus($this->ID, $initsdate, true, date('m', strtotime($initsdate)) . "月");
        if (date('m', strtotime($initsdate)) == date('m', strtotime($initedate))) {
            $reservesstatus2 = 0;
        } else {
            $reservesstatus2 = $this->reservesstatus($this->ID, $initedate, true, date('m', strtotime($initedate)) . "月");
        }
        $arr = [];
        if (count($queryShift->toArray())) {
            foreach ($queryShift as $item) {
                $arr[$item->work_date . '_' . $item->work_time_start][] = $item->sid;
            }
        }
        $positionShiftWorks = [];
        if (count($arr)) {
            foreach ($arr as $key => $value) {
                if (count($value)) {
                    foreach ($value as $key2 => $value2) {
                        $positionShiftWorks[$value2] = $key2 + 1;
                    }
                }
            }
        }
        $this->set(compact('reservesstatus'));
        $this->set(compact('reservesstatus2'));
        $this->set(compact('initsmonth'));
        $this->set(compact('initemonth'));
        $this->set(compact('arrconcierges'));
        $this->set(compact('initsdate', 'initedate'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
        $this->set(compact('queryconcierges'));
        $this->set(compact('positionShiftWorks'));
    }

    /**
     * Show day
     * Index page /management/schedule/day/
     * @query string|null $id id of Concierges $dt datetime
     * @author: ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function day()
    {
        $this->checkAuth();
        $id = (int)$this->request->getQuery('id');

        $dt = $this->request->getQuery('dt');
        // Make date
        if (isset($dt)) {
            try {
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $dt)) {
                    $initsdate = date('Y/m/d', strtotime($dt));
                } else {
                        $initsdate = date("Y/m/d");
                }
            } catch (\Exception $e) {
                $initsdate = date("Y/m/d");
            }
        } else {
            $initsdate = date("Y/m/d");
            $initedate = date('Y/m/d', strtotime('sunday this week'));
        }
        //$tmprsvdate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $array_query_concierges = [['Concierges.avail_flg' => 1]];
        $array_query = [];
        $now = new \DateTime();
        if ($id > 0) {
            array_push($array_query, [['Concierges.id' => $id]]);
            array_push($array_query_concierges, [['Concierges.id' => $id]]);
            $arrconcierges_workshiff = $conciergesTable->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])->where([$array_query_concierges])

                ->order(['id' => 'ASC'])->toArray();

            $queryconcierges = $conciergesTable->find()->select(['sort_no' => 'Concierges.sort_no', 'id' => 'Concierges.id', 'title' => 'Concierges.name'])

                ->join(['ShiftWorks' => [
                    'table' => 'shift_works',
                    'type' => 'INNER',
                    'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
                ->where(['avail_flg' => 1, 'Concierges.id' => $id])

                ->order(['sort_no' => 'ASC', 'id' => 'ASC']);
        } else {
            $arrconcierges_workshiff = $conciergesTable->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ]) ->join(['ShiftWorks' => [
                    'table' => 'shift_works',
                    'type' => 'INNER',
                    'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
                ->where([$array_query_concierges])->andWhere(['ShiftWorks.work_date =' => $initsdate ])->distinct()
                ->order(['Concierges.id' => 'ASC'])->toArray();

            $queryconcierges = $conciergesTable->find()->select(['sort_no' => 'Concierges.sort_no', 'id' => 'Concierges.id', 'title' => 'Concierges.name'])

                ->join(['ShiftWorks' => [
                    'table' => 'shift_works',
                    'type' => 'INNER',
                    'conditions' => 'Concierges.id = ShiftWorks.concierge_id']])
                ->where(['avail_flg' => 1])
                ->andWhere(['ShiftWorks.work_date =' => $initsdate ])
                ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . $now->format('i') . " ) )")
                ->distinct()
                ->order(['sort_no' => 'ASC', 'id' => 'ASC']);
        }
        $conciergesQuery = $conciergesTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])
            ->where(['Concierges.avail_flg' => 1])
            ->order(['sort_no' => 'ASC', 'id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;
        // Query data from DB
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name', 'cid' => 'Concierges.id',
            'sid' => 'ShiftWorks.id',
            'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start',
            'status' => 'ShiftWorks.status',
            'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
            'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
        ])
            ->join(['Concierges' => [
                'table' => 'concierges',
                'type' => 'INNER',
                'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
            ->where([
                'ShiftWorks.work_date =' => $initsdate,
                /*'ShiftWorks.tmp_reserve_date <= ' => $tmprsvdate,*/
                $array_query
            ])
            ->andWhere("(ShiftWorks.work_date >'" . $now->format('Y-m-d') . "' OR (ShiftWorks.work_date ='" . $now->format('Y-m-d') . "' AND  ShiftWorks.work_time_start >" . $now->format('H') . "00 ) )")
            ->order(['ShiftWorks.concierge_id' => 'ASC', 'ShiftWorks.work_date' => 'ASC' ]);

        $this->set(compact('arrconcierges'));
        $this->set(compact('initsdate', 'initedate'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
        $this->set(compact('arrconcierges_workshiff'));
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
                'Concierges.avail_flg' => 1
            ])->order(['sort_no' => 'ASC', 'id' => 'ASC'])->toArray();
        $this->set('concierges', $mconcierges);
       // var_dump($mconcierges)  ;
        $arrycon = $this->getConcierges();
        $d = new \DateTime($initsdate);
        $d->modify('first day of next month');
        $firstNextMonth = $d->format('Y-m-d');
        $reservesstatus = $this->reservesstatus($this->ID, $initsdate);
        $reservesstatusCurentMonth = $this->reservesstatus($this->ID, $now->format('Y-m-d'), false);
        $now->modify('first day of next month');
        $reservesstatusCurentMonthToNextMonth = $this->reservesstatus($this->ID, $now->format('Y-m-d'), false);
        $this->set(compact('reservesstatus'));

        $reservesstatusNextMonth = $this->reservesstatus($this->ID, $firstNextMonth, false);
        $this->set('arrayConierges', $arrycon);
        $filePath = Configure::read('thumbnail_img_path');
        $this->set(['filePath' => $filePath, 'reservesstatusCurentMonth' => $reservesstatusCurentMonth, 'reservesstatusCurentMonthToNextMonth' => $reservesstatusCurentMonthToNextMonth]);

        $this->set(compact('reservesstatusNextMonth'));
        $this->set(compact('queryconcierges'));
    }
}
