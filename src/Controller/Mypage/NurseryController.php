<?php
/**
 * Mypage Controller
 **/

namespace App\Controller\Mypage;

use Cake\Core\Configure;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;

class NurseryController extends MypageController
{
    /**
     * Index page /mypage/Nursery/list
     * @author: Huynh
     * @return \Cake\Network\Response|void : boolean
     */
    public function index()
    {
        $session = $this->request->session();
        $this->clearSearch('search112');
        $loginId = ($session->check('MYPAGE.ID')) ? $session->read('MYPAGE.ID') : 0;
        $loginName = ($session->check('MYPAGE.NAME')) ? $session->read('MYPAGE.NAME') : '';

        if (!$loginId || $loginName === '') {
            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'users/login');
        }

        $nurseryReservesTable = TableRegistry::get('NurseryReserves');
        $data = $this->request->data;
        if ($this->request->is('post') && !empty($data) && !empty($data['nurse_reserve_id'])) {
            $cancelNersery = $nurseryReservesTable->find()
                ->where(['id' => $data['nurse_reserve_id']])->first();
            $cancelNersery->status = $data['nurse_reserve_status'];
            $cancelNersery->approval = $data['nurse_reserve_appvoral'];
            $cancelNersery->modified_id = 0;
            $cancelNersery->modified_date = date('Y-m-d H:i:s');
            if (!empty($cancelNersery->id) && $nurseryReservesTable->save($cancelNersery)) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_046'));

                $queryName = $nurseryReservesTable->find()->select(['email' => 'Users.email', 'user_name' => 'CONCAT(name_last, name_first)',
                    'reserve_date', 'reserve_time_start', 'reserve_time_end', 'sex1' => 'NurseryReserves.sex1', 'sex2' => 'NurseryReserves.sex2'])
                    ->join(['Users' => ['table' => 'users', 'type' => 'INNER',
                        'conditions' => 'NurseryReserves.user_id = Users.id']])
                    ->where(['NurseryReserves.ID' => $data['nurse_reserve_id']])->first();
                if (isset($queryName->sex1) && !empty(isset($queryName->sex1))) {
                    $child = 1;
                }
                if (isset($queryName->sex2) && !empty(isset($queryName->sex2))) {
                    $child = 2;
                }

                $mailinfos['user_name'] = $queryName->user_name;
                $mailinfos['email'] = $queryName->email;
                $mailinfos['reserve_date'] = date_format($queryName->reserve_date, 'Y/m/d');
                $mailinfos['reserve_time_start'] = substr_replace($queryName->reserve_time_start, ':', 2, 0);
                $mailinfos['reserve_time_end'] = substr_replace($queryName->reserve_time_end, ':', 2, 0);
                $mailinfos['childs'] = $child;
                if ($mailinfos['email']) {
                    $this->sendEmail(
                        Configure::read('from_mailaddress'),
                        $mailinfos['email'],
                        '【Startup Hub Tokyo】一時保育サービスのキャンセルをお受けいたしました。',
                        $mailinfos,
                        'email_nursery_reserver_user_cancel',
                        null,
                        Configure::read('nursery_admin_mailaddress')
                    );
                }
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_047'));
            }
        }

        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search112.page', $this->request->query['page']);
        } else {
            if (!empty($session->read('search112.page'))) {
                $queryRedirect['page'] = !empty($session->read('search112.page')) ? $session->read('search112.page') : 1;

                return $this->redirect($queryRedirect);
            }
            $session->write('search112.page', 1);
        }
        $page = !empty($session->read('search112.page')) ? $session->read('search112.page') : 1;

        $nurseryReservesQuery = $nurseryReservesTable->find()->select([
            'NurseryReserves.id', 'NurseryReserves.reserve_date', 'NurseryReserves.reserve_time_start', 'NurseryReserves.reserve_time_end',
            'NurseryReserves.purpose', 'NurseryReserves.user_id', 'NurseryReserves.status', 'NurseryReserves.approval',
            'cancel_limit_date' => 'NurserySchedule.cancel_limit_date', 'sex1' => 'NurseryReserves.sex1', 'sex2' => 'NurseryReserves.sex2'
        ])
            ->join(['NurserySchedule' => ['table' => 'nursery_schedule', 'type' => 'INNER',
                'conditions' => 'NurserySchedule.reserve_date = NurseryReserves.reserve_date']])
            ->where(['NurseryReserves.user_id' => $loginId])
            ->order(['NurseryReserves.reserve_date' => 'DESC', 'NurseryReserves.reserve_time_start' => 'ASC']);

        $pagingConfig = [
            'page' => $page,
            'limit' => Configure::read('list_max_row'),
        ];

        try {
            $nurseryReserves = $this->Paginator->paginate($nurseryReservesQuery, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'index',
                'page' => 1,
            ]);
        }

        $this->set(compact(['pagingConfig', 'nurseryReserves']));

        $isLimitForm = $this->checkLimitNursery($loginId, 4);
        $this->set(compact(['isLimitForm']));
        if ($isLimitForm) {
            // $this->Flash->error('一時保育サービスは1ヶ月最大４件まで予約登録できます');
        }
    }

    /**
     * Check if total Booking is 4
     * @param int $loginId Login ID
     * @param int $limit max book allow
     * @author Huynh
     * @return bool
     */
    private function checkLimitNursery($loginId, $limit = 4)
    {
        $nurseryReservesTable = TableRegistry::get('NurseryReserves');

        $compareDate = date('Y-m-d');
        $compareTime = date('Hi');
        $nurseriesCount = $nurseryReservesTable->find()
            ->where([
                'NurseryReserves.user_id' => $loginId,
                'OR' => [['NurseryReserves.status' => 0], ['NurseryReserves.status' => 1, 'NurseryReserves.approval' => 1]],
            ])
            ->where([
                'OR' => [['NurseryReserves.reserve_date >' => $compareDate],
                    ['NurseryReserves.reserve_date' => $compareDate, 'NurseryReserves.reserve_time_start >' => $compareTime]]
            ])
            ->count();
        if ($nurseriesCount >= 4) {
            return true;
        }

        return false;
    }
    /**
     * Check if total Booking is 4 per month
     * @param int $loginId Login ID
     * @param string $date month check
     * @param int $limit max book allow
     * @author thovo
     * @return bool
     */
    private function checkLimitNurseryPerMonth($loginId, $date, $limit = 4)
    {
        $nurseryReservesTable = TableRegistry::get('NurseryReserves');
        $compareDate = date_format($date, 'Ym');
        $nurseriesCount = $nurseryReservesTable->find()
            ->where([
                'NurseryReserves.user_id' => $loginId,
                'OR' => [['NurseryReserves.status' => 0], ['NurseryReserves.status' => 1, 'NurseryReserves.approval' => 1]],
            ])
            ->where(["DATE_FORMAT(NurseryReserves.reserve_date,'%Y%m')" => $compareDate])
            ->count();
        if ($nurseriesCount >= 4) {
            return true;
        }

        return false;
    }
    /**
     * Check if total Booking is 4 per month
     * @param int $loginId Login ID
     * @param string $startDate start date check
     * @param string $endDate end date check
     * @param int $limit max book allow
     * @author thovo
     * @return bool
     */
    private function checkLimitNurseryByRankDate($loginId, $startDate, $endDate, $limit = 4)
    {
        $nurseryReservesTable = TableRegistry::get('NurseryReserves');

        $nurseriesCount = $nurseryReservesTable->find()
            ->where([
                'NurseryReserves.user_id' => $loginId,
                'OR' => [['NurseryReserves.status' => 0], ['NurseryReserves.status' => 1, 'NurseryReserves.approval' => 1]],
            ])
            ->where(['NurseryReserves.reserve_date >=' => $startDate, "NurseryReserves.reserve_date <=" => $endDate])
            ->count();
        if ($nurseriesCount > $limit) {
            return true;
        }

        return false;
    }
    /**
     * Form method
     * @author: Huynh
     * @return \Cake\Http\Response|void
     */
    public function form()
    {
        $session = $this->request->session();
        $arr_day_of_week = Configure::read('DAYOFWEEK');
        $loginId = ($session->check('MYPAGE.ID')) ? $session->read('MYPAGE.ID') : 0;
        $loginName = ($session->check('MYPAGE.NAME')) ? $session->read('MYPAGE.NAME') : '';
        if (!$loginId || $loginName === '') {
            return $this->redirect(Configure::read('FUEL_ADMIN_URL') . 'users/login');
        }
        $nurseryScheduleTable = TableRegistry::get('NurserySchedule');
        $nurserySchedule = $nurseryScheduleTable->find()
            ->select(['reserve_date', 'id'])
            ->where(['limit_date >=' => date('Y-m-d')])
            ->order(['reserve_date' => 'ASC']);
        $this->set(compact(['nurserySchedule']));
        $data = $this->request->data;
        if ($this->request->is('post') && !empty($data)) {
            $dataSave = $data;
            $dataSave['reserve_time_start'] = str_replace(':', '', $data['reserve_time_start']);
            $dataSave['reserve_time_end'] = str_replace(':', '', $data['reserve_time_end']);
            $dataSave['phone'] = str_replace('-', '', $data['phone']);

            $dataSave['user_id'] = $loginId;
            $dataSave['status'] = 0;
            $dataSave['approval'] = 0;
            $dataSave['created_id'] = $loginId;
            $dataSave['modified_id'] = 0;

            $dataSave['name1'] = '';
            $dataSave['name_k1'] = '';
            $child = '';
            if (isset($dataSave['sex1']) && !empty(isset($dataSave['sex1']))) {
                $child = 1;
            }
            if (isset($dataSave['sex2']) && !empty(isset($dataSave['sex2']))) {
                $child = 2;
            }
            $nurseryReservesTable = TableRegistry::get('NurseryReserves');
            $newNursery = $nurseryReservesTable->newEntity($dataSave);
            if ($nurseryReservesTable->save($newNursery)) {
                // $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_048'));
                $this->Flash->success("仮予約をお受けいたしました。ご登録いただいたメールアドレス宛にメールをお送りいたしました。 \r\n ご予約確定までには数日かかりますので、予めご了承ください。");
                $userEmail = $this->getUserEmail($loginId);
                $reserve_date = ($dataSave['reserve_date']? date("Y年m月d日", strtotime($dataSave['reserve_date'])) : '' ) . '（' . $arr_day_of_week[date("D", strtotime($dataSave['reserve_date']))] . '）';

                $this->sendEmail(
                    Configure::read('from_mailaddress'),
                    $userEmail,
                    '【Startup Hub Tokyo】一時保育サービス 仮予約受付のお知らせ',
                    [
                        'user_name' => $loginName,
                        'reserve_date' => $reserve_date,
                        'reserve_time_start' => substr_replace($data['reserve_time_start'], ':', 2, 0),
                        'reserve_time_end' => substr_replace($data['reserve_time_end'], ':', 2, 0),
                        'childs' => $child
                    ],
                    'email_nursery_reserves_register',
                    null,
                    Configure::read('nursery_admin_mailaddress')
                );

                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_049'));

                return $this->redirect(['action' => 'index']);
            }
        }
    }

    /**
     * Ajax Check shorttime method
     * @author: Thovo
     * @return \Cake\Http\Response|void : boolean
     */
    public function ajaxshorttime()
    {
        $session = $this->request->session();
        $loginId = ($session->check('MYPAGE.ID')) ? $session->read('MYPAGE.ID') : 0;
        $response['status'] = 0;
        $response['max_time'] = 21;
        $data = $this->request->query;
        if (isset($data['id']) && ($data['id'] > 0)) {
            $nurseryScheduleTable = TableRegistry::get('NurserySchedule');
            $item = $nurseryScheduleTable->get($data['id']);
            $LIMITDAY = $this->checkLimitNurseryByRankDate($loginId, $item->reserve_date, $item->reserve_date, 0);
            $dayofweek = date('w', strtotime($item->reserve_date));
            if ($dayofweek == 1) {
                $initsdate = Date('Y-m-d', strtotime($item->reserve_date));
                $initedate = Date('Y-m-d', strtotime('+6 days', strtotime($item->reserve_date)));
            } else {
                $initsdate = Date('Y-m-d', strtotime('+' . (1 - $dayofweek) . ' days', strtotime($item->reserve_date)));
                $initedate = Date('Y-m-d', strtotime('+6 days', strtotime($initsdate)));
            }
            $LIMITWEEK = $this->checkLimitNurseryByRankDate($loginId, $initsdate, $initedate, 1);
            $LIMITMONTH = $this->checkLimitNurseryPerMonth($loginId, $item->reserve_date, 4);
            if ($LIMITDAY) {
                $response['LIMIT'] = true;
                $response['MSGLIMIT'] = Configure::read('MESSAGE_NOTIFICATION.MSG_062');
            } elseif ($LIMITWEEK) {
                $response['LIMIT'] = true;
                $response['MSGLIMIT'] = Configure::read('MESSAGE_NOTIFICATION.MSG_063');
            } elseif ($LIMITMONTH) {
                $response['LIMIT'] = true;
                $response['MSGLIMIT'] = Configure::read('MESSAGE_NOTIFICATION.MSG_061');
            } else {
                $response['LIMIT'] = false;
                $response['MSGLIMIT'] = '';
            }
            $reserve_time_start = '';
            $reserve_time_end = '';
            if (isset($item)) {
                $response['status'] = 1;
                $TimeStartDefault = '1000';
                $TimeEndDefault = '1300';
                if ($item->shorttime_flg == 1) {
                    $response['max_time'] = 17;
                }

                for ($y = 10; $y < $response['max_time']; $y++) {
                    $val = (string)$y . '00';
                    $selected_time_start = ($TimeStartDefault == $val )?'selected':'';
                    $selected_time_end = ($TimeEndDefault == $val )?'selected':'';
                    $reserve_time_start .= '<option value="' . $val . '" ' . $selected_time_start . ' >' . (string)$y . ':00' . '</option>';
                    $reserve_time_end .= '<option value="' . $val . '" ' . $selected_time_end . ' >' . (string)$y . ':00' . '</option>';
                    $val = (string)$y . '30';
                    $selected_time_start = ($TimeEndDefault == $val )?'selected':'';
                    $selected_time_end = ($TimeEndDefault == $val )?'selected':'';
                    $reserve_time_start .= '<option value="' . $val . '" ' . $selected_time_start . ' >' . (string)$y . ':30' . '</option>';
                    $reserve_time_end .= '<option value="' . $val . '" ' . $selected_time_end . ' >' . (string)$y . ':30' . '</option>';
                }
                $selected_time_start = ($TimeEndDefault == $response['max_time'] . '00') ? 'selected' : '';
                $selected_time_end = ($TimeEndDefault == $response['max_time'] . '00') ? 'selected' : '';
                $reserve_time_start .= '<option value="' . $response['max_time'] . '00" ' . $selected_time_start . ' >' . $response['max_time'] . ':00</option>';
                $reserve_time_end .= '<option value="' . $response['max_time'] . '00" ' . $selected_time_end . ' >' . $response['max_time'] . ':00</option>';
                $response['reserve_time_start'] = $reserve_time_start;
                $response['reserve_time_end'] = $reserve_time_end;
            }
        }
        echo json_encode($response);
        exit;
    }
}
