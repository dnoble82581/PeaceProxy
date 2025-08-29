# Publishing Docs (Optional)

You can turn this `/docs` folder into a website with **MkDocs**.

## Local Preview
```bash
pipx install mkdocs-material
mkdocs serve
```
Create a `mkdocs.yml` in the repo root:
```yaml
site_name: Project Docs
docs_dir: docs
theme:
  name: material
nav:
  - Home: index.md
  - Getting Started: getting-started.md
  - Architecture: architecture.md
  - Deployment: deployment.md
  - Security: security.md
  - Multi‑Tenancy: multi-tenancy.md
  - Real‑time: realtime.md
  - Storage: storage.md
  - API:
      - Overview: api/README.md
      - Authentication: api/authentication.md
      - Chat: api/chat.md
  - User Guide:
      - Overview: user-guide/overview.md
      - Conversations: user-guide/conversations.md
      - Reports: user-guide/reports.md
  - FAQ: faq.md
  - Contributing: contributing.md
  - Changelog: changelog.md
```
