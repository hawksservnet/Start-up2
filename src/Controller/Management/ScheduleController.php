<?php
namespace App\Controller\Management;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use phpDocumentor\Reflection\Types\This;

/**
 * Schedule Controller
 *
 *
 * @method \App\Model\Entity\Schedule[] paginate($object = null, array $settings = [])
 */
class ScheduleController extends ManagementController
{
    private $AUTH = -1;
    private $LOGINID = -1;

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $schedule = $this->paginate($this->Schedule);

        $this->set(compact('schedule'));
        $this->set('_serialize', ['schedule']);
    }

    /**
     * View method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $schedule = $this->Schedule->get($id, [
            'contain' => []
        ]);

        $this->set('schedule', $schedule);
        $this->set('_serialize', ['schedule']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $schedule = $this->Schedule->newEntity();
        if ($this->request->is('post')) {
            $schedule = $this->Schedule->patchEntity($schedule, $this->request->getData());
            if ($this->Schedule->save($schedule)) {
                $this->Flash->success(__('The schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
        }
        $this->set(compact('schedule'));
        $this->set('_serialize', ['schedule']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $schedule = $this->Schedule->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $schedule = $this->Schedule->patchEntity($schedule, $this->request->getData());
            if ($this->Schedule->save($schedule)) {
                $this->Flash->success(__('The schedule has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The schedule could not be saved. Please, try again.'));
        }
        $this->set(compact('schedule'));
        $this->set('_serialize', ['schedule']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Schedule id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $schedule = $this->Schedule->get($id);
        if ($this->Schedule->delete($schedule)) {
            $this->Flash->success(__('The schedule has been deleted.'));
        } else {
            $this->Flash->error(__('The schedule could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
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
                $date = Date('Y/m/d', strtotime('first day of this month', strtotime($dt)));
                $this->set('currentTime', $date);
                $currentDate = Date('Y/m/d', strtotime($dt));
                $initsdate = Date('Y/m/d', strtotime('first day of this month', strtotime($date)));
                $initedate = Date('Y/m/d', strtotime('last day of this month', strtotime($date)));
            } else {
                $now = new \DateTime();
                $this->set('currentTime', $now->format('Y-m-d'));
                $currentDate = $now->format('Y-m-d');
                $initsdate = Date('Y/m/d', strtotime('first day of this month', strtotime($now->format('Y-m-d'))));
                $initedate = Date('Y/m/d', strtotime('last day of this month', strtotime($now->format('Y-m-d'))));
            }
        } else {
            $now = new \DateTime();
            $this->set('currentTime', $now->format('Y-m-d'));
            $initsdate = Date('Y/m/d', strtotime('first day of this month', strtotime($now->format('Y-m-d'))));
            $initedate = Date('Y/m/d', strtotime('last day of this month', strtotime($now->format('Y-m-d'))));
            $currentDate = $now->format('Y-m-d');
        }
        $this->set('currentDate', $currentDate);
        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $reservesTable = TableRegistry::get('Reserves');
        $conciergesQuery = $conciergesTable->find('list', [
        'keyField' => 'id',
        'valueField' => 'name'
        ])
        ->where(['avail_flg' => 1])
        ->order(['id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;

        $array_query = [];
        // Delete, Edit, Add
        if ($this->AUTH == 0 || $this->AUTH == 4 || $this->AUTH == 5) {
            if ($id > 0) {
                array_push($array_query, [['Concierges.id' => $id]]);
                $this->set(compact('id'));
            }
        } else {
            $conciergy = $conciergesTable->find()->select(['account_id' => 'Concierges.account_id', 'id' => 'Concierges.id'])
                ->where(['Concierges.account_id' => $this->LOGINID])->first();
            array_push($array_query, ['Concierges.id' => $conciergy['id']]);
        }
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        if ($this->AUTH == 0 || $this->AUTH == 4 || $this->AUTH == 5) {
            $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));

            // Query data from DB
            $queryShift = $ShiftWorkTable->find()->select(['work_date' => 'ShiftWorks.work_date', 'totalS' => 'count(ShiftWorks.id)'])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->join(['Reserves' => [
                    'table' => 'reserves',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ShiftWorks.concierge_id = Reserves.concierge_id ',
                        'ShiftWorks.work_date = Reserves.work_date',
                        'ShiftWorks.work_time_start = Reserves.work_time_start',
                        'Reserves.reserve_status <> 9',
                        'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                    ]
                ]])
                ->where([ 'ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <= ' => $initedate, 'Reserves.id IS NULL', 'ShiftWorks.tmp_reserve_id IS NULL', $array_query ])
                ->group(['ShiftWorks.work_date'])
                ->order(['ShiftWorks.work_date' => 'ASC' ]);

            $queryReserves = $reservesTable->find()->select(['work_date' => 'Reserves.work_date', 'totalR' => 'count(Reserves.id)'])
            ->where(['Reserves.work_date >=' => $initsdate, 'Reserves.work_date <= ' => $initedate, 'Reserves.reserve_status <> ' => 9,
                'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB') ])
            ->group(['Reserves.work_date']);
            $queryShiftReseerverTemp = $ShiftWorkTable->find()->select(['work_date' => 'ShiftWorks.work_date', 'totalST' => 'count(ShiftWorks.id)'])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->join(['Reserves' => [
                    'table' => 'reserves',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ShiftWorks.concierge_id = Reserves.concierge_id ',
                        'ShiftWorks.work_date = Reserves.work_date',
                        'ShiftWorks.work_time_start = Reserves.work_time_start',
                        'Reserves.reserve_status <> 9',
                        'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                    ]
                ]])
                ->where(['ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <= ' => $initedate, 'Reserves.id IS NULL', 'ShiftWorks.tmp_reserve_id > 0', 'ShiftWorks.tmp_reserve_date >=' => $compareDate, $array_query ])
                ->group(['ShiftWorks.work_date'])
                ->order(['ShiftWorks.work_date' => 'ASC' ]);
            $this->set(compact('queryShiftReseerverTemp'));
            $this->set(compact('queryReserves'));
        } else {
            // Query data from DB
            $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name',
                'work_date' => 'ShiftWorks.work_date', 'status' => 'ShiftWorks.status', 'work_time_start' => 'ShiftWorks.work_time_start',
                'name' => 'Concierges.name', 'reserve_status' => 'Reserves.reserve_status',
                'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
                'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
                'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
                'cid' => 'Reserves.id', 'user_id' => 'Users.id', 'user_name' => 'concat(Users.name_last," ",Users.name_first)'
            ])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->join(['Reserves' => [
                    'table' => 'reserves',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ShiftWorks.concierge_id = Reserves.concierge_id ',
                        'ShiftWorks.work_date = Reserves.work_date',
                        'ShiftWorks.work_time_start = Reserves.work_time_start',
                        'Reserves.reserve_status <> 9',
                        'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                    ]
                ]])
                ->join(['Users' => [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'Reserves.user_id = Users.id'
                ]])
                ->where([ 'ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <= ' => $initedate, $array_query ])
                ->order(['ShiftWorks.work_date' => 'ASC', 'ShiftWorks.work_time_start' => 'ASC' ]);
        }
        $this->set(compact('arrconcierges'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
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
                    if ($dayofweek == 1) {
                        $initsdate = Date('Y/m/d', strtotime($dt));
                        $initedate = Date('Y/m/d', strtotime('+6 days', strtotime($dt)));
                    } else {
                        $initsdate = Date('Y/m/d', strtotime('+' . (1 - $dayofweek) . ' days', strtotime($dt)));
                        $initedate = Date('Y/m/d', strtotime('+6 days', strtotime($initsdate)));
                    }
                } else {
                    $initsdate = Date('Y/m/d', strtotime('monday this week'));
                    $initedate = Date('Y/m/d', strtotime('sunday this week'));
                }
            } catch (\Exception $e) {
                $initsdate = Date('Y/m/d', strtotime('monday this week'));
                $initedate = Date('Y/m/d', strtotime('sunday this week'));
            }
        } else {
            $initsdate = Date('Y/m/d', strtotime('monday this week'));
            $initedate = Date('Y/m/d', strtotime('sunday this week'));
        }
        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $conciergesQuery = $conciergesTable->find('list', ['keyField' => 'id', 'valueField' => 'name'])
        ->where(['avail_flg' => 1])
        ->order(['id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;
        $array_query = [];
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        // Delete, Edit, Add
        if ($this->AUTH == 0 || $this->AUTH == 4 || $this->AUTH == 5) {
            if ($id > 0) {
                array_push($array_query, [['Concierges.id' => $id ]]);
            }
            $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name',
                'cid' => 'Concierges.id', 'sid' => 'ShiftWorks.id',
                'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start', 'status' => 'ShiftWorks.status',
                'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
                'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
                'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
                'reserve_status' => 'Reserves.reserve_status', 'user_id' => 'Reserves.user_id', 'rid' => 'Reserves.id', 'user_name' => 'concat(Users.name_last," ",Users.name_first)' ])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->join(['Reserves' => [
                    'table' => 'reserves',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ShiftWorks.concierge_id = Reserves.concierge_id',
                        'Reserves.work_date = ShiftWorks.work_date',
                        'ShiftWorks.work_time_start = Reserves.work_time_start',
                        'Reserves.reserve_status <> 9',
                        'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                    ]]])
                ->join(['Users' => [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'Reserves.user_id = Users.id'
                ]])
                ->where([ 'ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate, $array_query])
                ->order(['ShiftWorks.work_date' => 'ASC']);
        } else {
                $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
                $conciergy = $conciergesTable->find()->select(['account_id' => 'Concierges.account_id', 'id' => 'Concierges.id'])
                    ->where(['Concierges.account_id' => $this->LOGINID ])->first();
                    array_push($array_query, ['Concierges.id' => $conciergy['id']]);
            $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name',
                'cid' => 'Concierges.id', 'sid' => 'ShiftWorks.id',
                'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
                'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
                'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
                'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start', 'status' => 'ShiftWorks.status',
                'reserve_status' => 'Reserves.reserve_status', 'user_id' => 'Reserves.user_id', 'rid' => 'Reserves.id', 'user_name' => 'concat(Users.name_last," ",Users.name_first)' ])
                ->join(['Concierges' => [
                    'table' => 'concierges',
                    'type' => 'LEFT',
                    'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
                ->join(['Reserves' => [
                    'table' => 'reserves',
                    'type' => 'LEFT',
                    'conditions' => [
                        'ShiftWorks.concierge_id = Reserves.concierge_id',
                        'Reserves.work_date = ShiftWorks.work_date',
                        'ShiftWorks.work_time_start = Reserves.work_time_start',
                        'Reserves.reserve_status <> 9',
                        'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                    ]]])
                ->join(['Users' => [
                    'table' => 'users',
                    'type' => 'LEFT',
                    'conditions' => 'Reserves.user_id = Users.id'
                ]])
                ->where([ 'ShiftWorks.work_date >=' => $initsdate, 'ShiftWorks.work_date <=' => $initedate, $array_query])
                ->order(['ShiftWorks.work_date' => 'ASC']);
        }
        // Query data from DB
        /** @var TYPE_NAME $queryShift */
        $this->set(compact('arrconcierges'));
        $this->set(compact('initsdate', 'initedate'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
    }

    /**
    Check auth of user login
     * @author thovo
     * @return \Cake\Network\Response|void : boolean
     */
    private function checkAuth()
    {
        $session = $this->request->session();
        $this->AUTH = (int)(!$session->check('LOGIN.AUTH'))?-1:$session->read('LOGIN.AUTH');
        $this->LOGINID = (int)(!$session->check('LOGIN.ID'))?-1:$session->read('LOGIN.ID');
        /*
        if ($this->LOGINID == -1 || $this->AUTH == -1) {
            $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_010'));

            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/login');
        } else {
            if (in_array($this->AUTH, [0, 3, 4, 5 ]) !== true) {
                return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'admin/users');
            }
        }
        */
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
                    $initsdate = Date('Y/m/d', strtotime($dt));
                } else {
                    $initsdate = date("Y/m/d", strtotime(date("d.m.Y")));
                }
            } catch (\Exception $e) {
                $initsdate = date("Y/m/d", strtotime(date("d.m.Y")));
            }
        } else {
            $initsdate = date("Y/m/d", strtotime(date("d.m.Y")));
            $initedate = Date('Y/m/d', strtotime('sunday this week'));
        }
        $conciergesTable = TableRegistry::get('Concierges');
        $ShiftWorkTable = TableRegistry::get('ShiftWorks');
        $array_query_concierges = [];
        $array_query = [];
        // Delete, Edit, Add
        $compareDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
        if ($this->AUTH == 0 || $this->AUTH == 4 || $this->AUTH == 5) {
            $array_query_concierges = [['Concierges.avail_flg' => 1]];
            if ($id > 0) {
                array_push($array_query, [['Concierges.id' => $id]]);
                $arrconcierges_workshiff = $conciergesTable->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name'
                ])
                    ->where([$array_query_concierges])->andWhere(['Concierges.id =' => $id])
                    ->order(['id' => 'ASC'])->toArray();
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
            }
        } else {
            $conciergy = $conciergesTable->find()->select(['account_id' => 'Concierges.account_id', 'id' => 'Concierges.id'])
                ->where(['Concierges.account_id' => $this->LOGINID ])->first();
            if (isset($conciergy) == true) {
                array_push($array_query, [['Concierges.id' => $conciergy['id']]]);
            }
            array_push($array_query_concierges, [['Concierges.account_id' => $this->LOGINID]]);

            $arrconcierges_workshiff = $conciergesTable->find('list', [
                'keyField' => 'id',
                'valueField' => 'name'
            ])
                ->where([$array_query_concierges])
                ->order(['id' => 'ASC'])->toArray();
        }

        $conciergesQuery = $conciergesTable->find('list', [
            'keyField' => 'id',
            'valueField' => 'name'
        ])
            ->where([$array_query_concierges])
            ->order(['id' => 'ASC'])->toArray();
        $arrconcierges = $conciergesQuery;

        // Query data from DB
        $queryShift = $ShiftWorkTable->find()->select(['name' => 'Concierges.name', 'cid' => 'Concierges.id',
            'sid' => 'ShiftWorks.id',
            'work_date' => 'ShiftWorks.work_date', 'work_time_start' => 'ShiftWorks.work_time_start', 'status' => 'ShiftWorks.status',
            'tentative_reservation_flag' => 'IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)',
            'tmp_reserve_id' => 'ShiftWorks.tmp_reserve_id',
            'status_order' => 'case ShiftWorks.status when 1 then 1 when 0 then  IF(ShiftWorks.tmp_reserve_id IS NULL,0,IF(ShiftWorks.tmp_reserve_date > "' . $compareDate . '",1,0)) end',
            'user_id' => 'Users.id', 'rid' => 'Reserves.id', 'user_name' => 'concat(Users.name_last," ",Users.name_first)', 'test' => 'Reserves.user_id'
            ])
            ->join(['Concierges' => [
                'table' => 'concierges',
                'type' => 'LEFT',
                'conditions' => 'ShiftWorks.concierge_id = Concierges.id']])
            ->join(['Reserves' => [
                'table' => 'reserves',
                'type' => 'LEFT',
                'conditions' => [
                    'ShiftWorks.concierge_id = Reserves.concierge_id',
                    'ShiftWorks.work_date = Reserves.work_date',
                    'ShiftWorks.work_time_start = Reserves.work_time_start',
                    'Reserves.reserve_status <> ' => 9,
                    'Reserves.reserve_classification' => Configure::read('RESERVE_CLASSIFICATION.FROM_WEB')
                ]]])
            ->join(['Users' => [
                'table' => 'users',
                'type' => 'LEFT',
                'conditions' => 'Users.id = Reserves.user_id']])
            ->where([ 'ShiftWorks.work_date =' => $initsdate, $array_query])
            ->order(['ShiftWorks.work_date' => 'ASC' ]);

        $this->set(compact('arrconcierges'));
        $this->set(compact('arrconcierges_workshiff'));
        $this->set(compact('initsdate', 'initedate'));
        $this->set(compact('queryShift'));
        $this->set(compact('id'));
    }
}
