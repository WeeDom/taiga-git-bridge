## Run with Docker Compose (Nginx & PHP-FPM)

This setup uses two containers:
- **php**: Runs PHP-FPM and SlimPHP
- **nginx**: Serves requests and proxies PHP files to the php container

Build and start the app in detached mode:

```bash
docker compose up --build -d
```

Then visit [http://localhost:8080](http://localhost:8080)

All requests are routed to `index.php` as needed for SlimPHP routing.
## Run with Docker

Build and run the container:

```bash
docker build -t taiga-git-bridge .
docker run -p 8080:8080 taiga-git-bridge
```

Then visit [http://localhost:8080](http://localhost:8080)
# SlimPHP Day 1 Setup

This project uses [SlimPHP](https://www.slimframework.com/) as the framework.

## Getting Started

1. **Install dependencies**

	If not already done, run:
	```bash
	composer install
	```
	Always ensure you run `composer install` after adding or updating dependencies. The Docker build process also runs this step to keep dependencies up to date.

2. **Run the application**

	You can use PHP's built-in server:
	```bash
	php -S localhost:8080 index.php
	```

3. **Access the app**

	Open your browser and go to [http://localhost:8080](http://localhost:8080)

## Project Structure

- `index.php` — Main entry point with a basic Slim route
- `composer.json` — Composer dependencies

## Next Steps
- Add more routes and features as needed!
# taiga-git-bridge
Connecting git and taiga

## Purpose

Enable git and taiga to keep each other up to date.

## Create a story/task/issue in taiga, have it created as PR/Issue in github

Goal Build an open-source bridge that syncs Taiga tasks ↔ GitHub Issues (titles, descriptions, status, comments, labels).

Scope

MVP (Taiga → GitHub, one-way):

Create/update GitHub Issues from Taiga tasks.

Sync title, description, status (open/closed), labels.

Store Taiga IDs in GitHub (body or taiga:#123 label).

Dockerized deployment.


Extended (two-way):

GitHub → Taiga updates.

Comment sync both ways; basic assignee mapping.

Conflict policy (last-write-wins or system-of-record).

Drift reconciliation job.



Tech Python (FastAPI/Flask) or Node (Express). Taiga REST + webhooks; GitHub Issues API + webhooks. SQLite/Postgres for ID mapping + logs. Dockerized. Auth via Taiga token + GitHub PAT/App.

Timeline (5 days, ~2–3 hrs/day)

Day 1: Repo scaffold, Dockerfile, .env, API connectivity tests.

Day 2: Taiga webhook → GitHub issue creation/update + mapping/logging.

Day 3: GitHub webhook → Taiga updates + loop prevention.

Day 4: Comment sync + assignee mapping.

Day 5: README, sequence diagram, screenshots, local deploy, short write-up.


Deliverables Public repo, Docker setup, detailed README, screenshots, and a brief blog/case study. Stretch goals: CLI reconciliation, GitHub Action, mocked-API tests, CI.
