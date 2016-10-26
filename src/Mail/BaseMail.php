<?php

namespace Mandra\Mail;

use Carbon\Carbon;
use Illuminate\Mail\Mailable;

/**
 * Class BaseMail
 *
 * @package App\Mail
 */
abstract class BaseMail extends Mailable
{
    /** @var string */
    protected $messageId;

    public function __construct()
    {
        $this->messageId = date('YmdHis').uniqid('_');
    }

    /** @return string */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /** {@inheritDoc} */
    protected function buildViewData()
    {
        $data = parent::buildViewData();
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

        $data['utms']      = $utms;
        $data['utmString'] = implode('&', $utms);

        return $data;
    }
}