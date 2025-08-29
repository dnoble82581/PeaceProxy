# PeaceProxy Docs Publishing Quick Start

## Local preview
```bash
pip install mkdocs mkdocs-material
mkdocs serve                   # open http://127.0.0.1:8000
```

## GitHub Pages
1. Commit `mkdocs.yml`, `/docs`, and `.github/workflows/deploy-docs.yml` to your repo on the `main` branch.
2. In GitHub: **Settings → Pages** → Source: **GitHub Actions**.
3. Push to `main`—the workflow builds and deploys to Pages.

## Netlify (optional)
- Create a new site from your repo.
- Build command: `pip install mkdocs mkdocs-material && mkdocs build`
- Publish directory: `site`
