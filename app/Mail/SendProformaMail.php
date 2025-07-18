<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class SendProformaMail extends Mailable
{
    use Queueable, SerializesModels;
    public $proforma;
    public $pdf;
    public $emailData;

    /**
     * Create a new message instance.
     */
    public function __construct($proforma , array $emailData = [])
    {
        $this->proforma = $proforma;
        $this->emailData = $emailData;
        $this->pdf = PDF::loadView('pdf.proforma', compact('proforma'));    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->emailData['subject'] ?? 'Proforma - Bridge Technologies Solutions';
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
            markdown: 'emails.proforma'
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
                'Proforma.pdf'
            )->withMime('application/pdf'),
        ];    }
}
