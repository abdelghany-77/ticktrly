<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketAssignedEvent implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public int $ticketId;
  public string $title;
  public string $agentName;
  public int $agentId;

  public function __construct(Ticket $ticket)
  {
    $this->ticketId = $ticket->id;
    $this->title = $ticket->title;
    $this->agentName = $ticket->agent->name ?? 'Unknown';
    $this->agentId = $ticket->agent_id;
  }

  /**
   * Notify the newly assigned agent.
   */
  public function broadcastOn(): array
  {
    return [
      new PrivateChannel('agent.' . $this->agentId),
    ];
  }

  public function broadcastAs(): string
  {
    return 'ticket.assigned';
  }
}
