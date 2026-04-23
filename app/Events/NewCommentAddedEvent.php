<?php

namespace App\Events;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCommentAddedEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public int $ticketId;
  public string $ticketTitle;
  public string $commentText;
  public string $commenterName;
  public int $userId;
  public int $commenterId;

  public function __construct(Ticket $ticket, Comment $comment)
  {
    $this->ticketId = $ticket->id;
    $this->ticketTitle = $ticket->title;
    $this->commentText = $comment->comment;
    $this->commenterName = $comment->user->name ?? 'Unknown';
    $this->userId = $ticket->user_id;
    $this->commenterId = $comment->user_id;
  }

  /**
   * Notify ONLY the ticket creator (the User).
   * Do NOT notify agents or admins.
   * If the commenter IS the ticket creator, send nothing.
   */
  public function broadcastOn(): array
  {
    // Don't notify if the commenter is the ticket creator
    if ($this->userId === $this->commenterId) {
      return [];
    }

    return [
      new PrivateChannel('user.' . $this->userId),
    ];
  }

  public function broadcastAs(): string
  {
    return 'comment.added';
  }
}
