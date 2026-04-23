# Ticktly - Ticket Management System

## 📖 Project Description

Ticktly is a streamlined, role-based Support Ticket Management System designed to handle customer inquiries efficiently. It connects regular Users requiring support with specialized Agents capable of resolving specific domain issues, all overseen by System Administrators. It features a clean, responsive UI and a structured database to make ticket tracking simple and accountable.

## ✨ Main Features

- **Role-Based Access Control (RBAC):** Distinct specialized dashboards, views, and permissions for Users, Agents, and Admins.
- **Agent Specialization (Categories):** Support Agents are assigned to specific categories (e.g., Software, Hardware, Network). This ensures that tickets are routed accurately to the experts most qualified to solve them.
- **Ticket Lifecycle Management:** Create, view, update, and track tickets through open, in-progress, resolved, and closed statuses.
- **Interactive Commenting:** Direct communication layer on each ticket between Users and Agents/Admins.
- **Priority Flags:** Visual indicators for High, Medium, and Low priority tickets to ensure SLAs and urgency are respected.
- **Modern UI:** A clean, mobile-responsive dark-theme UI designed primarily with Tailwind CSS for clarity and fast navigation.

## 🛠 Tech Stack

- **Backend:** Laravel 11 (PHP)
- **Frontend:** Blade Templates, Tailwind CSS, Vite
- **Database:** Configured for MySQL/SQLite (via Laravel Eloquent ORM)
- **Authentication:** Laravel standard session-based Auth

## 🔐 Default Login Credentials

You can test the application using the following seeded accounts (Run php artisan db:seed first):

| Role                         | Email              | Password | Allowed Actions                                                       |
| :--------------------------- | :----------------- | :------- | :-------------------------------------------------------------------- |
| **System Admin**             | dmin@ticketly.com  | password | Full system access, can view and manage all tickets.                  |
| **Support Agent (Software)** | gent@ticketly.com  | password | Can manage tickets, automatically filters by their assigned category. |
| **Support Agent (Network)**  | gent2@ticketly.com | password | Manages network-related tickets.                                      |
| **Demo User**                | user@ticketly.com  | password | Can create tickets, view own tickets, and reply to agent comments.    |

## 🏷 Understanding Categories & Agent Specialization

In Ticktly, **Categories** act as the primary routing mechanism. When a User creates a new ticket, they must select a category (e.g., Hardware, Software, Network, Account).

**Agents are inherently tied to specific Categories.** For example, an Agent assigned to the "Software" category will see those tickets prioritized in their specific "My Category Tickets" tab. This avoids agent collision and ensures domain experts are handling the right problems efficiently without being overwhelmed by tickets outside their scope.

---

## 🎤 Interview Walkthrough: How the System Works

_If I were demonstrating this project to you in an interview, here is how I would explain the core workflows of the application:_

### 1. The User Workflow (Ticket Creation & Tracking)

"Let's start from the perspective of the **User**. When a User logs into Ticktly, their primary goal is getting help. They land on a clean dashboard showing their recent tickets.

If they have an issue—say, their 'Profile image upload fails'—they click **'Create Ticket'**. They fill out the title, description, priority, and critically, the **Category** (e.g., Software).

Once submitted, the ticket enters the Open status. The User can click into this ticket to view updates or add additional context via the comment section if they forgot a detail."

### 2. The Agent Workflow (Triage & Resolution)

"Now, let's switch hats to the **Support Agent**. Agents are the heart of the system. Remember how we linked Agents to Categories? If I log in as the 'Software' Agent, my dashboard dynamically caters to me.

I have a tab specifically for **'My Category Tickets'**. I immediately see the User's open ticket about the 'Profile image upload failure' because they tagged it as Software.

I click into the ticket, evaluate the problem, and change the status to In Progress. I can drop a comment: _'Looking into the mime-type validation now.'_

Once I deploy the fix, I change the ticket status to Resolved and leave a closing comment. Through this whole flow, the User gets real-time updates on their end."

### 3. The Admin Workflow (Oversight & Management)

"Finally, we have the **System Admin**. The Admin has a bird's-eye view of the entire operational pipeline. When they log in, they aren't restricted by Categories constraints. They see _all_ tickets across the platform.

If a ticket has been sitting in Open for too long, or if an Agent needs escalating approval (like changing an account role), the Admin can step in, comment on the ticket, or reassign priorities. They hold the ultimate authority to keep the queue moving efficiently."

### Summary of the Architecture

"Under the hood, I built this using Laravel's powerful Eloquent ORM. The relationships are highly normalized: a Ticket belongs to a User, a Category, and an Agent. It also hasMany Comments. By offloading the heavy business logic to the models and utilizing simple, clean Blade components for the frontend, the codebase remains highly maintainable, scalable, and easy for new engineers to understand. The entire UI is built with Tailwind CSS to ensure it's fully responsive without writing large custom CSS files."
