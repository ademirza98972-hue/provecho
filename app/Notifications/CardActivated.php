<?php

namespace App\Notifications;

use App\Models\Card;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CardActivated extends Notification
{
    use Queueable;

    public function __construct(
        public Card $card,
        public string $activatedBy = 'public', // 'public' atau 'dashboard'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $source = $this->activatedBy === 'dashboard' ? 'dashboard' : 'pemilik kartu';

        return (new MailMessage)
            ->subject("Card {$this->card->id} Diaktifkan — {$this->card->owner_name}")
            ->greeting('Card Baru Diaktifkan!')
            ->line("**{$this->card->owner_name}** baru saja mengaktifkan card **{$this->card->id}**.")
            ->line("Alamat: " . ($this->card->owner_address ?? '—'))
            ->line("Diaktifkan lewat: {$source}")
            ->action('Lihat di Dashboard', url(route('dashboard.cards.show', $this->card)))
            ->line('Notifikasi otomatis dari Provecho.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'card_id'      => $this->card->id,
            'owner_name'   => $this->card->owner_name,
            'activated_by' => $this->activatedBy,
            'message'      => "Card {$this->card->id} diaktifkan oleh {$this->card->owner_name}",
        ];
    }
}
