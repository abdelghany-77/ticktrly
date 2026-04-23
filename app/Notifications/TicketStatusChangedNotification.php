<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketStatusChangedNotification extends Notification
{
  use Queueable;

  public function __construct(
    public Ticket $ticket,
    public string $oldStatus,
  ) {}

  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    $status = str_replace('_', ' ', $this->ticket->status);

    return [
      'type' => 'ticket.status-changed',
      'title' => 'Status Changed',
      'body' => '"' . $this->ticket->title . '" is now ' . $status,
      'url' => '/tickets/' . $this->ticket->id,
      'ticket_id' => $this->ticket->id,
    ];
  }
}
