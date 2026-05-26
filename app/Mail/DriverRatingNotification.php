<?php

namespace App\Mail;

use App\Models\Rating;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DriverRatingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $rating;
    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Rating $rating, Booking $booking)
    {
        $this->rating = $rating;
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $stars = '⭐ ' . str_repeat('★', $this->rating->rating) . str_repeat('☆', 5 - $this->rating->rating);
        
        return new Envelope(
            subject: "New Rating Received - $stars - Bhutan Taxi",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.driver-rating-notification',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
