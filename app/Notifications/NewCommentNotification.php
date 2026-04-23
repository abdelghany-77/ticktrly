<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification
{
  use Queueable;

  public function __construct(
    public Ticket $ticket,
    public Comment $comment,
  ) {}

  public function via(object $notifiable): array
  {
    return ['database', 'broadcast'];
  }

  public function broadcastType(): string
  {
    return 'comment.added';
  }

  public function toArray(object $notifiable): array
  {
    return [
      'type' => 'comment.added',
      'title' => 'New Comment',
      'body' => ($this->comment->user->name ?? 'Unknown') . ' commented on "' . $this->ticket->title . '"',
      'url' => '/tickets/' . $this->ticket->id,
      'ticket_id' => $this->ticket->id,
    ];
  }
}
