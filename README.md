# PortfolioG — Online Portfolio Template Generator

A responsive student project that lets users enter portfolio details, save them to a Supabase cloud database, select one of exactly three templates (Simple, Modern, Creative), preview/generate the portfolio, and manage saved records.

## Technologies
- HTML5
- CSS3
- Vanilla JavaScript
- Supabase (online PostgreSQL database)
- Vercel (recommended static hosting)

## Files
- `index.html` — website pages/sections and form
- `style.css` — responsive design and the three distinct template styles
- `app.js` — portfolio form, preview, and database CRUD logic
- `config.js` — public Supabase project URL and publishable/anon key configuration
- `supabase-schema.sql` — database table and demo access policies

## Setup the online database
1. Create a project at https://supabase.com.
2. Open **SQL Editor** in the Supabase dashboard.
3. Open `supabase-schema.sql`, copy its contents, paste them into SQL Editor, and run it.
4. In Supabase Project Settings > API (or Connect), copy the **Project URL** and **publishable/anon key**. The legacy `anon` key can also be used if that is what the dashboard provides.
5. Paste those two values into `config.js`.
6. Open `index.html` using a local web server or deploy the files to test. Create a sample portfolio and refresh to confirm the record remains available.

### Security note for the class demo
The supplied SQL policies intentionally allow anonymous users to read, insert, edit, and delete records so an instructor can test CRUD without signing in. This means anyone who visits the public website can also change or delete records. Do not store sensitive personal data. For a real public product, implement Supabase Auth and restrict row-level security policies to each authenticated user's records. Never expose a `service_role` key or database password in `config.js`.

## Run locally
Because Supabase is called from browser JavaScript, run this folder using a local development server rather than relying on a `file://` URL. In VS Code, install/use the Live Server extension, right-click `index.html`, and choose **Open with Live Server**.

## Deploy to Vercel
1. Push these project files to a GitHub repository.
2. Sign in to https://vercel.com and import that repository.
3. Choose the static/Other framework preset. No build command is needed; set the output directory to `.` if requested.
4. Deploy and open the public URL.
5. Check that `config.js` has the correct Supabase URL and public key in the deployed version.
6. Test create, save, refresh, preview, edit, and delete from the published website.

## Required project documentation checklist
- [ ] Project title and description
- [ ] Technologies used
- [ ] Database platform and project name (do not include passwords or secret keys)
- [ ] Hosting platform and published URL
- [ ] Database structure (`portfolios` table)
- [ ] Screenshot of home page
- [ ] Screenshot of information form
- [ ] Screenshot of all three templates
- [ ] Screenshots proving save/edit/delete and persistence after refresh

## Database table
`portfolios`: `id`, `full_name`, `email`, `contact_number`, `address`, `profile_picture`, `about_me`, `education`, `skills`, `projects`, `experience`, `social_links`, `template`, `created_at`, `updated_at`.
