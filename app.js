(() => {
  "use strict";

  const $ = (selector) => document.querySelector(selector);
  const form = $("#portfolio-form");
  const formMessage = $("#form-message");
  const list = $("#portfolio-list");
  const statusBox = $("#database-status");
  const previewSection = $("#preview-section");
  const previewContainer = $("#portfolio-preview");
  const config = window.PORTFOLIO_CONFIG || {};
  const configured = config.SUPABASE_URL &&
    config.SUPABASE_ANON_KEY &&
    !config.SUPABASE_URL.includes("PASTE_YOUR") &&
    !config.SUPABASE_ANON_KEY.includes("PASTE_YOUR");

  let supabase = null;
  let currentPreviewId = null;
  let savedPortfolios = [];

  if (configured && window.supabase?.createClient) {
    supabase = window.supabase.createClient(config.SUPABASE_URL, config.SUPABASE_ANON_KEY);
    statusBox.innerHTML = '<span class="status-dot"></span> Online database configured. Loading saved portfolios…';
  } else {
    statusBox.classList.add("error");
    statusBox.textContent = "Database not configured yet. Add your Supabase URL and publishable/anon key in config.js to enable online saving.";
  }

  const fieldNames = [
    "full_name", "email", "contact_number", "address", "profile_picture",
    "about_me", "education", "skills", "projects", "experience", "social_links"
  ];

  function getFormData() {
    const data = {};
    fieldNames.forEach((name) => data[name] = $("#" + name).value.trim());
    data.template = form.querySelector('input[name="template"]:checked')?.value || "simple";
    return data;
  }

  function setFormData(data) {
    fieldNames.forEach((name) => $("#" + name).value = data[name] || "");
    const radio = form.querySelector(`input[name="template"][value="${data.template || "simple"}"]`);
    if (radio) radio.checked = true;
    $("#portfolio-id").value = data.id || "";
    $("#form-title").textContent = data.id ? "Edit your portfolio" : "Create your portfolio";
    $("#save-button").textContent = data.id ? "Save changes" : "Save portfolio";
    $("#cancel-edit-button").classList.toggle("hidden", !data.id);
  }

  function showMessage(message, isError = false) {
    formMessage.textContent = message;
    formMessage.classList.toggle("error", isError);
  }

  function toast(message) {
    const element = $("#toast");
    element.textContent = message;
    element.classList.add("show");
    window.setTimeout(() => element.classList.remove("show"), 3200);
  }

  function safeUrl(value) {
    try {
      const url = new URL(value);
      return ["http:", "https:"].includes(url.protocol) ? url.href : "";
    } catch {
      return "";
    }
  }

  function escapeHtml(value = "") {
    return String(value).replace(/[&<>"']/g, (char) => ({
      "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;"
    }[char]));
  }

  function lines(value = "") {
    return value.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
  }

  function chips(value = "") {
    const items = value.split(/[,\n]/).map(s => s.trim()).filter(Boolean);
    if (!items.length) return '<p class="muted-empty">No skills added yet.</p>';
    return `<div class="g-chips">${items.map(item => `<span class="g-chip">${escapeHtml(item)}</span>`).join("")}</div>`;
  }

  function section(title, content) {
    return `<section class="g-section"><h2>${title}</h2>${content}</section>`;
  }

  function renderPortfolio(data) {
    const template = ["simple", "modern", "creative"].includes(data.template) ? data.template : "simple";
    const name = escapeHtml(data.full_name || "Your Name");
    const picture = safeUrl(data.profile_picture || "");
    const avatar = picture
      ? `<img class="g-avatar" src="${escapeHtml(picture)}" alt="Profile picture of ${name}" onerror="this.style.display='none'">`
      : `<div class="g-avatar g-avatar-placeholder" aria-label="Profile picture placeholder">${escapeHtml((data.full_name || "Y").trim().charAt(0).toUpperCase())}</div>`;
    const intro = escapeHtml(data.about_me || "Add an introduction to tell people about yourself.").replace(/\n/g, "<br>");
    const contactItems = [
      data.email ? `<a href="mailto:${escapeHtml(data.email)}">${escapeHtml(data.email)}</a>` : "",
      data.contact_number ? `<span>${escapeHtml(data.contact_number)}</span>` : "",
      data.address ? `<span>${escapeHtml(data.address)}</span>` : ""
    ].filter(Boolean);
    const projects = lines(data.projects);
    const projectContent = projects.length
      ? projects.map((item) => `<div class="g-project">${escapeHtml(item)}</div>`).join("")
      : "<p>No projects added yet.</p>";
    const socialLinks = lines(data.social_links).map((line) => {
      const match = line.match(/^([^:]{1,60}):\s*(https?:\/\/\S+)$/i);
      const url = safeUrl(match ? match[2] : line);
      const label = match ? match[1].trim() : line;
      return url ? `<a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer">${escapeHtml(label)}</a>` : "";
    }).filter(Boolean);
    const html = `
      <article class="generated template-${template}">
        <div class="g-hero">
          ${avatar}
          <div><p class="eyebrow">PORTFOLIO</p><h1>${name}</h1>
          <p class="g-subtitle">${escapeHtml(data.email || "Student · Creator · Learner")}</p>
          <div class="g-links" style="margin-top:12px">${contactItems.join("")}</div></div>
        </div>
        <div class="g-body">
          ${section("About me", `<p>${intro}</p>`)}
          ${section("Education", `<p>${escapeHtml(data.education || "No educational background added yet.").replace(/\n/g, "<br>")}</p>`)}
          ${section("Skills", chips(data.skills))}
          ${section("Projects", projectContent)}
          ${section("Work experience", `<p>${escapeHtml(data.experience || "No work experience added yet.").replace(/\n/g, "<br>")}</p>`)}
          ${section("Find me online", socialLinks.length ? `<div class="g-links">${socialLinks.join("")}</div>` : "<p>No links added yet.</p>")}
        </div>
      </article>`;
    previewContainer.innerHTML = html;
    previewSection.classList.remove("hidden");
    previewSection.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  async function loadPortfolios() {
    if (!supabase) {
      list.innerHTML = '<div class="empty-state">Configure Supabase to load and save portfolios online. See README.md for setup instructions.</div>';
      return;
    }
    const { data, error } = await supabase.from("portfolios")
      .select("*").order("updated_at", { ascending: false });
    if (error) {
      statusBox.classList.add("error");
      statusBox.textContent = "Could not load portfolios: " + error.message;
      list.innerHTML = '<div class="empty-state">Database request failed. Check the SQL setup and Supabase table policies.</div>';
      return;
    }
    savedPortfolios = data || [];
    statusBox.classList.remove("error");
    statusBox.innerHTML = `<span class="status-dot"></span> Connected · ${savedPortfolios.length} saved portfolio${savedPortfolios.length === 1 ? "" : "s"} found`;
    renderSavedList();
  }

  function renderSavedList() {
    if (!savedPortfolios.length) {
      list.innerHTML = '<div class="empty-state">No portfolios saved yet. Use the form above to create your first portfolio.</div>';
      return;
    }
    list.innerHTML = savedPortfolios.map((item) => `
      <article class="saved-card">
        <div><h3>${escapeHtml(item.full_name || "Untitled portfolio")}</h3>
          <p>${escapeHtml(item.email || "No email")} · ${escapeHtml((item.template || "simple").charAt(0).toUpperCase() + (item.template || "simple").slice(1))} template</p>
          <p>Last updated: ${item.updated_at ? new Date(item.updated_at).toLocaleString() : "Not available"}</p>
        </div>
        <div class="saved-actions">
          <button class="small-button" data-action="preview" data-id="${item.id}">Preview</button>
          <button class="small-button" data-action="edit" data-id="${item.id}">Edit</button>
          <button class="small-button danger" data-action="delete" data-id="${item.id}">Delete</button>
        </div>
      </article>`).join("");
  }

  async function savePortfolio(event) {
    event.preventDefault();
    if (!form.reportValidity()) return;
    if (!supabase) {
      showMessage("Online saving is not enabled. Configure config.js and the Supabase database first.", true);
      return;
    }
    const data = getFormData();
    const id = $("#portfolio-id").value;
    const saveButton = $("#save-button");
    saveButton.disabled = true;
    saveButton.textContent = "Saving…";
    showMessage("");

    try {
      let result;
      if (id) {
        result = await supabase.from("portfolios").update(data).eq("id", id).select().single();
      } else {
        result = await supabase.from("portfolios").insert(data).select().single();
      }
      if (result.error) throw result.error;
      showMessage(id ? "Portfolio updated successfully." : "Portfolio saved successfully.");
      toast(id ? "Portfolio changes saved." : "Portfolio created and saved.");
      setFormData({ ...result.data });
      await loadPortfolios();
      renderPortfolio(result.data);
      currentPreviewId = result.data.id;
    } catch (error) {
      showMessage("Could not save portfolio: " + (error.message || "Unknown error"), true);
    } finally {
      saveButton.disabled = false;
      saveButton.textContent = $("#portfolio-id").value ? "Save changes" : "Save portfolio";
    }
  }

  async function manageAction(action, id) {
    const item = savedPortfolios.find((portfolio) => String(portfolio.id) === String(id));
    if (!item) return;
    if (action === "preview") {
      currentPreviewId = item.id;
      renderPortfolio(item);
      $("#edit-preview").classList.remove("hidden");
    } else if (action === "edit") {
      setFormData(item);
      showMessage("Editing saved portfolio. Make changes and select Save changes.");
      $("#create").scrollIntoView({ behavior: "smooth" });
    } else if (action === "delete") {
      const confirmed = window.confirm(`Delete the portfolio for "${item.full_name}"? This cannot be undone.`);
      if (!confirmed) return;
      if (!supabase) return;
      const { error } = await supabase.from("portfolios").delete().eq("id", id);
      if (error) {
        toast("Could not delete portfolio: " + error.message);
        return;
      }
      if (String(currentPreviewId) === String(id)) {
        previewSection.classList.add("hidden");
        currentPreviewId = null;
      }
      if ($("#portfolio-id").value === String(id)) resetForm();
      toast("Portfolio deleted.");
      await loadPortfolios();
    }
  }

  function resetForm() {
    form.reset();
    $("#portfolio-id").value = "";
    $("#form-title").textContent = "Create your portfolio";
    $("#save-button").textContent = "Save portfolio";
    $("#cancel-edit-button").classList.add("hidden");
    showMessage("");
  }

  form.addEventListener("submit", savePortfolio);
  list.addEventListener("click", (event) => {
    const button = event.target.closest("button[data-action]");
    if (button) manageAction(button.dataset.action, button.dataset.id);
  });

  $("#preview-form-button").addEventListener("click", () => {
    if (!form.reportValidity()) return;
    renderPortfolio(getFormData());
    currentPreviewId = $("#portfolio-id").value || null;
    $("#edit-preview").classList.toggle("hidden", !currentPreviewId);
  });
  $("#close-preview").addEventListener("click", () => previewSection.classList.add("hidden"));
  $("#edit-preview").addEventListener("click", () => {
    const item = savedPortfolios.find((portfolio) => String(portfolio.id) === String(currentPreviewId));
    if (item) {
      setFormData(item);
      $("#create").scrollIntoView({ behavior: "smooth" });
    } else {
      $("#create").scrollIntoView({ behavior: "smooth" });
    }
  });
  $("#cancel-edit-button").addEventListener("click", resetForm);

  document.querySelectorAll(".choose-template").forEach((button) => {
    button.addEventListener("click", () => {
      const radio = form.querySelector(`input[name="template"][value="${button.dataset.template}"]`);
      if (radio) radio.checked = true;
      $("#create").scrollIntoView({ behavior: "smooth" });
      toast(button.dataset.template.charAt(0).toUpperCase() + button.dataset.template.slice(1) + " template selected. Fill in your details.");
    });
  });

  loadPortfolios();
})();
