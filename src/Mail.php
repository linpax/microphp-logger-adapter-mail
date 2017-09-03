<?php
/**
 * Created by PhpStorm.
 * User: casper
 * Date: 03.09.17
 * Time: 6:36
 */

namespace Micro\Logger\Adapter\Mail;

use Micro\Logger\AdapterInterface;
use Micro\Mail\TransportInterface;


class Mail implements AdapterInterface
{
    /** @var TransportInterface $driver */
    private $driver;
    private $supportedLevels = [];

    private $email;
    private $template = '{{$level}}: {{$message}} | {{$context}';


    public function __construct(array $config = [])
    {
        if (!empty($config['levels'])) {
            $this->supportedLevels = $config['levels'];
        }
        if (!empty($config['email'])) {
            $this->email = $config['email'];
        }
        if (!empty($config['template'])) {
            $this->template = $config['template'];
        }
    }

    public function setDriver(TransportInterface $driver)
    {
        $this->driver = $driver;
    }


    public function log($level, $message, array $context = array())
    {
        $mail = new \Micro\Mail\Mail;
        $mail->setTo($this->email);
        $mail->setText(str_replace([
            '{{$level}}', '{{$message}}', '{{$context}}'
        ],[
            $level, $message, print_r($context, true)
        ], $this->template));

        $this->driver->send($mail);
    }

    public function isSupportedLevel($name)
    {
        if (empty($this->supportedLevels)) {
            return true;
        }

        return in_array($name, $this->supportedLevels, false);
    }
}