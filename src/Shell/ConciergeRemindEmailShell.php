<?php
/**
 * コンシェルジュ相談リマインドメール送信
 * @author Huynh
 *
 */

namespace App\Shell;

use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Network\Email\Email;
use Cake\ORM\TableRegistry;

class ConciergeRemindEmailShell extends Shell
{
    /**
     * main() method.
     * @author Huynh
     * @return bool|int Success or error code.
     */
    public function main()
    {
        $reservesTable = TableRegistry::get('Reserves');
        $query = $reservesTable->find();
        $query = $query->select([
            'Reserves.work_date', 'Reserves.work_time_start', 'Reserves.work_time_start', 'Reserves.work_time_end', 'Reserves.user_id', 'Reserves.concierge_id', 'Reserves.reserve_status', 'Reserves.cancel_status',
            'Users.id', 'Users.name_last', 'Users.name_first', 'Users.type', 'Users.email',
            'Concierges.id', 'Concierges.name',
        ])
            ->join([
                'Concierges' => [
                        'table' => 'concierges',
                        'type' => 'INNER',
                        'conditions' => ['Reserves.concierge_id = Concierges.id', 'Concierges.avail_flg' => 1]
                ],
                'Users' => [
                    'table' => 'users',
                    'type' => 'INNER',
                    'conditions' => 'Reserves.user_id = Users.id'
                ],
            ])
            ->where([
                'Reserves.work_date' => date('Y-m-d', strtotime('+2 days')),
                'Reserves.reserve_status <>' => 9
            ]);
        if ($query->count() <= 0) {
            return false;
        }
        $adminEmail = Configure::read('admin_mailaddress');
        foreach ($query as $item) {
            $work_time_start = substr($item->work_time_start, 0, 2) . ':' . substr($item->work_time_start, 2, 2);
            $work_time_end = substr($item->work_time_end, 0, 2) . ':' . substr($item->work_time_end, 2, 2);
            $this->__sendEmail(
                Configure::read('from_mailaddress'),
                $item->Users['email'],
                '【Startup Hub Tokyo】コンシェルジュ相談ご予約日のお知らせ',
                [
                    'user_name' => $item->Users['name_last'] . ' ' . $item->Users['name_first'],
                    'work_date' => $item['work_date'],
                    'work_time_start' => $work_time_start,
                    'work_time_end' => $work_time_end,
                    'concierge_name' => $item->Concierges['name'],
                ],
                'email_cron_concierge_reserves_reminder',
                null,
                $adminEmail
            );
        }

        return false;
    }
    
    /**
     * プレアントレメンバー期間満了メール
     */
	public function sendExpirationDateMail()
	{
		$currMonth = date('Ym');
		
		$preentreRequests = TableRegistry::get('PreentreRequests');
		$query = $preentreRequests
			->find('all')
			->contain(['Users'])
			->where(["FROM_UNIXTIME(PreentreRequests.updated_at, '%Y%m') = '{$currMonth}'"]);
		
		foreach($query as $item){
			$this->__sendEmail(
				Configure::read('from_mailaddress'),
				$item->user->email,
				'【Startup Hub Tokyo】プレアントレメンバー期間満了のお知らせ',
				[
					'user_name' => $item->user->name_last . ' ' . $item->user->name_first,
					'email' => $item->user->email,
					'expiration_date' => date('Y/m/d', $item->updated_at)
				],
				'email_preentre_expiration_date',
				null,
				'pre-entre@startuphub.tokyo'
			);
		}
		
		return;
	}

    /**
     * Description: send the email
     * @param null $from admin
     * @param null $to User
     * @param null $subject of mail
     * @param null $msg array variables
     * @param null $template of email
     * @param null $from_name of sender
     * @param null $cc for other
     * @return void
     */
    private function __sendEmail($from = null, $to = null, $subject = null, $msg = null, $template = null, $from_name = null, $cc = null)
    {
        $email = new Email();
        $email->transport('mailsht');
        try {
            $email->helpers(['Html']);
            $email->template($template);
            $email->to($to);
            if ($cc !== null) {
                $email->bcc($cc);
            }
            $email->emailFormat('html');
            $email->subject($subject);
            if ($from_name !== null) {
                $email->from($from, $from_name);
            } else {
                $email->from($from);
            }

            $email->viewVars($msg);
            $email->send();
        } catch (\Exception $e) {
            echo $e->getMessage();
            Log::error($e->getMessage());
        }
    }
}
