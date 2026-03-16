<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    protected string $status; // pending, received, sent, approved, rejected, signed

    /**
     * Create a new message instance.
     * @param array $data
     * @param string $status
     */
    public function __construct(array $data, string $status)
    {
        $this->data = $data;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subjects = [
            'pending' => 'Oferta Pendiente',
            'received' => 'Oferta Recibida',
            'sent' => 'Oferta Enviada',
            'approved' => 'Oferta Aprobada',
            'rejected' => 'Oferta Rechazada',
            'signed' => 'Oferta Firmada',
        ];

        return new Envelope(
            subject: $subjects[$this->status] ?? 'Actualización de Oferta',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.offerStatus',
            with: [
                'status' => $this->status
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        $disk = $this->data['attachments_disk'] ?? config('filesystems.default');

        foreach ($this->data['attachments'] ?? [] as $filePath) {
            $attachments[] = Attachment::fromStorageDisk($disk, $filePath);
        }

        return $attachments;
    }
}
