<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChangedEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public int $ticketId;
  public string $title;
  public string $oldStatus;
  public string $newStatus;
  public int $userId;
  public int $changerId;

  public function __construct(Ticket $ticket, string $oldStatus, int $changerId)
  {
    $this->ticketId = $ticket->id;
    $this->title = $ticket->title;
    $this->oldStatus = $oldStatus;
    $this->newStatus = $ticket->status;
    $this->userId = $ticket->user_id;
    $this->changerId = $changerId;
  }

  /**
   * Notify ONLY the ticket creator (the User).
   * Do NOT notify the agent/admin who changed the status.
   */
  public function broadcastOn(): array
  {
    // Don't broadcast if the person who changed the status is the ticket creator
    if ($this->userId === $this->changerId) {
      return [];
    }

    return [
      new PrivateChannel('user.' . $this->userId),
    ];
  }

  public function broadcastAs(): string
  {
    return 'ticket.status-changed';
  }
}
