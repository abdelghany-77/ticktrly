<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification
{
  use Queueable;

  public function __construct(public Ticket $ticket) {}

  /**
   * Store the notification in the database.
   */
  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    return [
      'type' => 'ticket.created',
      'title' => 'New Ticket',
      'body' => '"' . $this->ticket->title . '" by ' . ($this->ticket->user->name ?? 'Unknown') . ' in ' . ($this->ticket->category->name ?? 'None'),
      'url' => '/tickets/' . $this->ticket->id,
      'ticket_id' => $this->ticket->id,
    ];
  }
}
