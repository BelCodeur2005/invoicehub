<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SendInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdf;
    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($invoice,array $emailData = [])
    {
        $this->invoice = $invoice;
        $this->emailData = $emailData;
        $this->pdf = PDF::loadView('pdf.invoice', compact('invoice'));
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->emailData['subject'] ?? 'Facture- Bridge Technologies Solutions';
        return new Envelope(
            subject: $subject,
            replyTo: 'merline.merokenne@bridgetech-solutions.com'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice'
            // ou view: 'emails.invoice' si ce n’est pas du Markdown
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            \Illuminate\Mail\Mailables\Attachment::fromData(
                fn () => $this->pdf->output(),
                'Facture.pdf'
            )->withMime('application/pdf'),
        ];
    }
}
