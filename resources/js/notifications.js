document.addEventListener("DOMContentLoaded", () => {
  const userId = document.querySelector('meta[name="user-id"]')?.content;
  const userRole = document.querySelector('meta[name="user-role"]')?.content;
  const userCategoryId = document.querySelector(
    'meta[name="user-category-id"]',
  )?.content;

  if (!userId) return;

  if (!window.Echo) return;

  const notifications = [];

  fetchStoredNotifications();

  if (userRole === "user") {
    bindBroadcastNotifications(`user.${userId}`);
  }

  if (userRole === "agent") {
    bindBroadcastNotifications(`agent.${userId}`);
  }

  if (userRole === "user") {
    window.Echo.private(`user.${userId}`)
      .listen(".comment.added", (e) => {
        pushNotification({
          type: "comment.added",
          title: "New Comment",
          body: `${e.commenterName} commented on "${e.ticketTitle}"`,
          url: `/tickets/${e.ticketId}`,
          ticketId: e.ticketId,
        });
      })
      .listen(".ticket.status-changed", (e) => {
        const status = e.newStatus.replace("_", " ");
        pushNotification({
          type: "ticket.status-changed",
          title: "Status Changed",
          body: `"${e.title}" is now ${status}`,
          url: `/tickets/${e.ticketId}`,
          ticketId: e.ticketId,
        });
      });
  }

  if (userRole === "agent") {
    window.Echo.private(`agent.${userId}`).listen(".ticket.assigned", (e) => {
      pushNotification({
        type: "ticket.assigned",
        title: "Ticket Assigned",
        body: `You were assigned to "${e.title}"`,
        url: `/tickets/${e.ticketId}`,
        ticketId: e.ticketId,
      });
    });

    if (userCategoryId) {
      window.Echo.private(`category.${userCategoryId}`).listen(
        ".ticket.created",
        (e) => {
          pushNotification({
            type: "ticket.created",
            title: "New Ticket",
            body: `"${e.title}" by ${e.userName} in ${e.categoryName}`,
            url: `/tickets/${e.ticketId}`,
            ticketId: e.ticketId,
          });

          prependTicketRow(e);
        },
      );
    }
  }

  if (userRole === "admin") {
    window.Echo.private(`agent.${userId}`).listen(".ticket.assigned", (e) => {
      pushNotification({
        type: "ticket.assigned",
        title: "Ticket Assigned",
        body: `You were assigned to "${e.title}"`,
        url: `/tickets/${e.ticketId}`,
        ticketId: e.ticketId,
      });
    });

    window.Echo.private("admins").listen(".ticket.created", (e) => {
      pushNotification({
        type: "ticket.created",
        title: "New Ticket",
        body: `"${e.title}" by ${e.userName} in ${e.categoryName}`,
        url: `/tickets/${e.ticketId}`,
        ticketId: e.ticketId,
      });

      prependTicketRow(e);
    });
  }

  function bindBroadcastNotifications(channelName) {
    window.Echo.private(channelName).notification((notification) => {
      pushNotification({
        type: notification.type || "notification",
        title: notification.title,
        body: notification.body,
        url: notification.url,
        ticketId: notification.ticket_id,
        id: notification.id,
      });
    });
  }

  function fetchStoredNotifications() {
    fetch("/notifications", {
      headers: {
        Accept: "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.notifications && data.notifications.length > 0) {
          data.notifications.forEach((n) => {
            const fingerprint = buildNotificationKey({
              type: n.type,
              title: n.title,
              body: n.body,
              url: n.url,
              ticketId: n.ticket_id,
            });

            if (notifications.some((existing) => existing.fingerprint === fingerprint)) {
              return;
            }

            notifications.push({
              id: n.id,
              type: n.type,
              title: n.title,
              body: n.body,
              url: n.url,
              ticketId: n.ticket_id,
              time: new Date(n.time),
              read: n.read,
              fromDb: true,
              fingerprint,
            });
          });

          updateBadge();
          renderDropdown();
        }
      })
      .catch((err) => {
        console.error("Failed to fetch notifications:", err);
      });
  }

  function pushNotification({ title, body, url, type = "notification", ticketId = null, id = null }) {
    const fingerprint = buildNotificationKey({
      type,
      title,
      body,
      url,
      ticketId: ticketId ?? id,
    });

    if (notifications.some((n) => n.fingerprint === fingerprint)) {
      return;
    }

    const notification = {
      id,
      type,
      title,
      body,
      url,
      ticketId,
      time: new Date(),
      read: false,
      fromDb: false,
      fingerprint,
    };
    notifications.unshift(notification);

    if (notifications.length > 30) notifications.pop();

    updateBadge();
    showToast(title, body, url);
    renderDropdown();
  }

  function buildNotificationKey({ type, title, body, url, ticketId }) {
    return [type || "", title || "", body || "", url || "", ticketId || ""].join("|");
  }

  function updateBadge() {
    const badge = document.getElementById("notification-badge");
    if (!badge) return;

    const unreadCount = notifications.filter((n) => !n.read).length;
    badge.textContent = unreadCount;
    badge.classList.toggle("hidden", unreadCount === 0);
  }

  function showToast(title, body, url) {
    const container = document.getElementById("toast-container");
    if (!container) return;

    const toast = document.createElement("div");
    toast.className = "toast-notification";
    toast.innerHTML = `
            <div class="toast-header">
                <svg class="toast-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="toast-title">${title}</span>
                <button class="toast-close" onclick="this.closest('.toast-notification').remove()">✕</button>
            </div>
            <p class="toast-body">${body}</p>
        `;

    if (url) {
      toast.style.cursor = "pointer";
      toast.addEventListener("click", (e) => {
        if (!e.target.classList.contains("toast-close")) {
          window.location.href = url;
        }
      });
    }

    container.appendChild(toast);

    setTimeout(() => {
      toast.classList.add("toast-exit");
      setTimeout(() => toast.remove(), 300);
    }, 5000);
  }

  function renderDropdown() {
    const list = document.getElementById("notification-list");
    if (!list) return;

    if (notifications.length === 0) {
      list.innerHTML =
        '<div class="notification-empty">No notifications yet</div>';
      return;
    }

    list.innerHTML = notifications
      .map(
        (n) => `
            <a href="${n.url || "#"}" class="notification-item ${!n.read ? "notification-unread" : ""}" ${n.id ? `data-notification-id="${n.id}"` : ""}>
                <div class="notification-item-title">${n.title}</div>
                <div class="notification-item-body">${n.body}</div>
                <div class="notification-item-time">${timeAgo(n.time)}</div>
            </a>
        `,
      )
      .join("");

    list
      .querySelectorAll(".notification-item[data-notification-id]")
      .forEach((item) => {
        item.addEventListener("click", () => {
          const notifId = item.dataset.notificationId;
          if (notifId) {
            markNotificationAsRead(notifId);
          }
        });
      });
  }

  function markNotificationAsRead(id) {
    const csrfToken = document.querySelector(
      'meta[name="csrf-token"]',
    )?.content;

    fetch(`/notifications/${id}/mark-read`, {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken || "",
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then(() => {
        const notif = notifications.find((n) => n.id === id);
        if (notif) {
          notif.read = true;
        }
        updateBadge();
        renderDropdown();
      })
      .catch((err) => {
        console.error("Failed to mark notification as read:", err);
      });
  }

  function markAllNotificationsAsRead() {
    const csrfToken = document.querySelector(
      'meta[name="csrf-token"]',
    )?.content;

    fetch("/notifications/mark-all-read", {
      method: "POST",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken || "",
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then(() => {
        notifications.forEach((n) => (n.read = true));
        updateBadge();
        renderDropdown();
      })
      .catch((err) => {
        console.error("Failed to mark all notifications as read:", err);
      });
  }

  function prependTicketRow(e) {
    const tbody = document.querySelector("table tbody");
    if (!tbody) return;

    const isTicketsPage = window.location.pathname.includes("/tickets");
    if (!isTicketsPage) return;

    const emptyRow = tbody.querySelector("td[colspan]");
    if (emptyRow) emptyRow.closest("tr").remove();

    const statusClasses = {
      open: "badge-amber",
      in_progress: "badge-blue",
      resolved: "badge-emerald",
      closed: "badge-emerald",
    };
    const priorityClasses = {
      high: "badge-red",
      medium: "badge-amber",
      low: "badge-blue",
    };

    const sc = statusClasses[e.status] || "badge-gray";
    const pc = priorityClasses[e.priority] || "badge-gray";
    const initial = (e.userName || "U").charAt(0).toUpperCase();

    const tr = document.createElement("tr");
    tr.className = "animate-highlight";
    tr.innerHTML = `
          <td><div class="cell-name">#${e.ticketId} - ${e.title}</div></td>
            <td class="cell-light">${e.categoryName || "None"}</td>
            <td><span class="badge ${sc}">${e.status.replace("_", " ")}</span></td>
            <td><span class="badge ${pc}">${e.priority}</span></td>
            <td>
                <div class="cell-with-avatar">
                    <div class="avatar-circle-sm">${initial}</div>
                    <span class="cell-light">${e.userName || "N/A"}</span>
                </div>
            </td>
            <td class="cell-muted">Just now</td>
            <td class="text-right">
                <a href="/tickets/${e.ticketId}" class="btn-secondary show-on-hover">
                    View
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </td>
        `;

    tbody.insertBefore(tr, tbody.firstChild);
  }

  function timeAgo(date) {
    const seconds = Math.floor((new Date() - date) / 1000);
    if (seconds < 60) return "Just now";
    const minutes = Math.floor(seconds / 60);
    if (minutes < 60) return `${minutes}m ago`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}h ago`;
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days}d ago`;
    return date.toLocaleDateString();
  }

  const bellBtn = document.getElementById("notification-bell");
  const dropdown = document.getElementById("notification-dropdown");

  if (bellBtn && dropdown) {
    bellBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdown.classList.toggle("hidden");

      const hasUnread = notifications.some((n) => !n.read);
      if (hasUnread && !dropdown.classList.contains("hidden")) {
        markAllNotificationsAsRead();
      }
    });

    document.addEventListener("click", () => {
      dropdown.classList.add("hidden");
    });

    dropdown.addEventListener("click", (e) => {
      e.stopPropagation();
    });
  }

  renderDropdown();
});
