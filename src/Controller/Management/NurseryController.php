<?php
/**
 * Created by PhpStorm.
 * User: thovo
 * Date: 10/11/2017
 * Time: 10:10 AM
 */
namespace App\Controller\Management;

use Cake\Core\Configure;
use Cake\Network\Email\Email;
use Cake\ORM\TableRegistry;
use phpDocumentor\Reflection\Types\This;

class NurseryController extends ManagementController
{
    private $AUTH = -1;
    private $LOGINID = -1;
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
    }

    /**
     * Show reserve
     * Index page /management/nursery/reserve/
     * @query string|null $p page of list
     * @author: ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function reserve()
    {
        // $this->sendemailtext();
        $this->checkAuth();
        $this->clearSearch('search021');
        $session = $this->request->session();
        $arr_child_purpose = Configure::read('CHILD_PURPOSE');
        $arr_child_approval = Configure::read('CHILD_APPROVAL');
        $arr_day_of_week = Configure::read('DAYOFWEEK');
        $NurseryReservesTable = TableRegistry::get('NurseryReserves');
        $queryManage = $NurseryReservesTable->find();
        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search021.page', $this->request->query['page']);
        } else {
            $session->write('search021.page', 1);
        }
        $page = !empty($session->read('search021.page')) ? $session->read('search021.page') : 1;
        $data = $this->request->data;
        if ($this->request->is('post') && !empty($data['hid_csv']) && $data['hid_csv'] == 1) {
            $conditions_CSV = [];
            $sort_CSV = '';
            if (isset($data['arrChk']) || (!empty($data['download_all']) && $data['download_all'] == '1')) {
                if ((!empty($data['download_all']) && $data['download_all'] == '1')) {
                    $conditions_CSV = json_decode($data['csvwhere'], true);
                } else {
                    $conditions_CSV['NurseryReserves.id IN'] = $data['arrChk'];
                }




                    if (!$session->check('search021.orderby')) {
                        $sort_CSV = '  NurseryReserves.modified_date desc,
                                    NurseryReserves.reserve_date desc,
                                    NurseryReserves.reserve_time_start asc ';
                    } else
                    {
                        if (!empty($data['csvsort'])) {
                            $sort_CSV = $data['csvsort'];
                        } else {
                            $sort_CSV = 'NurseryReserves.id desc';
                        }
                    }

                $queryManageCount = $NurseryReservesTable->find()->select(['id' => 'NurseryReserves.id',
                    'reserve_date' => 'NurseryReserves.reserve_date',
                    'reserve_month' => "DATE_FORMAT(NurseryReserves.reserve_date,'%Y%m')",
                    'user_id' => 'NurseryReserves.user_id',
                    'total' => $queryManage->func()->count('NurseryReserves.id')
                ]) ->join([
                    'Users' => ['table' => 'users', 'type' => 'INNER',
                        'conditions' => 'NurseryReserves.user_id = Users.id'],
                ])->where([
                    'NurseryReserves.reserve_date < NOW()'
                ])
                    ->orWhere(['NurseryReserves.status' => 1, 'NurseryReserves.approval' => 1])
                    ->group(['reserve_month', 'NurseryReserves.user_id']);
                $queryManageAppvoral = $NurseryReservesTable->find()->select(['id' => 'NurseryReserves.id', 'approval_status' => '
                 CASE
		WHEN (NurseryReserves.reserve_date < now() AND NurseryReserves.`status` = 2  ) THEN 3
		WHEN (NurseryReserves.reserve_date < now() AND (NurseryReserves.`status` = 1 OR  NurseryReserves.`status` = 0 )) THEN NurseryReserves.approval
		WHEN (NurseryReserves.`status` =2 AND NurseryReserves.approval =4) THEN 4
		WHEN NurseryReserves.`status` = 2 THEN 3
		WHEN (NurseryReserves.approval = 0  AND NurseryReserves.`status` <> 2) THEN 0
		WHEN ( NurseryReserves.approval = 1 AND NurseryReserves.`status` <> 2 ) THEN 1
		WHEN (NurseryReserves.approval = 2 AND NurseryReserves.`status` <> 2) THEN 2
		ELSE 2 END ']);
                $queryCSV = $queryManage->select(['id' => 'NurseryReserves.id', 'reserve_date' => 'NurseryReserves.reserve_date',
                    'reserve_time_start' => 'NurseryReserves.reserve_time_start',
                    'reserve_time_end' => 'NurseryReserves.reserve_time_end', 'user_id' => 'NurseryReserves.user_id',
                    'purpose' => 'NurseryReserves.purpose', 'status' => 'NurseryReserves.status',
                    'approval' => 'NurseryReserves.approval', 'name_k1' => 'NurseryReserves.name_k1', 'name_k2' => 'NurseryReserves.name_k2',
                    'age_year1' => 'NurseryReserves.age_year1', 'age_month1' => 'NurseryReserves.age_month1',
                    'sex1' => 'NurseryReserves.sex1', 'age_year2' => 'NurseryReserves.age_year2', 'age_month2' => 'NurseryReserves.age_month2',
                    'sex2' => 'NurseryReserves.sex2', 'user_name' => 'concat(Users.name_last," ",Users.name_first)',
                    'phone' => 'NurseryReserves.phone', 'created_date' => 'NurseryReserves.created_date',
                    'total' => 'CountFields.total', 'remarks' => 'NurseryReserves.remarks',
                    'modified_date' => 'NurseryReserves.modified_date',
                ])
                    ->join([
                        'Users' => ['table' => 'users', 'type' => 'INNER',
                            'conditions' => 'NurseryReserves.user_id = Users.id'],

                    ]) ->leftJoin(
                        ['CountFields' => $queryManageCount],
                        ["CountFields.reserve_month = DATE_FORMAT(NurseryReserves.reserve_date,'%Y%m')",
                            'CountFields.user_id = NurseryReserves.user_id']
                    )->innerJoin(
                        ['ManageAppvoral' => $queryManageAppvoral],
                        ["ManageAppvoral.id = NurseryReserves.id"]
                    )->where($conditions_CSV)->order($sort_CSV);


                if ($queryCSV->count() > 0) {
                    $now = new \DateTime();
                    Configure::write('debug', false);
                    $dataCSV = $queryCSV->toArray();
                    $this->autoRender = false;
                    ini_set('max_execution_time', 60000);
                    $temp_memory = fopen("php://output", 'w');
                    fprintf($temp_memory, chr(0xEF) . chr(0xBB) . chr(0xBF));
                    header('HTTP/1.1 200 OK');
                    header('Date: ' . date('D M j G:i:s T Y'));
                    header('Last-Modified: ' . date('D M j G:i:s T Y'));
                    header("Content-type: application/vnd . ms-excel;charset=Shift_JIS");
                    header('Content-Disposition: attachement;filename="Nursery_' . date('YmdHis') . '.csv";');
                    $this->myfputcsv($temp_memory, [
                        'ユーザID', 'メンバー名', '予約日付', '時間帯',
                        '申込日時', '最終更新日時', '回数／月', 'お子さんの性別', 'お子さんの月齢',
                        '利用目的', '緊急連絡先電話番号', 'その他留意事項', '連絡時追加文言', '承認'
                    ], ',', '"');
                    foreach ($dataCSV as $item) {
                        if (!empty($item['id'])) {
                            $approval_status = '';

                            $age_year1 = (!isset($item['age_year1']) ? '' : (($item['age_year1'] == 0) ? '' :$item['age_year1'] . '歳'));
                            $phone = !isset($item['phone']) ? '' : $item['phone'];
                            $age_month1 = (!isset($item['age_month1']) ? '' : (($item['age_month1'] == 0) ? '' : $item['age_month1'] . '月'));
                            $age_year2 = (!isset($item['age_year2']) ? '' : (($item['age_year2'] == 0) ? '' : $item['age_year2'] . '歳'));
                            $age_month2 = (!isset($item['age_month2']) ? '' : (($item['age_month2'] == 0 ) ? '' : $item['age_month2'] . '月'));
                            $create_date = date("Y年m月d日", strtotime($item['created_date'])) . '（' . $arr_day_of_week[date("D", strtotime($item['created_date']))] . '）';
                            $modified_date = date("Y年m月d日", strtotime($item['modified_date'])) . '（' . $arr_day_of_week[date("D", strtotime($item['modified_date']))] . '）';
                            $reserve_date = date("Y年m月d日", strtotime($item['reserve_date'])) . '（' . $arr_day_of_week[date("D", strtotime($item['reserve_date']))] . '）';

                            $reserve_time_start = substr_replace($item['reserve_time_start'], ':', 2, 0) . ' ~ ' . substr_replace($item['reserve_time_end'], ':', 2, 0);
                            $total = $item->total ? $item->total:'0';
                            $gender ='';
                            if (!empty($item['sex1']) &&  $item['sex1']==1)
                                $gender ='男の子';
                            else if (!empty($item['sex1']) &&  $item['sex1']==2)
                                $gender ='女の子';

                            if (!empty($item['sex2']))
                                {
                                    if (!empty($item['sex2']) &&  $item['sex2']==1)
                                        $gender .=PHP_EOL.'男の子';
                                    else if (!empty($item['sex2']) &&  $item['sex2']==2)
                                        $gender .=PHP_EOL.'女の子';
                                }
                            $approval_text = $item['approval_text'];

                            if ($item['reserve_date'] < $now) {
                                if ($item['status'] == 2) {
                                    $approval_status = 'キャンセル';
                                } elseif ($item['status'] = 1 || $item['status'] == 0) {
                                    $approval_status = $arr_child_approval[$item['approval']];
                                }
                            } elseif ($item['status'] == 2 && $item['approval'] == 4) {
                                $approval_status = $arr_child_approval[4];
                            } elseif ($item['status'] == 2) {
                                $approval_status = $arr_child_approval[3];
                            } else {
                                if ($item['approval'] == 0) {
                                    $approval_status = $arr_child_approval[0];
                                } elseif ($item['approval'] == 1) {
                                    $approval_status = $arr_child_approval[1];
                                } elseif ($item['approval'] == 2) {
                                    $approval_status = $arr_child_approval[$item['approval']];
                                }
                            }
                            $this->myfputcsv($temp_memory, [
                                $item['user_id'], $item['user_name'],
                                $reserve_date, $reserve_time_start, $create_date, $modified_date, $total,
                                $gender, $age_year1 . $age_month1 . PHP_EOL . $age_year2 . $age_month2, $this->preg_replace_csv($arr_child_purpose[$item['purpose']]),
                                $phone, $this->preg_replace_csv($item['remarks']), $this->preg_replace_csv($approval_text), $approval_status
                            ], ',', '"');
                        }
                    }

                    fclose($temp_memory);
                    //Clear the last empty row.
                    $out = ob_get_contents();
                    ob_end_clean();
                    echo trim($out);
                }
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_052'));
            }
        } elseif ($this->request->is('post') && isset($data['cancel'])) {
            $approval = isset($data['approval'])?$data['approval']:0;
            $approval_text = $data['mail_contact'];
            $child = $data['childs'];
            $updateTransaction = $NurseryReservesTable->getConnection()->transactional(function () use ($data, $NurseryReservesTable, $approval, $approval_text) {
                try {
                    $entity = $NurseryReservesTable->get($data['nursery_reserves_id']);
                    $entity->approval = $approval;
                    $entity->approval_text = $approval_text;
                    $entity->status = $data['status'];
                    $entity->modified_id = $this->LOGINID;
                    $entity->modified_date = date('Y-m-d H:i:s');
                    if (!$NurseryReservesTable->save($entity, ['atomic' => false])) {
                        $NurseryReservesTable->getConnection()->rollback();

                        return false;
                    }
                } catch (RecordNotFoundException $e) {
                    $NurseryReservesTable->getConnection()->rollback();

                    return false;
                }

                return true;
            });
            if ($updateTransaction == true) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
                if ($approval == 1 || $approval == 2) {
                    $queryName = $NurseryReservesTable->find()->select(['email' => 'Users.email', 'user_name' => 'CONCAT(name_last, name_first)',
                        'reserve_date', 'reserve_time_start', 'reserve_time_end', 'remarks' => 'NurseryReserves.remarks'])
                        ->join(['Users' => ['table' => 'users', 'type' => 'INNER',
                            'conditions' => 'NurseryReserves.user_id = Users.id']])
                        ->where(['NurseryReserves.ID' => $data['nursery_reserves_id']])->first();
                    $reserve_date = ($queryName->reserve_date ? date("Y年m月d日", strtotime($queryName->reserve_date)) : '' ) . '（' . $arr_day_of_week[date("D", strtotime($queryName->reserve_date))] . '）';

                    $mailinfos['user_name'] = $queryName->user_name;
                    $mailinfos['email'] = $queryName->email;
                    $mailinfos['childs'] = $child;
                    $mailinfos['remark'] = nl2br($queryName->remarks);
                    $mailinfos['mail_contact'] = nl2br($data['mail_contact']);
                    $mailinfos['reserve_date'] = $reserve_date;
                    $mailinfos['reserve_time_start'] = substr_replace($queryName->reserve_time_start, ':', 2, 0);
                    $mailinfos['reserve_time_end'] = substr_replace($queryName->reserve_time_end, ':', 2, 0);
                    if ($approval == 1) {
                        if ($mailinfos['email']) {
                            $this->sendEmail(
                                Configure::read('from_mailaddress'),
                                $mailinfos['email'],
                                '【Startup Hub Tokyo】一時保育サービス ご予約確定のお知らせ',
                                $mailinfos,
                                'email_nursery_reserves_approval',
                                null,
                                Configure::read('nursery_admin_mailaddress')
                            );
                        }
                    } elseif ($approval == 2) {
                        if ($mailinfos['email']) {
                            $this->sendEmail(
                                Configure::read('from_mailaddress'),
                                $mailinfos['email'],
                                '【Startup Hub Tokyo】一時保育サービス 仮予約についてのご連絡',
                                $mailinfos,
                                'email_nursery_reserves_unapproval',
                                null,
                                Configure::read('nursery_admin_mailaddress')
                            );
                        }
                    }
                }
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }
        } elseif ($this->request->is('post') && isset($data['search'])) {
            if (isset($data['user_id'])) {
                $session->write('search021.user_id', $data['user_id']);
            } else {
                $session->write('search021.user_id', '');
            }
            if (isset($data['user_name'])) {
                $session->write('search021.user_name', $data['user_name']);
            } else {
                $session->write('search021.user_name', '');
            }
            if (isset($data['time_start'])) {
                $session->write('search021.time_start', $data['time_start']);
            } else {
                $session->write('search021.time_start', '');
            }
            if (isset($data['time_end'])) {
                $session->write('search021.time_end', $data['time_end']);
            } else {
                $session->write('search021.time_end', '');
            }
            if (isset($data['ck_approval'])) {
                $session->write('search021.ck_approval', $data['ck_approval']);
            } else {
                $session->write('search021.ck_approval', '-1');
            }

            if (isset($data['orderby']) && $data['orderby'] == '0') {
                $session->write('search021.orderby', null);
            }
        } else {
            if (empty($this->request->query) &&
                (!empty($session->read('search021.orderby')) || !empty($session->read('search021.page')))) {
                $queryRedirect = ['action' => 'reserve'];
                $queryRedirect['page'] = !empty($session->read('search021.page')) ? $session->read('search021.page') : 1;
                if (!empty($session->read('search021.orderby'))) {
                    $order = explode(' ', $session->read('search021.orderby'));
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
            if (!in_array($this->request->query['sort'], ['user_id', 'user_name', 'user_name', 'created_date', 'modified_date', 'reserve_date', 'total'])) {
                $this->request->query['sort'] = 'id';
            }
            if (strtolower($this->request->query['direction']) != "asc" && strtolower($this->request->query['direction']) != "desc") {
                $this->request->query['direction'] = 'asc';
            }
            $session->write('search021.orderby', $this->request->query['sort'] . ' ' . $this->request->query['direction']);
        }
        if (!empty($this->request->query['page'])) {
            /* validation */
            if (!preg_match('/^\d+$/', $this->request->query['page'])) {
                $this->request->query['page'] = 1;
            }
            $session->write('search021.page', $this->request->query['page']);
        } else {
            $session->write('search021.page', 1);
        }

        $orderby = !empty($session->read('search021.orderby')) ? $session->read('search021.orderby') : 'id desc';

        $page = !empty($session->read('search021.page')) ? $session->read('search021.page') : 1;
        $user_id = !is_null($session->read('search021.user_id')) ? $session->read('search021.user_id') : '';
        $user_name = !is_null($session->read('search021.user_name')) ? $session->read('search021.user_name') : '';
        $ck_approval = !is_null($session->read('search021.ck_approval')) ? $session->read('search021.ck_approval') : '-1';
        $timeStart = !is_null($session->read('search021.time_start')) ? $session->read('search021.time_start') : '';
        $timeEnd = !is_null($session->read('search021.time_end')) ? $session->read('search021.time_end') : '';
        $this->set(compact(['user_id', 'user_name', 'ck_approval', 'timeStart', 'timeEnd']));
        $order = explode(' ', $orderby);
        $array_query = [];
        if (isset($user_id) && !empty($user_id)) {
            array_push($array_query, [['NurseryReserves.user_id' => $user_id]]);
        }
        if (isset($user_name) && !empty($user_name)) {
            $array_query["CONCAT(hiragana_name_last,' ',hiragana_name_first) LIKE"] = '%' . $user_name . '%';
        }
        if (!empty($timeStart)) {
            $array_query["NurseryReserves.reserve_date >="] = date('Y-m-d', strtotime($timeStart));
        }
        if (!empty($timeEnd)) {
            $array_query["NurseryReserves.reserve_date <="] = date('Y-m-d', strtotime($timeEnd));
        }
        if ($ck_approval != '' && $ck_approval > -1) {
            $array_query["approval_status ="] = $ck_approval;
        }
        $queryManageCount = $NurseryReservesTable->find()->select(['id' => 'NurseryReserves.id',
            'reserve_date' => 'NurseryReserves.reserve_date',
            'reserve_month' => "DATE_FORMAT(NurseryReserves.reserve_date,'%Y%m')",
            'user_id' => 'NurseryReserves.user_id',
            'total' => $queryManage->func()->count('NurseryReserves.id')
        ]) ->join([
            'Users' => ['table' => 'users', 'type' => 'INNER',
                'conditions' => 'NurseryReserves.user_id = Users.id'],
        ])
            ->where([ 'NurseryReserves.approval' => 1])
            ->group(['reserve_month', 'NurseryReserves.user_id']);
        $queryManageAppvoral = $NurseryReservesTable->find()->select(['id' => 'NurseryReserves.id', 'approval_status' => '
        CASE
		WHEN (NurseryReserves.reserve_date < now() AND NurseryReserves.`status` = 2  ) THEN 3
		WHEN (NurseryReserves.reserve_date < now() AND (NurseryReserves.`status` = 1 OR  NurseryReserves.`status` = 0 )) THEN NurseryReserves.approval
		WHEN (NurseryReserves.`status` =2 AND NurseryReserves.approval =4) THEN 4
		WHEN NurseryReserves.`status` = 2 THEN 3
		WHEN (NurseryReserves.approval = 0  AND NurseryReserves.`status` <> 2) THEN 0
		WHEN ( NurseryReserves.approval = 1 AND NurseryReserves.`status` <> 2 ) THEN 1
		WHEN (NurseryReserves.approval = 2 AND NurseryReserves.`status` <> 2) THEN 2
		ELSE 2 END  ']);
        $queryManage = $queryManage->select(['id' => 'NurseryReserves.id', 'reserve_date' => 'NurseryReserves.reserve_date',
            'reserve_time_start' => 'NurseryReserves.reserve_time_start',
            'reserve_time_end' => 'NurseryReserves.reserve_time_end', 'user_id' => 'NurseryReserves.user_id',
            'purpose' => 'NurseryReserves.purpose', 'status' => 'NurseryReserves.status',
            'approval' => 'NurseryReserves.approval', 'name_k1' => 'NurseryReserves.name_k1', 'name_k2' => 'NurseryReserves.name_k2',
            'age_year1' => 'NurseryReserves.age_year1', 'age_month1' => 'NurseryReserves.age_month1',
            'sex1' => 'NurseryReserves.sex1', 'age_year2' => 'NurseryReserves.age_year2', 'age_month2' => 'NurseryReserves.age_month2',
            'sex2' => 'NurseryReserves.sex2', 'user_name' => 'concat(Users.name_last," ",Users.name_first)',
            'phone' => 'NurseryReserves.phone', 'created_date' => 'NurseryReserves.created_date',
            'total' => 'CountFields.total', 'hiragana_name' => 'concat(Users.hiragana_name_last," ",Users.hiragana_name_first)',
            'approval_status' => 'ManageAppvoral.approval_status', 'remarks' => 'NurseryReserves.remarks',
            'modified_date' => 'NurseryReserves.modified_date', 'approval_text' => 'NurseryReserves.approval_text'
        ])
            ->join([
                'Users' => ['table' => 'users', 'type' => 'INNER',
                    'conditions' => 'NurseryReserves.user_id = Users.id'],

            ]) ->leftJoin(
                ['CountFields' => $queryManageCount],
                ["CountFields.reserve_month = DATE_FORMAT(NurseryReserves.reserve_date,'%Y%m')",

                'CountFields.user_id = NurseryReserves.user_id']
            ) ->innerJoin(
                ['ManageAppvoral' => $queryManageAppvoral],
                ["ManageAppvoral.id = NurseryReserves.id"]
            ) ->where([$array_query]);
        $pagingConfig = ['contain' => ['Users'],
            'page' => $page,
            'limit' => Configure::read('list_max_row'),
            'sortWhitelist' => [
                'user_name',
                'user_id',
                'created_date',
                'reserve_date',
                'modified_date',
                'total',

            ]

        ];

        if (empty($array_query)) {
            $array_query["NurseryReserves.id >"] = 0;
        };
        $this->set('csvwhere', json_encode($array_query));
        $this->set('csvsort', $orderby);
        if ($order[0] != 'id') {
            $pagingConfig['sort'] = $order[0];
            if (!empty($order[1])) {
                $pagingConfig['direction'] = $order[1];
            }
        } else {
            $pagingConfig['order'] = ['modified_date' => 'desc', 'reserve_date' => 'desc', 'reserve_time_start' => 'asc'];
        }

        try {
            $nurseryreserves = $this->Paginator->paginate($queryManage, $pagingConfig);
        } catch (NotFoundException $e) {
            return $this->redirect([
                'action' => 'reserve',
                'page' => 1,
                'sort' => $pagingConfig['sort'],
                'direction' => !empty($pagingConfig['direction']) ? $pagingConfig['direction'] : ''
            ]);
        }
        $this->set(compact(['pagingConfig', 'nurseryreserves', 'page']));
    }
    /**
     * Show reserve
     * Index page /management/nursery/schedule/
     * @query string|null $p page of list
     * @author: ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function schedule()
    {
        $this->checkAuth();
        $session = $this->request->session();
        $NurseryScheduleTable = TableRegistry::get('NurserySchedule');
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $updateTransaction = $NurseryScheduleTable->getConnection()->transactional(function () use ($data, $NurseryScheduleTable) {
                try {
                    if (isset($data['cancel_nursery']) && $data['cancel_nursery'] == 1) {
                        $entity = $NurseryScheduleTable->get($data['nursery_schedule_id']);

                        if (!$NurseryScheduleTable->delete($entity, ['atomic' => false])) {
                            $NurseryScheduleTable->getConnection()->rollback();

                            return false;
                        }
                    } elseif (isset($data['change_date']) && $data['change_date'] == 1) {
                        $entity = $NurseryScheduleTable->get($data['nursery_schedule_id']);

                        $entity->limit_date = Date('Y-m-d', strtotime($data['limit_date']));

                        if (!$NurseryScheduleTable->save($entity, ['atomic' => false])) {
                            $NurseryScheduleTable->getConnection()->rollback();

                            return false;
                        }
                    } elseif (isset($data['cancel_date']) && $data['cancel_date'] == 1) {
                        $entity = $NurseryScheduleTable->get($data['nursery_schedule_id']);

                        $entity->cancel_limit_date = Date('Y-m-d', strtotime($data['cancel_limit_date']));

                        if (!$NurseryScheduleTable->save($entity, ['atomic' => false])) {
                            $NurseryScheduleTable->getConnection()->rollback();

                            return false;
                        }
                    }
                } catch (RecordNotFoundException $e) {
                    $NurseryScheduleTable->getConnection()->rollback();

                    return false;
                }

                return true;
            });

            if ($updateTransaction) {
                $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
            } else {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
            }
        }
        $now = new \DateTime();
        $queryManage = $NurseryScheduleTable->find();
        $nurseryschedule = $queryManage->select(['id' => 'NurserySchedule.id', 'rid' => 'NurseryReserves.id', 'reserve_date' => 'NurserySchedule.reserve_date',
            'limit_date' => 'NurserySchedule.limit_date', 'cancel_limit_date' => 'NurserySchedule.cancel_limit_date' ])
            ->join(['NurseryReserves' => ['table' => 'nursery_reserves', 'type' => 'LEFT',
                'conditions' => 'NurseryReserves.reserve_date = NurserySchedule.reserve_date']])
            ->where(['NurserySchedule.reserve_date >=' => $now->format('Y-m-d')])
            ->group(['NurserySchedule.reserve_date'])
            ->order(['NurserySchedule.reserve_date' => 'DESC']);
        $this->set(compact([ 'nurseryschedule']));
    }

    /**
     * send email test
     * Index page /management/nursery/schedule/
     * @author: ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    private function sendemailtext()
    {
        $arr_day_of_week = Configure::read('DAYOFWEEK');
        $NurseryReservesTable = TableRegistry::get('NurseryReserves');
        $queryName = $NurseryReservesTable->find()->select(['email' => 'Users.email', 'user_name' => 'CONCAT(name_last, name_first)',
            'reserve_date', 'reserve_time_start', 'reserve_time_end', 'remarks' => 'NurseryReserves.remarks'])
            ->join(['Users' => ['table' => 'users', 'type' => 'INNER',
                'conditions' => 'NurseryReserves.user_id = Users.id']])
            ->where(['NurseryReserves.ID' => 119])->first();
        $reserve_date = ($queryName->reserve_date ? date("Y年m月d日", strtotime($queryName->reserve_date)) : '' ) . '（' . $arr_day_of_week[date("D", strtotime($queryName->reserve_date))] . '）';

        $mailinfos['user_name'] = $queryName->user_name;
        $mailinfos['email'] = $queryName->email;
        $mailinfos['childs'] = 'childs test';
        $mailinfos['remark'] = nl2br($queryName->remarks);
        $mailinfos['mail_contact'] = 'SEND MAIL TEXT';
        $mailinfos['reserve_date'] = $reserve_date;
        $mailinfos['reserve_time_start'] = substr_replace($queryName->reserve_time_start, ':', 2, 0);
        $mailinfos['reserve_time_end'] = substr_replace($queryName->reserve_time_end, ':', 2, 0);
        if ($mailinfos['email']) {
            $this->sendEmail(
                Configure::read('from_mailaddress'),
                'hong.tho@primelabo.com.vn',
                '【Startup Hub Tokyo】一時保育サービス 仮予約についてのご連絡',
                $mailinfos,
                'email_nursery_reserves_unapproval',
                null,
                'tan.thuan@primelabo.com.vn'
            );
        }
    }

    /**
     * Show setting
     * Index page /management/nursery/setting/
     * @query string|null $p page of list
     * @author: ThoVo
     * @return \Cake\Network\Response|void : boolean
     */
    public function setting()
    {
        $this->checkAuth();
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
        $NurseryScheduleTable = TableRegistry::get('NurserySchedule');
        if ($this->request->is('post')) {
            $data = $this->request->data;
            $queryManage = $NurseryScheduleTable->find();
            $nurseryschedule = $queryManage->select(['id' => 'NurserySchedule.id', 'rid' => 'NurseryReserves.id', 'reserve_date' => 'NurserySchedule.reserve_date', 'approval' => 'NurseryReserves.approval' ])
                ->join(['NurseryReserves' => ['table' => 'nursery_reserves', 'type' => 'LEFT',
                    'conditions' => 'NurseryReserves.reserve_date= NurserySchedule.reserve_date']])
                ->where([
                    'NurserySchedule.reserve_date >=' => $initsdate,
                    'NurserySchedule.reserve_date <= ' => $initedate
                ])
                ->order(['NurserySchedule.reserve_date' => 'DESC']);

            $arrayNS = [];
            foreach ($nurseryschedule as $item) {
                if (!isset($arrayNS[$item->id])) {
                    $arrayNS[$item->id] = $item;
                }
            }

            $validate = true;
            for ($i = 1; $i <= 31; $i++) {
                $day_ = $i < 10?'0' . $i:$i;
                if (isset($data['s_flg_' . $day_]) && $data['s_flg_' . $day_] == 0) {
                    if (!isset($data['day_' . $day_])) {
                        $validate = false;
                        break;
                    }
                }
            }

            if (!$validate) {
                $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_060'));
            } else {
                $updateTransaction = $NurseryScheduleTable->getConnection()->transactional(function () use ($data, $arrayNS, $NurseryScheduleTable, $nurseryschedule, $initsdate) {

                    try {
                        foreach ($nurseryschedule as $item) {
                            if (!$item->rid || ($item->rid > 0 && ($item->appvoral != 1 || $item->appvoral != 0 ) )) {
                                $day_ = Date('d', strtotime($item->reserve_date));
                                if (!isset($data['day_' . $day_])) {
                                    if (isset($arrayNS[$item->id])) {
                                        $entity = $NurseryScheduleTable->get($item->id);
                                        if (isset($entity)) {
                                            if (!$NurseryScheduleTable->delete($entity, ['atomic' => false])) {
                                                $NurseryScheduleTable->getConnection()->rollback();

                                                return false;
                                            } else {
                                                unset($arrayNS[$item->id]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        for ($i = 1; $i <= 31; $i++) {
                            $day_ = $i < 10 ? '0' . $i : $i;
                            if (isset($data['day_' . $day_]) && $data['day_' . $day_] == 0) {
                                $entity = $NurseryScheduleTable->newEntity();
                                $entity->reserve_date = Date('Y-m', strtotime($initsdate)) . '-' . $day_;
                                $limit_date = '';
                                $shorttime_flg = 0;
                                $Day_Of_Week = date("w", strtotime($entity->reserve_date));
                                //$cancel_limit_date = Date('Y-m-d', strtotime('-2 day', strtotime($entity->reserve_date)));
                                if ($Day_Of_Week == 1) {
                                    // 指定されたカレンダーの曜日が月の場合
                                    $limit_date = Date('Y-m-d', strtotime('-10 day', strtotime($entity->reserve_date)));
                                } elseif ($Day_Of_Week > 0) {
                                    // 指定されたカレンダーの曜日が火・水・木・金・土の場合
                                    $limit_date = Date('Y-m-d', strtotime('-8 day', strtotime($entity->reserve_date)));
                                } else {
                                    // 指定されたカレンダーの曜日が日の場合
                                    $limit_date = Date('Y-m-d', strtotime('-9 day', strtotime($entity->reserve_date)));
                                }
                                if (isset($data['s_flg_' . $day_]) && $data['s_flg_' . $day_] == 0) {
                                    $shorttime_flg = 1;
                                }
                                if ($Day_Of_Week == 0) {
                                    $cancel_limit_date = Date('Y-m-d', strtotime('-4 day', strtotime($entity->reserve_date)));
                                } elseif (in_array($Day_Of_Week, [1, 2, 3])) {
                                    $cancel_limit_date = Date('Y-m-d', strtotime('-5 day', strtotime($entity->reserve_date)));
                                } elseif (in_array($Day_Of_Week, [4, 5, 6])) {
                                    $cancel_limit_date = Date('Y-m-d', strtotime('-3 day', strtotime($entity->reserve_date)));
                                }

                                $entity->limit_date = $limit_date;
                                $entity->shorttime_flg = $shorttime_flg;
                                $entity->cancel_limit_date = $cancel_limit_date;
                                $entity->created_id = $this->LOGINID;
                                $entity->created_date = date('Y-m-d H:i:s');
                                $entity->modified_id = $this->LOGINID;
                                $entity->modified_date = date('Y-m-d H:i:s');
                                if (!$NurseryScheduleTable->save($entity, ['atomic' => false])) {
                                    $NurseryScheduleTable->getConnection()->rollback();

                                    return false;
                                }
                            } elseif (isset($data['day_' . $day_]) && $data['day_' . $day_] > 0) {
                                if (isset($data['s_flg_' . $day_]) && $data['s_flg_' . $day_] == 0) {
                                    $entity = $NurseryScheduleTable->get($data['day_' . $day_]);
                                    $cancel_limit_date = $entity->reserve_date;
                                    $Day_Of_Week = date("w", strtotime($entity->reserve_date));
                                    if ($Day_Of_Week == 0) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-4 day', strtotime($entity->reserve_date)));
                                    } elseif (in_array($Day_Of_Week, [1, 2, 3])) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-5 day', strtotime($entity->reserve_date)));
                                    } elseif (in_array($Day_Of_Week, [4, 5, 6])) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-3 day', strtotime($entity->reserve_date)));
                                    }
                                    if (isset($entity)) {
                                        $entity->shorttime_flg = 1;
                                        $entity->cancel_limit_date = $cancel_limit_date;
                                        $entity->modified_id = $this->LOGINID;
                                        $entity->modified_date = date('Y-m-d H:i:s');
                                        if (!$NurseryScheduleTable->save($entity, ['atomic' => false])) {
                                            $NurseryScheduleTable->getConnection()->rollback();

                                            return false;
                                        }
                                    }
                                } elseif (!isset($data['s_flg_' . $day_])) {
                                    $entity = $NurseryScheduleTable->get($data['day_' . $day_]);
                                    $Day_Of_Week = date("w", strtotime($entity->reserve_date));
                                    if ($Day_Of_Week == 0) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-4 day', strtotime($entity->reserve_date)));
                                    } elseif (in_array($Day_Of_Week, [1, 2, 3])) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-5 day', strtotime($entity->reserve_date)));
                                    } elseif (in_array($Day_Of_Week, [4, 5, 6])) {
                                        $cancel_limit_date = Date('Y-m-d', strtotime('-3 day', strtotime($entity->reserve_date)));
                                    }
                                    if (isset($entity)) {
                                        $entity->shorttime_flg = 0;
                                        $entity->cancel_limit_date = $cancel_limit_date;
                                        $entity->modified_id = $this->LOGINID;
                                        $entity->modified_date = date('Y-m-d H:i:s');
                                        if (!$NurseryScheduleTable->save($entity, ['atomic' => false])) {
                                            $NurseryScheduleTable->getConnection()->rollback();

                                            return false;
                                        }
                                    }
                                }
                            }
                        }
                    } catch (RecordNotFoundException $e) {
                        $NurseryScheduleTable->getConnection()->rollback();

                        return false;
                    }

                    return true;
                });
                if ($updateTransaction) {
                    $this->Flash->success(Configure::read('MESSAGE_NOTIFICATION.MSG_028'));
                } else {
                    $this->Flash->error(Configure::read('MESSAGE_NOTIFICATION.MSG_031'));
                }
            }
        }
        $queryManage = $NurseryScheduleTable->find();
        $nurseryschedule = $queryManage->select(['id' => 'NurserySchedule.id', 'rid' => 'NurseryReserves.id',
            'cancel_limit_date' => 'NurserySchedule.cancel_limit_date', 'shorttime_flg' => 'NurserySchedule.shorttime_flg',
            'reserve_date' => 'NurserySchedule.reserve_date', 'approval' => 'NurseryReserves.approval' ])
            ->join(['NurseryReserves' => ['table' => 'nursery_reserves', 'type' => 'LEFT',
                'conditions' => 'NurseryReserves.reserve_date = NurserySchedule.reserve_date']])
            ->where([
                'NurserySchedule.reserve_date >=' => $initsdate,
                'NurserySchedule.reserve_date <= ' => $initedate])

            ->order(['NurserySchedule.reserve_date' => 'DESC']);
        $this->set(compact([ 'nurseryschedule']));
        $this->set(['currentDate', $currentDate]);
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
     * preg_replace string csv
     * @param string|null content
     * @return string
     */

    private function preg_replace_csv($content)
    {
        if (!empty($content))
            return preg_replace("/[\"]+/", '""', $content);
        else
            return '';
    }
}
