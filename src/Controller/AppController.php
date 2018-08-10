<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Network\Email\Email;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    /** @var array $helpers custom common helper for paging */
    public $helpers = ['Html' => ['className' => 'Sht']];
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        //$this->loadComponent('Security');
        $this->loadComponent('Csrf');
    }
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
    }
    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    /**
     * Description: send the gas stop information
     * Function: sendEmail
     * @author: Dat
     * @param null $from admin
     * @param null $to User
     * @param null $subject of mail
     * @param null $msg array variables
     * @param null $template of email
     * @param null $from_name of sender
     * @param null $cc for other
     * @return void
     */
    protected function sendEmail($from = null, $to = null, $subject = null, $msg = null, $template = null, $from_name = null, $cc = null)
    {
        $this->set($msg);
        $email = new Email();
        $email->transport('mailsht');
        try {
            $email->helpers(['Html']);
            $email->template($template);
            $email->to($to);
            if (!empty($cc)) {
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
            $this->Flash->error($e->getMessage());
        }
    }
}
