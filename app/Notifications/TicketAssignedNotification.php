<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
{
  use Queueable;

  public function __construct(public Ticket $ticket) {}

  public function via(object $notifiable): array
  {
    return ['database'];
  }

  public function toArray(object $notifiable): array
  {
    return [
      'type' => 'ticket.assigned',
      'title' => 'Ticket Assigned',
      'body' => 'You were assigned to "' . $this->ticket->title . '"',
      'url' => '/tickets/' . $this->ticket->id,
      'ticket_id' => $this->ticket->id,
    ];
  }
}
