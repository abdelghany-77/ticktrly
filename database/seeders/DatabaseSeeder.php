<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Ticket;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  use WithoutModelEvents;

  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // ── Wipe all existing data (child tables first to avoid FK issues) ──
    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
    DB::table('comments')->truncate();
    DB::table('tickets')->truncate();
    DB::table('users')->truncate();
    DB::table('categories')->truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    // ── 1. Categories ──────────────────────────────────────────
    $categories = [];
    $categoryData = [
      'Hardware' => 'Physical devices, peripherals, and workstation issues.',
      'Software' => 'Application bugs, crashes, and installation problems.',
      'Network'  => 'Connectivity, VPN, DNS, and internet-related issues.',
      'Account'  => 'Login, permissions, and profile-related requests.',
      'Other'    => 'General requests that do not match other categories.',
    ];

    foreach ($categoryData as $name => $description) {
      $categories[$name] = Category::create([
        'name'        => $name,
        'description' => $description,
      ]);
    }

    // ── 2. Admin ───────────────────────────────────────────────
    User::create([
      'name'        => 'System Admin',
      'email'       => 'admin@ticketly.com',
      'password'    => Hash::make('password'),
      'role'        => 'admin',
      'category_id' => null,
    ]);

    // ── 3. Normal User ─────────────────────────────────────────
    User::create([
      'name'        => 'Demo User',
      'email'       => 'user@ticketly.com',
      'password'    => Hash::make('password'),
      'role'        => 'user',
      'category_id' => null,
    ]);

    // ── 4. Agents (2 per category) ─────────────────────────────
    $agents = [
      'Hardware' => [
        ['name' => 'Ahmed Hassan',   'email' => 'ahmed.hassan@ticketly.com'],
        ['name' => 'Mohamed Ali',    'email' => 'mohamed.ali@ticketly.com'],
      ],
      'Software' => [
        ['name' => 'Sara Mahmoud',   'email' => 'sara.mahmoud@ticketly.com'],
        ['name' => 'Fatma Ibrahim',  'email' => 'fatma.ibrahim@ticketly.com'],
      ],
      'Network' => [
        ['name' => 'Khaled Mostafa', 'email' => 'khaled.mostafa@ticketly.com'],
        ['name' => 'Omar Hussein',   'email' => 'omar.hussein@ticketly.com'],
      ],
      'Account' => [
        ['name' => 'Noha Elsayed',   'email' => 'noha.elsayed@ticketly.com'],
        ['name' => 'Amr Khalil',     'email' => 'amr.khalil@ticketly.com'],
      ],
      'Other' => [
        ['name' => 'Dina Adel',      'email' => 'dina.adel@ticketly.com'],
        ['name' => 'Tarek Ismail',   'email' => 'tarek.ismail@ticketly.com'],
      ],
    ];

    foreach ($agents as $categoryName => $agentList) {
      foreach ($agentList as $agentData) {
        User::create([
          'name'        => $agentData['name'],
          'email'       => $agentData['email'],
          'password'    => Hash::make('password'),
          'role'        => 'agent',
          'category_id' => $categories[$categoryName]->id,
        ]);
      }
    }

    // ── 5. Realistic English Tickets (150 tickets) ───────────────────────────
    $faker = Faker::create();
    $statuses = ['open', 'in_progress', 'resolved', 'closed'];
    $priorities = ['low', 'medium', 'high'];
    $allUsers = User::where('role', 'user')->get();
    $allCategories = Category::all();

    $englishTickets = [
      ['title' => 'Cannot access corporate email', 'description' => 'I am getting an invalid password error when trying to access Outlook. Please reset my account.'],
      ['title' => 'VPN connection dropping', 'description' => 'My Cisco AnyConnect VPN keeps disconnecting every 5 minutes. It is impossible to work.'],
      ['title' => 'Need access to shared drive', 'description' => 'Please grant me read/write access to the Marketing shared folder for the new campaign.'],
      ['title' => 'Laptop screen flickering', 'description' => 'My Dell laptop screen starts flickering when connected to the docking station.'],
      ['title' => 'Software installation request', 'description' => 'I need Adobe Photoshop installed on my machine for the upcoming design project.'],
      ['title' => 'Printer out of toner', 'description' => 'The main printer on the 3rd floor (PRN-301) is completely out of black toner.'],
      ['title' => 'Forgot system password', 'description' => 'I forgot my Windows login password after returning from vacation. I need it reset.'],
      ['title' => 'Blue screen of death', 'description' => 'My computer crashed with a BSOD error code 0x0000000A while compiling code.'],
      ['title' => 'Update billing software', 'description' => 'The accounting application needs to be updated to the latest version v4.5 by Friday.'],
      ['title' => 'New employee onboarding', 'description' => 'We have a new hire starting next Monday. They need a laptop, two monitors, and an email account.'],
      ['title' => 'Network is very slow', 'description' => 'The internet speed is crawling today, taking minutes to load a simple internal webpage.'],
      ['title' => 'Mouse not working', 'description' => 'My wireless Bluetooth mouse stopped working. I changed the batteries but it still will not connect.'],
      ['title' => 'Requesting dual monitor setup', 'description' => 'I need a second monitor for my workstation to improve productivity while coding.'],
      ['title' => 'Phishing email reported', 'description' => 'I received a highly suspicious email asking for my bank credentials. Please investigate immediately.'],
      ['title' => 'Zoom audio issues', 'description' => 'People cannot hear me on Zoom video calls. My headset microphone seems to be completely dead.'],
      ['title' => 'Access revoked randomly', 'description' => 'I can no longer log into the CRM system. It says my account is disabled or locked out.'],
      ['title' => 'Keyboard sticky keys', 'description' => 'The spacebar on my mechanical keyboard is stuck and making it extremely difficult to type.'],
      ['title' => 'Guest Wi-Fi not working', 'description' => 'Clients are unable to authenticate to the Guest Wi-Fi portal in conference room B.'],
      ['title' => 'Server downtime notification', 'description' => 'Our main database replica server has been completely unreachable since 2:00 PM.'],
      ['title' => 'Mobile app bug', 'description' => 'The internal company iOS app crashes immediately whenever I try to upload a receipt photo.']
    ];

    for ($i = 0; $i < 150; $i++) {
      $ticketData = $i < count($englishTickets)
        ? $englishTickets[$i]
        : [
          'title' => rtrim($faker->sentence(rand(4, 8)), '.'),
          'description' => $faker->paragraph(rand(2, 4))
        ];

      $category = $allCategories->random();

      $categoryAgents = User::where('role', 'agent')->where('category_id', $category->id)->get();
      $agent = $faker->boolean(70) && $categoryAgents->isNotEmpty() ? $categoryAgents->random() : null;
      $status = $faker->randomElement($statuses);

      // If it's a new ticket (open), usually there's no agent
      if ($status === 'open' && $faker->boolean(50)) {
        $agent = null;
      }
      // If it has an agent and was open, maybe it's in_progress
      if ($agent && $status === 'open' && $faker->boolean(50)) {
        $status = 'in_progress';
      }

      Ticket::create([
        'title'       => $ticketData['title'],
        'description' => $ticketData['description'],
        'status'      => $status,
        'priority'    => $faker->randomElement($priorities),
        'category_id' => $category->id,
        'user_id'     => $allUsers->random()->id,
        'agent_id'    => $agent ? $agent->id : null,
      ]);
    }

    // ── 6. Knowledge Base ──────────────────────────────────────
    DB::table('knowledge_bases')->truncate();
    $this->call(KnowledgeBaseSeeder::class);
  }
}
