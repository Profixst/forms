<?php namespace ProFixS\Forms\Jobs;

use Exception;
use ProFixS\Forms\Models\Inbox;
use ProFixS\MultiLanguage\Classes\MultiLanguage;
use ProFixS\MultiSite\Classes\MultiSite;
use Mail;
use System\Classes\MailManager;
use System\Classes\PluginManager;
use System\Models\MailSetting;

/**
* SendFormMail
*/
class SendFormMail
{
    /**
     * fire
     */
    public function fire($job, array $data)
    {
        try {
            $inbox = Inbox::withoutGlobalScopes()
                ->findOrFail($data['inbox_id']);
            
            if (PluginManager::instance()->hasPlugin('ProFixS.MultiLanguage')) {
                MultiLanguage::instance()->setLocale($data['lang']);
            }

            if (PluginManager::instance()->hasPlugin('ProFixS.MultiSite')) {
                MultiSite::instance()->reinitSite($data['site_id']);
            }

            $this->send($inbox);
        } catch (Exception $e) {
            trace_log($e);
        }

        $job->delete();
    }

    /**
     * send
     */
    protected function send(Inbox $inbox)
    {
        $settings = MailSetting::instance();

        // fix for multisite/multilange, MailManager load template by code and use singleton
        MailManager::forgetInstance();

        Mail::send(
            // set email template
            $this->getEmailTemplate($inbox),
            // set fields for email template
            ['fields' => $inbox->fields],
            function ($message) use ($inbox, $settings) {
                // set emails
                $message->from($settings->sender_email, $settings->sender_name);
                $message->to($this->getRecipientEmails($inbox));

                // attach files
                foreach ($inbox->files as $file) {
                    $message->attach($file->getLocalPath());
                }
            }
        );
    }

    /**
     * getEmailTemplate
     */
    protected function getEmailTemplate(Inbox $inbox)
    {
        return $inbox->form->template->code;
    }

    /**
     * getRecipientEmails
     */
    protected function getRecipientEmails(Inbox $inbox)
    {
        return array_pluck($inbox->form->emails, 'email');
    }
}

