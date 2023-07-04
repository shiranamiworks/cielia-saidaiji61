<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH.'third_party/qdmail/qdmail.php';
require_once APPPATH.'third_party/qdmail/qdsmtp.php';

/**
 * Mail Library
 *
 * @author localdisk<info@localdisk.org>
 */
class Mailer {

    /**
     * mailer
     * 
     * @var Qdmail
     */
    private $_mailer;

    public function __construct($option = array()) {
        $this->_mailer = new Qdmail();
        $this->initialize($option);
    }

    /**
     * initialize
     * 
     * @param array $config 
     */
    public function initialize($config = array()) {
        if (isset($config['protocol']) && strtolower($config['protocol']) === 'smtp') {
            $this->_mailer->smtp(TRUE);
            $param = array();
            if ($config['smtp_user'] === '' && $config['smtp_pass'] === '') {
                $param['protocol'] = 'SMTP';
            } else {
                $param['protocol'] = 'SMTP_AUTH';
                $param['user'] = $config['smtp_user'];
                $param['pass'] = $config['smtp_pass'];
                
            }
            $param['host'] = $config['smtp_host'];
            $param['port'] = (isset($config['smtp_port']) === FALSE) ? 25 : $config['smtp_port'];
            $this->_mailer->smtpServer($param);
        }
        $this->_mailer->lineFeed("\n"); // qmail対策
    }

    /**
     * to
     * 
     * @param  mixed  $addr
     * @param  mixed  $name
     * @param  boolean $add
     * @return Mailer 
     */
    public function to($addr = null, $name = null, $add = FALSE) {
        $this->_mailer->to($addr, $name, $add);
        return $this;
    }

    /**
     * subject
     * 
     * @param  string $subj
     * @return Mailer 
     */
    public function subject($subj = null) {
        $this->_mailer->subject($subj);
        return $this;
    }

    /**
     * text
     * 
     * @param  string $cont
     * @param  string $length
     * @param  string $charset
     * @param  string $enc
     * @param  string $org_charset
     * @return Mailer 
     */
    public function text($cont, $length = null, $charset = null, $enc = null, $org_charset = null) {
        $this->_mailer->text($cont, $length, $charset, $enc, $org_charset);
        return $this;
    }

    /**
     * message
     * Email Libarary の互換メソッド
     * 
     * @param  string $body
     * @return Mailer 
     */
    public function message($body) {
        $this->text($body);
        return $this;
    }

    /**
     * from
     * 
     * @param  string $addr
     * @param  string $name
     * @return Mailer 
     */
    public function from($addr = null, $name = null) {
        $this->_mailer->from($addr, $name);
        return $this;
    }

    /**
     * send
     * 
     * @param mixed $option 
     */
    public function send($option = null) {
        return $this->_mailer->send($option);
    }

    /**
     * cc
     * 
     * @param  mixed   $addr
     * @param  mixed   $name
     * @param  boolean $add
     * @return Mailer 
     */
    public function cc($addr = null, $name = null, $add = false) {
        $this->_mailer->cc($addr, $name, $add);
        return $this;
    }

    /**
     * bcc
     * 
     * @param  mixed   $addr
     * @param  mixed   $name
     * @param  boolean $add
     * @return Mailer 
     */
    public function bcc($addr = null, $name = null, $add = false) {
        $this->_mailer->bcc($addr, $name, $add);
        return $this;
    }

    /**
     * reply_to
     * 
     * @param  mixed   $addr
     * @param  mixed   $name
     * @return Mailer 
     */
    public function reply_to($addr = null, $name = null) {
        $this->_mailer->replyto($addr, $name);
        return $this;
    }

    /**
     * clear
     * 
     * @return Mailer
     */
    public function clear() {
        $this->_mailer->reset();
        return $this;
    }

    /**
     * attach
     * 
     * @param  string  $param
     * @param  boolean $add
     * @return Mailer 
     */
    public function attach($param, $add = false) {
        $this->_mailer->attach($param, $add);
        return $this;
    }

    /**
     * print_debugger
     * 
     * @return string 
     */
    public function print_debugger() {
        $msg = '<pre>';
        $msg =  'to:  '          . print_r($this->_mailer->to, TRUE)           . "\n";
        $msg .= 'cc:'            . print_r($this->_mailer->cc, TRUE)           . "\n";
        $msg .= 'bcc:'           . print_r($this->_mailer->bcc, TRUE)          . "\n";
        $msg .= 'from:'          . print_r($this->_mailer->from, TRUE)         . "\n";
        $msg .= 'replyto:'       . print_r($this->_mailer->replyto, TRUE)      . "\n";
        $msg .= 'otherheader:'   . print_r($this->_mailer->other_header, TRUE) . "\n";
        $msg .= 'subject:'       . print_r($this->_mailer->subject, TRUE)      . "\n";
        $msg .= 'subject:'       . print_r($this->_mailer->subject, TRUE)      . "\n";
        $msg .= 'body:'          . print_r($this->_mailer->content, TRUE)      . "\n";
        $msg .= '</pre>';
        return $msg;
    }

    /**
     * __call
     * 
     * @param mixed $name
     * @param mixed $arguments 
     * @return mixed
     */
    public function __call($name, $arguments) {
        if (!method_exists($this->_mailer, $name)) {
            return  FALSE;
        }
        if (!is_callable(array($this->_mailer, $name), TRUE)) {
            return FALSE;
        }
        return call_user_func_array(array($this->_mailer, $name), $arguments);
    }
}