<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\KnowledgeBase;
use Illuminate\Database\Seeder;

class KnowledgeBaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $categories = Category::pluck('id', 'name');

    $data = [
      'Hardware' => [
        [
          'question' => 'My laptop screen is flickering',
          'answer' => "Try these steps to fix screen flickering:\n\n1. Update your display drivers — go to Device Manager → Display adapters → right-click → Update driver.\n2. Disconnect and reconnect the docking station cable.\n3. Try a different video cable (HDMI / DisplayPort).\n4. If using an external monitor, test the laptop without it.\n5. If the issue persists, contact IT to schedule a hardware inspection.",
          'keywords' => 'screen,flicker,display,monitor,dock,laptop',
        ],
        [
          'question' => 'My mouse or keyboard is not working',
          'answer' => "Quick fixes for mouse/keyboard issues:\n\n1. Replace the batteries (wireless devices).\n2. Unplug and re-plug the USB receiver into a different port.\n3. Try pairing via Bluetooth again — remove the device and re-add it.\n4. Restart your computer.\n5. Test with a wired mouse/keyboard to rule out a hardware defect.\n6. If nothing works, submit a ticket and IT will provide a replacement.",
          'keywords' => 'mouse,keyboard,bluetooth,wireless,usb,not working,stuck,sticky',
        ],
        [
          'question' => 'How do I request a second monitor?',
          'answer' => "To request a dual monitor setup:\n\n1. Submit a ticket under the Hardware category with the subject 'Dual Monitor Request'.\n2. Include your desk/office location.\n3. IT will check availability and schedule the installation within 3-5 business days.\n4. Make sure you have a compatible docking station or video port.",
          'keywords' => 'monitor,dual,second,screen,setup,request',
        ],
        [
          'question' => 'Printer is not printing or out of toner',
          'answer' => "Troubleshoot printer issues:\n\n1. Check the printer display panel for error messages (paper jam, toner low, etc.).\n2. Make sure the printer is powered on and connected to the network.\n3. Try restarting the print spooler: open Services → Print Spooler → Restart.\n4. For toner replacement, note the printer model and floor number and submit a ticket.\n5. IT keeps spare toner cartridges and will replace within 24 hours.",
          'keywords' => 'printer,print,toner,paper,jam,not printing',
        ],
        [
          'question' => 'My computer shows a Blue Screen of Death (BSOD)',
          'answer' => "If you see a Blue Screen (BSOD):\n\n1. Note the error code displayed on the screen (e.g., 0x0000000A).\n2. Restart your computer — it may recover automatically.\n3. If BSOD repeats, boot in Safe Mode (hold Shift while restarting).\n4. Run Windows Memory Diagnostic to check for RAM issues.\n5. If the problem continues, submit a ticket with the error code and IT will inspect the hardware.",
          'keywords' => 'bsod,blue screen,crash,error,freeze,restart',
        ],
      ],

      'Software' => [
        [
          'question' => 'How do I request software installation?',
          'answer' => "To request new software:\n\n1. Submit a ticket under the Software category.\n2. Include the exact software name and version you need.\n3. Provide a business justification (project name or approval from your manager).\n4. IT will verify licensing and install within 2-3 business days.\n5. Some software requires admin approval — IT will notify you if there is a delay.",
          'keywords' => 'install,software,application,setup,request,download',
        ],
        [
          'question' => 'An application keeps crashing or freezing',
          'answer' => "Try these steps to fix app crashes:\n\n1. Close the application completely and reopen it.\n2. Clear the application cache (Settings → Clear Cache if available).\n3. Check for updates — install the latest version.\n4. Restart your computer.\n5. If the problem persists, submit a ticket with the app name, version, and error message screenshot.",
          'keywords' => 'crash,freeze,hang,not responding,application,app,bug',
        ],
        [
          'question' => 'How do I update my software?',
          'answer' => "To update software:\n\n1. Most apps auto-update — check Help → About for the current version.\n2. For managed software, IT pushes updates automatically.\n3. If you need a specific version urgently, submit a ticket with the software name and the version you need.\n4. Do not download software from unofficial sources — always go through IT.",
          'keywords' => 'update,upgrade,version,latest,software,patch',
        ],
        [
          'question' => 'Zoom or Teams audio/video is not working',
          'answer' => "Fix meeting audio/video problems:\n\n1. Check your mic/camera permissions — go to Settings → Privacy → Microphone/Camera.\n2. In Zoom/Teams, go to Settings → Audio/Video and select the correct device.\n3. Test your mic at https://www.onlinemictest.com\n4. Update Zoom/Teams to the latest version.\n5. Try unplugging and reconnecting your headset.\n6. Restart the app and rejoin the meeting.",
          'keywords' => 'zoom,teams,audio,video,microphone,camera,call,meeting,headset',
        ],
        [
          'question' => 'Mobile app is crashing on my phone',
          'answer' => "If the company mobile app is crashing:\n\n1. Force close the app and reopen it.\n2. Clear the app cache: Settings → Apps → [App Name] → Clear Cache.\n3. Check for app updates in the App Store / Google Play.\n4. Ensure your phone OS is up to date.\n5. Uninstall and reinstall the app as a last resort.\n6. If nothing helps, submit a ticket with your phone model and OS version.",
          'keywords' => 'mobile,app,phone,crash,ios,android,upload,receipt',
        ],
      ],

      'Network' => [
        [
          'question' => 'My internet is very slow',
          'answer' => "Steps to improve internet speed:\n\n1. Test your speed at https://fast.com to confirm the issue.\n2. Restart your router/modem (unplug for 30 seconds).\n3. Disconnect other devices that may be using bandwidth.\n4. Switch from Wi-Fi to a wired Ethernet connection if possible.\n5. If the issue affects multiple users, it may be a network-wide problem — submit a ticket.",
          'keywords' => 'slow,internet,speed,bandwidth,wifi,connection,crawling',
        ],
        [
          'question' => 'VPN is not connecting or keeps disconnecting',
          'answer' => "Fix VPN connection issues:\n\n1. Ensure you have an active internet connection first.\n2. Restart the VPN client (Cisco AnyConnect, etc.).\n3. Try connecting to a different VPN server/gateway.\n4. Check with IT if your VPN account is active.\n5. Disable any firewall or antivirus temporarily to test.\n6. If the issue continues, uninstall and reinstall the VPN client.",
          'keywords' => 'vpn,disconnect,connection,cisco,anyconnect,remote,dropping',
        ],
        [
          'question' => 'Guest Wi-Fi is not working',
          'answer' => "Troubleshoot Guest Wi-Fi:\n\n1. Ensure you are connecting to the correct SSID (Guest network).\n2. Open a browser — the authentication portal should appear automatically.\n3. Try using an incognito/private browser window.\n4. Clear your browser cache and try again.\n5. If the portal does not load, the access point may need a restart — contact IT.",
          'keywords' => 'wifi,guest,wireless,portal,authenticate,conference',
        ],
        [
          'question' => 'Cannot access a specific website or service',
          'answer' => "If a website or service is blocked/unreachable:\n\n1. Check if the site works on your phone (mobile data) to rule out a company firewall block.\n2. Try clearing your DNS cache: open CMD and run 'ipconfig /flushdns'.\n3. Try a different browser.\n4. If the site is blocked by company policy and you need it for work, submit a ticket requesting access with a business justification.",
          'keywords' => 'website,blocked,access,dns,firewall,unreachable,server',
        ],
        [
          'question' => 'Network drive or shared folder is not accessible',
          'answer' => "Fix shared drive access issues:\n\n1. Make sure you are connected to the company network (or VPN if remote).\n2. Try accessing the drive using the direct path: \\\\server\\share.\n3. Restart your computer to refresh network connections.\n4. If you get 'Access Denied', you may need permissions — submit a ticket with the drive path and your manager's approval.",
          'keywords' => 'drive,shared,folder,network,access,permission,map',
        ],
      ],

      'Account' => [
        [
          'question' => 'I forgot my password',
          'answer' => "To reset your password:\n\n1. Go to the login page and click 'Forgot Password'.\n2. Enter your email address — a reset link will be sent.\n3. Check your spam/junk folder if you do not see the email.\n4. If you are locked out of your Windows account, contact IT directly for a manual reset.\n5. After resetting, update the password on all your devices (phone, tablet, etc.).",
          'keywords' => 'password,forgot,reset,login,locked,cannot login',
        ],
        [
          'question' => 'My account is locked or disabled',
          'answer' => "If your account is locked:\n\n1. This usually happens after multiple failed login attempts.\n2. Wait 15-30 minutes — the lockout may clear automatically.\n3. If it does not, contact IT to manually unlock your account.\n4. If your account shows 'disabled', it may have been deactivated — submit a ticket for reactivation.\n5. Always use a strong password to avoid repeated lockouts.",
          'keywords' => 'locked,disabled,account,blocked,revoked,access,crm,login',
        ],
        [
          'question' => 'How do I request access to a system or tool?',
          'answer' => "To request system access:\n\n1. Submit a ticket under the Account category.\n2. Specify the system name (CRM, ERP, shared drive, etc.).\n3. Include the level of access needed (read only, read/write, admin).\n4. Attach manager's approval if required.\n5. IT will process your request within 1-2 business days after verification.",
          'keywords' => 'access,permission,request,system,tool,crm,erp,grant',
        ],
        [
          'question' => 'How do I set up email on a new device?',
          'answer' => "To set up corporate email:\n\n1. Open the Mail app on your device.\n2. Select 'Add Account' → choose 'Exchange' or 'Office 365'.\n3. Enter your company email address and password.\n4. The server settings should auto-configure.\n5. If you get an 'invalid password' error, try resetting your password first.\n6. Contact IT if two-factor authentication is blocking the setup.",
          'keywords' => 'email,outlook,setup,phone,device,exchange,configure,corporate',
        ],
        [
          'question' => 'New employee needs accounts and equipment',
          'answer' => "For new employee onboarding:\n\n1. Submit a ticket at least 5 business days before the start date.\n2. Include: employee name, department, role, and start date.\n3. Specify what is needed: email account, laptop, monitors, software, building access.\n4. IT will prepare everything and coordinate with HR.\n5. On the first day, the new hire can pick up equipment from IT support desk.",
          'keywords' => 'new,employee,onboarding,hire,account,laptop,equipment,setup',
        ],
        [
          'question' => 'I received a phishing or suspicious email',
          'answer' => "If you received a suspicious email:\n\n1. Do NOT click any links or download attachments.\n2. Do NOT reply to the email or provide any personal information.\n3. Report it: forward the email to security@company.com.\n4. Mark it as phishing in Outlook (right-click → Report → Phishing).\n5. Submit a ticket so IT can investigate and block the sender.\n6. If you already clicked a link, change your password immediately.",
          'keywords' => 'phishing,suspicious,email,scam,hack,security,spam,credentials',
        ],
      ],

      'Other' => [
        [
          'question' => 'How do I submit a support ticket?',
          'answer' => "To submit a support ticket:\n\n1. Log in to the Ticketly portal.\n2. Click 'New Ticket' in the sidebar.\n3. Fill in the title, description, and select a category.\n4. Attach any relevant files or screenshots.\n5. Click 'Save Ticket' — you will receive a confirmation.\n6. You can track your ticket status from the Tickets page.",
          'keywords' => 'ticket,submit,create,how,help,support,request',
        ],
        [
          'question' => 'What are the IT support working hours?',
          'answer' => "IT Support is available:\n\n• Sunday to Thursday: 8:00 AM – 5:00 PM\n• Friday and Saturday: Closed (emergency support only)\n• Emergency hotline: ext. 5555\n\nFor urgent issues outside working hours, call the emergency line. All tickets submitted after hours will be addressed the next business day.",
          'keywords' => 'hours,working,support,time,available,emergency,contact',
        ],
        [
          'question' => 'How long does it take to resolve a ticket?',
          'answer' => "Ticket resolution times depend on priority:\n\n• High priority: 4-8 hours\n• Medium priority: 1-2 business days\n• Low priority: 3-5 business days\n\nComplex issues may take longer. You will receive status updates as your ticket progresses. You can check your ticket status at any time from the Tickets page.",
          'keywords' => 'time,resolve,how long,wait,response,sla,priority',
        ],
        [
          'question' => 'Can I track the status of my ticket?',
          'answer' => "Yes! To track your ticket:\n\n1. Go to the Tickets page from the sidebar.\n2. You will see all your submitted tickets with their current status.\n3. Statuses: Open → In Progress → Resolved → Closed.\n4. Click on any ticket to see full details and comments.\n5. You will also receive notifications when your ticket status changes.",
          'keywords' => 'track,status,check,follow,progress,update',
        ],
        [
          'question' => 'How do I contact IT support directly?',
          'answer' => "You can reach IT support through:\n\n1. Ticketly Portal — submit a ticket (recommended for tracking).\n2. Email: support@company.com\n3. Phone: ext. 5555 (for emergencies)\n4. Walk-in: IT Help Desk, Building A, Ground Floor\n\nFor the fastest resolution, always include a clear description and any relevant screenshots.",
          'keywords' => 'contact,support,phone,email,help,desk,reach',
        ],
      ],
    ];

    foreach ($data as $categoryName => $articles) {
      $categoryId = $categories[$categoryName] ?? null;
      if (!$categoryId) continue;

      foreach ($articles as $article) {
        KnowledgeBase::create([
          'category_id' => $categoryId,
          'question' => $article['question'],
          'answer' => $article['answer'],
          'keywords' => $article['keywords'],
        ]);
      }
    }
  }
}
