<?php
/**
 * Small component for sending emails.
 * Allows you to define multiple views, which may receive specific parameters to be used in it.
 * Allows sending to multiple recipients.
 * Usage:
 * <?php Yii::app()->controller->widget('ext.easy-mail.Mail', 
 *     array(
 *         'view' => 'testView',
 *         'params' => array(
 *             'to' => array(
 *                 'email@example.com' => 'Name'
 *             ),
 *             'content' => array(
 *                 'param1' => 'Value1',
 *                 'paramN' => 'ValueN'
 *             ),
 *             'subject' => 'Subject of email'
 *         )
 *     )); ?>  
 * @version 1.0
 * @author Rafael J Torres <rafaelt88@gmail.com>
 * @copyright (c) 2014 Rafael J Torres
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 */

require_once dirname(__FILE__) . '/models/PHPMailer.php';

class Mail extends CWidget {
    public $mail;
    public $view;
    public $params;

    public function init() {
        $this->mail = new PHPMailer();
        $this->mail->Host = 'localhost';
        $this->mail->Username = 'noreply@sistemas2.sead.ufscar.br';
        $this->mail->Password = '';
        $this->mail->Mailer = 'smtp';
        $this->mail->Port = 465;
        $this->mail->SMTPAuth = true;
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->CharSet = 'utf-8';
        $this->mail->ContentType = 'text/html';
    }

    public function run() {
        $this->setFrom();
        $this->setTo();
        $this->setCc();
        $this->setSubject();
        $this->setBody();
        $this->mail->Send();
    }

    public function setFrom() {
		$this->mail->setFrom('edutec@ead.ufscar.br', 'Coordenação EDUTEC');
        // $this->mail->SetFrom($this->mail->Username, Yii::app()->name);
    }

    public function setTo() {
        foreach ($this->params['to'] as $email => $name) {
            $this->mail->AddAddress($email, $name);
        }
    }

    public function setCc() {
        if (isset($this->params['cc'])) {
            foreach ($this->params['cc'] as $email => $name) {
                $this->mail->AddCC($email, $name);
            }
        }
    }

    public function setSubject() {
        if (isset($this->params['subject'])) {
            $this->mail->Subject = $this->params['subject'];
        } else {
            $this->mail->Subject = $this->t($this->view);
        }
    }

    public function setBody() {
        $this->mail->MsgHTML($this->render($this->view, array(
            'params' => $this->params
        ), true));
    }

}
