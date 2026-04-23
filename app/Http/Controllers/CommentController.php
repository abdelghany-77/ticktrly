<?php

namespace App\Http\Controllers;

use App\Events\NewCommentAddedEvent;
use App\Events\TicketAssignedEvent;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\NewCommentNotification;
use App\Notifications\TicketAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
  public function store(Request $request, Ticket $ticket)
  {
    /** @var \App\Models\User $user */
    $user = Auth::user();

    if ($user->role == 'user' && $ticket->user_id != $user->id) {
      abort(403, 'You can only comment on your own tickets.');
    }

    if ($user->role == 'user') {
      if ($ticket->status == 'resolved' || $ticket->status == 'closed') {
        return redirect()->route('tickets.show', $ticket)->with('error', 'You cannot comment on a resolved or closed ticket.');
      }
    }

    $data = $request->validate([
      'comment' => 'required|string|min:2|max:2000',
    ]);

    $comment = $ticket->comments()->create([
      'user_id' => Auth::id(),
      'comment' => $data['comment'],
    ]);

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => Auth::id(),
      'action' => 'commented',
      'old_value' => null,
      'new_value' => $data['comment'],
    ]);

    if ($ticket->status == 'open') {
      $ticket->update(['status' => 'in_progress']);
    }

    if ($user->role == 'agent' && is_null($ticket->agent_id)) {
      $ticket->update(['agent_id' => $user->id]);
      $ticket->load('agent');
      broadcast(new TicketAssignedEvent($ticket))->toOthers();

      $user->notify(new TicketAssignedNotification($ticket));
    }

    $comment->load('user');
    broadcast(new NewCommentAddedEvent($ticket, $comment))->toOthers();

    if ($ticket->user_id !== $user->id) {
      $ticketCreator = User::find($ticket->user_id);
      if ($ticketCreator) {
        $ticketCreator->notify(new NewCommentNotification($ticket, $comment));
      }
    }


    if ($ticket->agent_id && $ticket->agent_id !== $user->id) {
      $agent = User::find($ticket->agent_id);
      if ($agent) {
        $agent->notify(new NewCommentNotification($ticket, $comment));
      }
    }

    return redirect()->route('tickets.show', $ticket)->with('success', 'Comment added successfully.');
  }
}