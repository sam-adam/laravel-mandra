<?php

namespace Mandra\Mail;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;

/**
 * Class Mail
 *
 * @package App\Mail
 */
abstract class Mail extends Mailable
{
    /** @var array */
    protected $logData;

    /** @return string */
    public function getSubject()
    {
        return $this->subject;
    }

    /** @return array */
    public function getTo()
    {
        return $this->to;
    }

    /** @return array */
    public function getCC()
    {
        return $this->cc;
    }

    /** {@inheritDoc} */
    public function setLogData($logData)
    {
        $this->logData = $logData;

        return $this;
    }

    /** {@inheritDoc} */
    protected function buildViewData()
    {
        $data = parent::buildViewData();
        $utms = $this->getCampaignData($data);
        $logs = $this->getLogData($data);

        $data['utms']      = $utms;
        $data['utmString'] = implode('&', $utms);
        $data['logData']   = $logs;

        return $data;
    }

    /**
     * Get data to be logged
     *
     * @param array $data
     *
     * @return array
     */
    protected function getLogData(array $data)
    {
        $logData = isset($data['logData'])
            ? $data['logData']
            : [];

        $logData['mail_type']     = str_replace('/', '.', $this->view);
        $logData['project']       = config('app.name');
        $logData['is_production'] = app()->environment() == 'production' ? 1 : 0;
        $logData['is_loggable']   = true;
        $logData['to']            = $this->to;
        $logData['subject']       = $this->subject;
        $logData['cc']            = $this->cc;
        $logData['reply_to']      = $this->replyTo;

        return $logData;
    }

    /**
     * Get campaign related data
     *
     * @param array $data
     *
     * @return array|mixed
     */
    protected function getCampaignData(array $data)
    {
        $utms = isset($data['utms'])
            ? $data['utms']
            : [];

        $utms['utm_timestamp']  = Carbon::now()->toDateTimeString();
        $utms['utm_source']     = 'email';
        $utms['utm_medium']     = str_replace('/\\', '_', $this->buildView());
        $utms['utm_message_id'] = $this->messageId;

        if ($this->to) {
            $utms['utm_recipients'] = implode(',', array_keys($this->to));
        }

        if ($this->from) {
            $utms['utm_sender'] = implode(',', array_keys($this->from));
        } else {
            $utms['utm_sender'] = config('mail.from.address');
        }

        if ($this->subject) {
            $utms['utm_subject'] = $this->subject;
        }

        foreach ($utms as $key => $value) {
            $value = urlencode(trim($value));

            if (!in_array($value, [false, '', null])) {
                $utms[$key] = "{$key}={$value}";
            }
        }

        return $utms;
    }
}