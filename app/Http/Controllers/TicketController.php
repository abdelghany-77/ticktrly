<?php

namespace App\Http\Controllers;

use App\Events\NewTicketCreatedEvent;
use App\Events\TicketAssignedEvent;
use App\Events\TicketStatusChangedEvent;
use App\Models\Category;
use App\Models\KnowledgeBase;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\TicketAssignedNotification;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\TicketStatusChangedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
  public function activity(Request $request)
  {
    $user = Auth::user();

    if ($user->role != 'agent' && $user->role != 'admin') {
      abort(403, 'Only agents and admins can view the activity log.');
    }

    $search = $request->input('search');

    $query = TicketActivity::with(['user', 'ticket'])->latest();

    if ($search) {
      $query->where('ticket_id', $search);
    }

    $activities = $query->paginate(15)->withQueryString();

    return view('tickets.activity', compact('activities', 'search'));
  }

  public function index(Request $request)
  {
    $user = Auth::user();

    $search = $request->input('search');
    $status = $request->input('status');
    $activeTab = 'all';

    $query = Ticket::with(['user', 'agent', 'category']);

    if ($user->role == 'user') {
      $query->where('user_id', $user->id);
    }

    if ($user->role == 'agent') {
      if ($request->input('tab') == 'my-category') {
        $activeTab = 'my-category';
        if ($user->category_id) {
          $query->where('category_id', $user->category_id);
        } else {
          $query->where('id', '<', 0);
        }
      }
    }

    if ($search) {
      $normalizedSearch = ltrim($search, '#');

      $query->where(function ($q) use ($search, $normalizedSearch) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");

        if (is_numeric($normalizedSearch)) {
          $q->orWhere('id', (int) $normalizedSearch);
        }
      });
    }

    if ($status) {
      $query->where('status', $status);
    }

    $query->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END");
    $query->orderBy('created_at', 'desc');

    $tickets = $query->paginate(10)->withQueryString();

    return view('tickets.index', compact('tickets', 'search', 'status', 'activeTab'));
  }

  public function create()
  {
    $user = Auth::user();

    if ($user->role != 'user') {
      abort(403, 'Only users can create tickets.');
    }

    $categories = Category::orderBy('name')->get();

    return view('tickets.create', compact('categories'));
  }

  public function store(Request $request)
  {
    $user = Auth::user();

    if ($user->role != 'user') {
      abort(403, 'Only users can create tickets.');
    }

    $data = $request->validate([
      'title' => 'required|string|max:255',
      'description' => 'required|string',
      'category_id' => 'required|exists:categories,id',
      'file' => 'nullable|file|max:2048',
    ]);

    $data['user_id'] = $user->id;
    $data['status'] = 'open';
    $data['priority'] = 'medium';

    if ($request->hasFile('file')) {
      $data['file_path'] = $request->file('file')->store('tickets', 'public');
    }

    if ($request->boolean('solved_by_ai')) {
      $data['solved_by_ai'] = true;
      $data['status'] = 'resolved';
    }

    $ticket = Ticket::create($data);

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => $user->id,
      'action' => 'created',
      'old_value' => null,
      'new_value' => $ticket->status,
    ]);

    if (!$ticket->solved_by_ai) {
      $ticket->load(['user', 'category']);
      broadcast(new NewTicketCreatedEvent($ticket))->toOthers();

      $recipients = User::where(function ($q) use ($ticket) {
        $q->where('role', 'agent')->where('category_id', $ticket->category_id);
      })->orWhere('role', 'admin')->get();
      Notification::send($recipients, new TicketCreatedNotification($ticket));
    }

    if ($request->wantsJson()) {
      return response()->json([
        'message' => 'Ticket created successfully',
        'ticket' => $ticket
      ]);
    }

    return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket created successfully.');
  }

  public function show(Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role == 'user' && $ticket->user_id != $user->id) {
      abort(403, 'You can only view your own tickets.');
    }

    $ticket->load(['user', 'agent', 'category', 'comments.user']);

    $allowedTransitions = $this->allowedTransitions($ticket->status);

    $agents = [];
    if ($user->role == 'admin') {
      $agents = User::where('role', 'agent')->orderBy('name')->get();
    }

    return view('tickets.show', compact('ticket', 'allowedTransitions', 'agents'));
  }

  public function edit(Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role != 'agent' && $user->role != 'admin') {
      abort(403, 'Only agents and admins can update ticket status.');
    }

    $allowedTransitions = $this->allowedTransitions($ticket->status);

    return view('tickets.edit', compact('ticket', 'allowedTransitions'));
  }

  public function update(Request $request, Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role != 'agent' && $user->role != 'admin') {
      abort(403, 'Only agents and admins can update ticket status.');
    }

    $data = $request->validate([
      'status' => 'required|in:open,in_progress,resolved,closed',
    ]);

    if (!in_array($data['status'], $this->allowedTransitions($ticket->status))) {
      return back()->withErrors(['status' => 'Invalid status transition.']);
    }

    $oldStatus = $ticket->status;
    $ticket->update(['status' => $data['status']]);

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => $user->id,
      'action' => 'status_changed',
      'old_value' => $oldStatus,
      'new_value' => $data['status'],
    ]);

    broadcast(new TicketStatusChangedEvent($ticket, $oldStatus, $user->id))->toOthers();

    if ($ticket->user_id !== $user->id) {
      $ticketCreator = User::find($ticket->user_id);
      if ($ticketCreator) {
        $ticketCreator->notify(new TicketStatusChangedNotification($ticket, $oldStatus));
      }
    }

    return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket status updated successfully.');
  }

  public function updatePriority(Request $request, Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role != 'agent' && $user->role != 'admin') {
      abort(403, 'Only agents and admins can update priority.');
    }

    $data = $request->validate([
      'priority' => 'required|in:low,medium,high',
    ]);

    $oldPriority = $ticket->priority;

    $ticket->update([
      'priority' => $data['priority'],
    ]);

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => $user->id,
      'action' => 'priority_changed',
      'old_value' => $oldPriority,
      'new_value' => $data['priority'],
    ]);

    return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket priority updated successfully.');
  }

  public function assignAgent(Request $request, Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role != 'admin') {
      abort(403, 'Only admins can assign agents.');
    }

    $data = $request->validate([
      'agent_id' => 'nullable|exists:users,id',
    ]);

    if (!empty($data['agent_id'])) {
      $isAgent = User::where('id', $data['agent_id'])->where('role', 'agent')->exists();

      if (!$isAgent) {
        return back()->withErrors(['agent_id' => 'Selected user is not an agent.']);
      }
    }

    $oldAgentId = $ticket->agent_id;

    $ticket->update([
      'agent_id' => $data['agent_id'] ?? null,
    ]);

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => $user->id,
      'action' => 'assigned',
      'old_value' => $oldAgentId,
      'new_value' => $ticket->agent_id,
    ]);

    if ($ticket->agent_id) {
      $ticket->load('agent');
      broadcast(new TicketAssignedEvent($ticket))->toOthers();

      $agent = User::find($ticket->agent_id);
      if ($agent) {
        $agent->notify(new TicketAssignedNotification($ticket));
      }
    }

    return redirect()->route('tickets.show', $ticket)->with('success', 'Ticket assignment updated successfully.');
  }

  public function destroy(Ticket $ticket)
  {
    $user = Auth::user();

    if ($user->role != 'admin') {
      abort(403, 'Only admins can delete tickets.');
    }

    TicketActivity::create([
      'ticket_id' => $ticket->id,
      'user_id' => $user->id,
      'action' => 'deleted',
      'old_value' => null,
      'new_value' => null,
    ]);

    $ticket->delete();

    return redirect()->route('tickets.index')->with('success', 'Ticket deleted successfully.');
  }

  public function aiSearch(Request $request)
  {
    $title = $request->input('title', '');
    $description = $request->input('description', '');
    $categoryId = $request->input('category_id');

    $searchText = $title . ' ' . $description;
    $words = array_filter(array_unique(explode(' ', strtolower($searchText))));

    $stopWords = ['i', 'a', 'an', 'the', 'is', 'it', 'to', 'my', 'me', 'of', 'on', 'in', 'and', 'or', 'for', 'not', 'am', 'are', 'was', 'has', 'have', 'do', 'does', 'can', 'will'];
    $words = array_values(array_diff($words, $stopWords));

    if (empty($words)) {
      return response()->json(['found' => false]);
    }

    $query = KnowledgeBase::query();

    if ($categoryId) {
      $query->where('category_id', $categoryId);
    }

    $articles = $query->get();

    $bestMatch = null;
    $bestScore = 0;

    foreach ($articles as $article) {
      $score = 0;
      $haystack = strtolower($article->question . ' ' . $article->keywords);

      foreach ($words as $word) {
        if (strlen($word) < 3) continue;
        if (str_contains($haystack, $word)) {
          $score++;
        }
      }

      if ($score > $bestScore) {
        $bestScore = $score;
        $bestMatch = $article;
      }
    }

    if ($bestMatch && $bestScore >= 2) {
      return response()->json([
        'found' => true,
        'question' => $bestMatch->question,
        'answer' => nl2br(e($bestMatch->answer)),
        'score' => $bestScore,
      ]);
    }

    return response()->json(['found' => false]);
  }

  private function allowedTransitions($currentStatus)
  {
    if ($currentStatus == 'open') {
      return ['in_progress'];
    } else if ($currentStatus == 'in_progress') {
      return ['resolved'];
    } else if ($currentStatus == 'resolved') {
      return ['closed', 'in_progress'];
    } else if ($currentStatus == 'closed') {
      return ['in_progress'];
    }
    return [];
  }
}
