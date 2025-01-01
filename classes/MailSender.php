<?php namespace ProFixS\Forms\Classes;

use ProFixS\Forms\Models\Inbox;
use Mail;
use System\Models\MailSetting;

class MailSender
{
    protected $inbox;
    protected $mailSettings;

    /**
     * __construct
     */
    public function __construct(Inbox $inbox)
    {
        $this->inbox = $inbox;
        $this->mailSettings = MailSetting::instance();
    }

    /**
     * queue
     */
    public function queue()
    {
        return Mail::queue(
            // set email template
            $this->inbox->form->template->code,
            // set fields for email template
            ['fields' => $this->inbox->fields],
            function ($message) {
                // set emails
                $message->from($this->mailSettings->sender_email, $this->mailSettings->sender_name);
                $message->to(array_pluck($this->inbox->form->emails, 'email'));

                // attach files
                foreach ($this->inbox->files as $file) {
                    $message->attach($file->getLocalPath());
                }
            }
        );
    }
}

