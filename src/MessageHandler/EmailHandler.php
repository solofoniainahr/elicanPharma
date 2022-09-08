<?php

// src/MessageHandler/SmsNotificationHandler.php
namespace App\MessageHandler;

use App\Services\SendMail;
use App\Message\SmsNotification;
use App\Message\EmailNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;


class EmailHandler implements MessageHandlerInterface
{
    private $sendMail;

    public function __construct(SendMail $sendMail)
    {
        $this->sendMail = $sendMail;
    }

    public function __invoke(EmailNotification $message)
    {
        $this->sendMail->sendValidationCode();
    }
}