<?php

namespace App\Mail;

use App\Models\RefundRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RefundRequestNotification extends Mailable
{
    use Queueable, SerializesModels;

    public RefundRequest $refundRequest;

    public bool $forPassenger;

    public function __construct(RefundRequest $refundRequest, bool $forPassenger = false)
    {
        $this->refundRequest = $refundRequest;
        $this->forPassenger = $forPassenger;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->forPassenger
                ? 'Refund Request Received - Bhutan Taxi'
                : 'New Refund Request - Bhutan Taxi',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: $this->forPassenger
                ? 'emails.refund-request-confirmation'
                : 'emails.refund-request-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}