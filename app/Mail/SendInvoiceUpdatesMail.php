<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendInvoiceUpdatesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $old_data;
    public $new_data;

    /**
     * Create a new message instance.
     */
    public function __construct($old_data, $new_data)
    {
        $this->old_data = $old_data; // Pass data to the email view
        $this->new_data = $new_data; // Pass data to the email view
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Send Invoice Updates Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.send_invoiceUpdates_mail',
            with: [
                'old_data' => $this->old_data,
                'new_data' => $this->new_data
            ], // Pass data to the view
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
