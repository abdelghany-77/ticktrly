document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("ticket-form");
  const modal = document.getElementById("ai-modal");
  const aiQuestion = document.getElementById("ai-question");
  const aiAnswer = document.getElementById("ai-answer");
  const btnUse = document.getElementById("btn-use-answer");
  const btnNormal = document.getElementById("btn-create-normal");
  const resolved = document.getElementById("ai-resolved");
  const resolvedQuestion = document.getElementById("resolved-question");
  const resolvedAnswer = document.getElementById("resolved-answer");
  const submitBtn = document.getElementById("submit-btn");

  // Read URLs from data attributes on the form
  const aiSearchUrl = form.dataset.aiSearchUrl;
  const storeUrl = form.dataset.storeUrl;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

  let aiData = null;
  let skipAiCheck = false;

  function showModal() {
    modal.style.display = "flex";
  }

  function hideModal() {
    modal.style.display = "none";
  }

  form.addEventListener("submit", function (e) {
    if (skipAiCheck) {
      return;
    }

    e.preventDefault();

    const title = document.getElementById("title").value.trim();
    const description = document.getElementById("description").value.trim();
    const categoryId = document.getElementById("category_id").value;

    if (!title || !description || !categoryId) {
      skipAiCheck = true;
      form.submit();
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = "Checking AI...";

    fetch(aiSearchUrl, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": csrfToken,
        Accept: "application/json",
      },
      body: JSON.stringify({
        title: title,
        description: description,
        category_id: categoryId,
      }),
    })
      .then(function (res) {
        if (!res.ok) throw new Error("Server error: " + res.status);
        return res.json();
      })
      .then(function (data) {
        submitBtn.disabled = false;
        submitBtn.textContent = "Save Ticket";

        if (data.found) {
          aiData = data;
          aiQuestion.textContent = data.question;
          aiAnswer.innerHTML = data.answer;
          showModal();
        } else {
          skipAiCheck = true;
          form.submit();
        }
      })
      .catch(function (err) {
        console.error("AI search error:", err);
        submitBtn.disabled = false;
        submitBtn.textContent = "Save Ticket";
        skipAiCheck = true;
        form.submit();
      });
  });

  btnUse.addEventListener("click", function () {
    btnUse.disabled = true;
    btnUse.textContent = "Saving...";

    const formData = new FormData(form);
    formData.append("solved_by_ai", "1");

    fetch(storeUrl, {
      method: "POST",
      body: formData,
      headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
      },
    })
      .then(function (res) {
        if (!res.ok) throw new Error("Server error");
        return res.json();
      })
      .then(function (data) {
        hideModal();
        form.closest(".create-ticket-card").style.display = "none";
        resolvedQuestion.textContent = aiData.question;
        resolvedAnswer.innerHTML = aiData.answer;

        const desc = document.querySelector(
          "#ai-resolved .ai-resolved-subtitle",
        );
        if (desc)
          desc.textContent =
            "A resolved ticket has been logged — the answer below should help you";

        resolved.style.display = "block";
      })
      .catch(function (err) {
        console.error("Error saving ticket:", err);
        hideModal();
        // Fallback
        const hiddenField = document.createElement("input");
        hiddenField.type = "hidden";
        hiddenField.name = "solved_by_ai";
        hiddenField.value = "1";
        form.appendChild(hiddenField);
        skipAiCheck = true;
        form.submit();
      });
  });

  btnNormal.addEventListener("click", function () {
    hideModal();

    skipAiCheck = true;
    form.submit();
  });

  modal.addEventListener("click", function (e) {
    if (e.target === modal) {
      hideModal();
    }
  });
});
