<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Create, save, and generate a personal portfolio using three templates.">
  <title>PorfolioG — Portfolio Template Generator</title>
  <link rel="stylesheet" href="style.css">
  <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
  <script src="config.js"></script>
  <script src="app.js" defer></script>
</head>
<body>
  <header class="site-header">
    <a class="brand" href="#home" aria-label="PorfolioG home"><span class="brand-mark">P</span> PorfolioG</a>
    <nav aria-label="Main navigation">
      <a href="#home">Home</a>
      <a href="#create">Create</a>
      <a href="#templates">Templates</a>
      <a href="#manage">My Portfolios</a>
      <a href="logout.php">Log out</a>
    </nav>
  </header>

  <main>
    <section class="hero section" id="home">
      <div class="hero-copy">
        <p class="eyebrow">YOUR WORK. YOUR STORY.</p>
        <h1><i>Build a portfolio that suits <span>for you.</span></i></h1>
        <p class="hero-text">Enter your details once, choose one of three designs, and generate a portfolio you can preview, update, and manage.</p>
        <div class="hero-actions">
          <a class="button primary" href="#create">Create a portfolio <span aria-hidden="true">→</span></a>
          <a class="button secondary" href="#templates">Explore templates</a>
        </div>
        <div class="hero-note"><span class="status-dot"></span> Three templates · Online saving · Easy editing</div>
      </div>
      <div class="hero-art" aria-label="Portfolio preview illustration">
        <div class="art-back"></div>
        <div class="art-card">
          <div class="art-top"><span></span><span></span><span></span></div>
          <div class="art-profile"><div class="art-avatar">YOU</div><div><b>Your Name</b><small>Student & Creator</small></div></div>
          <div class="art-line wide"></div><div class="art-line"></div>
          <div class="art-pills"><i></i><i></i><i></i></div>
          <div class="art-projects"><div></div><div></div></div>
        </div>
        <div class="floating-tag">✦ Your next chapter</div>
      </div>
    </section>

    <section class="section how-section">
      <div class="section-heading"><p class="eyebrow">HOW IT WORKS</p><h2>From details to portfolio in a few steps.</h2></div>
      <div class="steps-grid">
        <article class="step-card"><span class="step-number">01</span><h3>Add your details</h3><p>Fill in your profile, education, skills, projects, and experience.</p></article>
        <article class="step-card"><span class="step-number">02</span><h3>Pick a style</h3><p>Choose Simple, Modern, or Creative to match your personality.</p></article>
        <article class="step-card"><span class="step-number">03</span><h3>Generate & manage</h3><p>Preview your result, save changes, and manage saved portfolios.</p></article>
      </div>
    </section>

    <section class="section form-section" id="create">
      <div class="section-heading left-heading"><p class="eyebrow">PORTFOLIO BUILDER</p><h2 id="form-title">Create your portfolio</h2><p>Fields marked with * are required. Your portfolio is saved to your configured online database.</p></div>
      <form id="portfolio-form">
        <input type="hidden" id="portfolio-id">
        <div class="form-panel">
          <div class="panel-heading"><span class="panel-icon">01</span><div><h3>Personal information</h3><p>Tell visitors who you are.</p></div></div>
          <div class="form-grid">
            <label>Full name *<input id="full_name" name="full_name" required maxlength="120" placeholder="e.g. Alex Santos"></label>
            <label>Email *<input id="email" name="email" type="email" required maxlength="180" placeholder="alex@example.com"></label>
            <label>Contact number<input id="contact_number" name="contact_number" maxlength="50" placeholder="+63 900 000 0000"></label>
            <label>Address<input id="address" name="address" maxlength="240" placeholder="City, Country"></label>
            <label class="full-width">Profile picture URL<input id="profile_picture" name="profile_picture" type="url" placeholder="https://example.com/photo.jpg"><small>Use a publicly accessible image URL. Do not enter private account links.</small></label>
            <label class="full-width">About me *<textarea id="about_me" name="about_me" required rows="4" maxlength="3000" placeholder="Write a short introduction about yourself..."></textarea></label>
          </div>
        </div>

        <div class="form-panel">
          <div class="panel-heading"><span class="panel-icon">02</span><div><h3>Education & skills</h3><p>Share what you have learned.</p></div></div>
          <div class="form-grid">
            <label class="full-width">Educational background<textarea id="education" name="education" rows="3" maxlength="3000" placeholder="School, course, year, awards..."></textarea></label>
            <label class="full-width">Skills<textarea id="skills" name="skills" rows="3" maxlength="2000" placeholder="HTML, CSS, Java, communication..."></textarea><small>Separate skills with commas or new lines.</small></label>
          </div>
        </div>

        <div class="form-panel">
          <div class="panel-heading"><span class="panel-icon">03</span><div><h3>Projects & experience</h3><p>Highlight your work and experience.</p></div></div>
          <div class="form-grid">
            <label class="full-width">Projects<textarea id="projects" name="projects" rows="4" maxlength="4000" placeholder="Project name — description — link (one project per line)"></textarea></label>
            <label class="full-width">Work experience<textarea id="experience" name="experience" rows="4" maxlength="4000" placeholder="Role, organization, dates, and responsibilities..."></textarea></label>
            <label class="full-width">Social media / website links<textarea id="social_links" name="social_links" rows="3" maxlength="2000" placeholder="GitHub: https://github.com/username&#10;LinkedIn: https://linkedin.com/in/username"></textarea></label>
          </div>
        </div>

        <div class="form-panel">
          <div class="panel-heading"><span class="panel-icon">04</span><div><h3>Choose your template</h3><p>You can change this at any time.</p></div></div>
          <div class="template-options">
            <label class="template-option"><input type="radio" name="template" value="simple" checked><span class="mini-preview mini-simple"><i></i><i></i><i></i></span><b>01 · Simple</b><small>Clean and professional</small></label>
            <label class="template-option"><input type="radio" name="template" value="modern"><span class="mini-preview mini-modern"><i></i><i></i><i></i></span><b>02 · Modern</b><small>Bold cards and contrast</small></label>
            <label class="template-option"><input type="radio" name="template" value="creative"><span class="mini-preview mini-creative"><i></i><i></i><i></i></span><b>03 · Creative</b><small>Expressive and colorful</small></label>
          </div>
        </div>

        <div class="form-actions">
          <button class="button primary" type="submit" id="save-button">Save portfolio</button>
          <button class="button secondary" type="button" id="preview-form-button">Preview draft</button>
          <button class="button text-button hidden" type="button" id="cancel-edit-button">Cancel edit</button>
        </div>
        <p id="form-message" class="message" role="status" aria-live="polite"></p>
      </form>
    </section>

    <section class="section templates-section" id="templates">
      <div class="section-heading"><p class="eyebrow">THREE DISTINCT STYLES</p><h2>Choose the look that fits your story.</h2><p>Every template uses the same saved information, presented in a different design.</p></div>
      <div class="template-showcase">
        <article class="showcase-card">
          <div class="showcase-image show-simple">
            <div class="show-simple-avatar">

            </div><div class="show-lines">
              <i></i><i></i><i></i></div><div class="show-block"></div></div>
              <div class="showcase-copy">
                <span class="template-label">TEMPLATE 01</span>
                <h3>Simple</h3>
                <p>Elegant typography and a distraction-free layout.</p>
                <button class="button secondary choose-template" data-template="simple">Use Simple</button></div></article>
        <article class="showcase-card">
          <div class="showcase-image show-modern">
            <div class="show-modern-sidebar"></div>
            <div class="show-modern-content"><i></i><i></i><div><b></b><b></b></div></div></div>
            <div class="showcase-copy"><span class="template-label">TEMPLATE 02</span>
              <h3>Modern</h3><p>Dark visual styling, accent colors, and content cards.</p><button class="button secondary choose-template" data-template="modern">Use Modern</button></div></article>
        <article class="showcase-card">
          <div class="showcase-image show-creative">
            <div class="creative-shape"></div>
            <div class="creative-type">MAKE<br>IT<br>MATTER.</div>
            <div class="creative-lines"><i></i><i></i><i></i></div></div>
            <div class="showcase-copy"><span class="template-label">TEMPLATE 03</span>
              <h3>Creative</h3><p>Playful shapes, expressive type, and an editorial layout.</p><button class="button secondary choose-template" data-template="creative">Use Creative</button></div></article>
      </div>
    </section>

    <section class="section manage-section" id="manage">
      <div class="section-heading left-heading"><p class="eyebrow">YOUR SAVED WORK</p><h2>Manage portfolios</h2><p>Saved portfolios load from the online database when it is configured.</p></div>
      <div id="database-status" class="database-status"><span class="status-dot"></span> Checking database configuration…</div>
      <div id="portfolio-list" class="portfolio-list"><div class="empty-state">Loading saved portfolios…</div></div>
    </section>

    <section class="section preview-section hidden" id="preview-section">
      <div class="preview-toolbar"><div><p class="eyebrow">GENERATED PORTFOLIO</p><h2>Preview</h2></div><div class="preview-actions"><button class="button secondary" id="close-preview">Close preview</button><button class="button primary" id="edit-preview">Edit information</button></div></div>
      <div id="portfolio-preview"></div>
    </section>
  </main>

  <footer><a class="brand footer-brand" href="#home"><span class="brand-mark">P</span> PortfolioG</a><p>Portfolio Template Generator · Student Project</p><small>Remember to configure your cloud database before publishing.</small></footer>
  <div id="toast" class="toast" role="status" aria-live="polite"></div>
</body>
</html>
