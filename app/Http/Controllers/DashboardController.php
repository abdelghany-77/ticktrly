<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $user = Auth::user();

    if ($user->role == 'user') {
      $my_tickets = Ticket::where('user_id', $user->id)->count();
    } else if ($user->role == 'agent') {
      $my_tickets = Ticket::where('agent_id', $user->id)->count();
    } else {
      $my_tickets = Ticket::count();
    }

    if ($user->role == 'user') {
      $open_tickets = Ticket::where('user_id', $user->id)->where('status', 'open')->count();
    } else {
      $open_tickets = Ticket::where('status', 'open')->count();
    }

    if ($user->role == 'user') {
      $resolved_tickets = Ticket::where('user_id', $user->id)->where('status', 'resolved')->count();
    } else {
      $resolved_tickets = Ticket::where('status', 'resolved')->count();
    }

    $stats = [
      'my_tickets' => $my_tickets,
      'open_tickets' => $open_tickets,
      'resolved_tickets' => $resolved_tickets,
    ];

    if ($user->role == 'user') {
      $recentTickets = Ticket::where('user_id', $user->id)
        ->orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    } else {
      $recentTickets = Ticket::orderByRaw("CASE priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 WHEN 'low' THEN 3 ELSE 4 END")
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();
    }


    // 1. Tickets by Category (Bar Chart)
    $categories = Category::withCount('tickets')->orderBy('name')->get();
    $categoryLabels = $categories->pluck('name')->toArray();
    $categoryCounts = $categories->pluck('tickets_count')->toArray();

    // 2. Tickets by Status (Pie Chart)
    $statusData = Ticket::select('status', DB::raw('count(*) as total'))
      ->groupBy('status')
      ->pluck('total', 'status')
      ->toArray();

    $statusLabels = [];
    $statusCounts = [];
    $statusMap = [
      'open' => 'Open',
      'in_progress' => 'In Progress',
      'resolved' => 'Resolved',
      'closed' => 'Closed',
    ];
    foreach ($statusMap as $key => $label) {
      $statusLabels[] = $label;
      $statusCounts[] = $statusData[$key] ?? 0;
    }

    // 3. AI Resolution Rate (Donut Chart)
    $aiResolved = Ticket::where('solved_by_ai', true)->count();
    $normalResolved = Ticket::where('solved_by_ai', false)
      ->whereIn('status', ['resolved', 'closed'])
      ->count();

    $aiLabels = ['AI Resolved', 'Agent Resolved'];
    $aiCounts = [$aiResolved, $normalResolved];

    // 4. Tickets by Priority (Horizontal Bar Chart)
    $priorityData = Ticket::select('priority', DB::raw('count(*) as total'))
      ->groupBy('priority')
      ->pluck('total', 'priority')
      ->toArray();

    $priorityMap = ['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'];
    $priorityLabels = [];
    $priorityCounts = [];
    foreach ($priorityMap as $key => $label) {
      $priorityLabels[] = $label;
      $priorityCounts[] = $priorityData[$key] ?? 0;
    }

    // Encode chart data for JavaScript
    $chartData = [
      'category' => ['labels' => $categoryLabels, 'data' => $categoryCounts],
      'status'   => ['labels' => $statusLabels,   'data' => $statusCounts],
      'ai'       => ['labels' => $aiLabels,        'data' => $aiCounts],
      'priority' => ['labels' => $priorityLabels,  'data' => $priorityCounts],
    ];

    return view('dashboard', compact('stats', 'recentTickets', 'chartData'));
  }
}
