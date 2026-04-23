<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTicketCreatedEvent implements ShouldBroadcastNow
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public int $ticketId;
  public string $title;
  public string $status;
  public string $priority;
  public string $userName;
  public string $categoryName;
  public int $categoryId;
  public int $creatorId;

  public function __construct(Ticket $ticket)
  {
    $this->ticketId = $ticket->id;
    $this->title = $ticket->title;
    $this->status = $ticket->status;
    $this->priority = $ticket->priority;
    $this->userName = $ticket->user->name ?? 'Unknown';
    $this->categoryName = $ticket->category->name ?? 'None';
    $this->categoryId = $ticket->category_id;
    $this->creatorId = $ticket->user_id;
  }

  /**
   * Broadcast on:
   * 1. The category channel — so agents in that category get notified.
   * 2. The admins channel — so ALL admins get notified.
   */
  public function broadcastOn(): array
  {
    return [
      new PrivateChannel('category.' . $this->categoryId),
      new PrivateChannel('admins'),
    ];
  }

  public function broadcastAs(): string
  {
    return 'ticket.created';
  }
}