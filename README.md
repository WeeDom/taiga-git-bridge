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


## further clarification of steps needed.


🔧 Step 1. Decide on mapping (source of truth)

Epic ↔ GitHub Milestone

Story ↔ GitHub Issue

Task ↔ GitHub Issue (labelled task)

Taiga bug ↔ GitHub Issue (labelled bug)

Status ↔ GitHub Project column

👉 Add Taiga ID into GitHub Issue title or label (e.g. [TG-123] Story title). That becomes your “foreign key” for syncing.

🔧 Step 2. Set up Taiga webhooks

In your Taiga project, go to Admin → Webhooks.

Create a webhook pointing to your bridge service (e.g. https://your-bridge/taiga/webhook).

Configure it to fire on: userstory, task, issue, and epic.

🔧 Step 3. Set up GitHub webhooks

In your GitHub repo, go to Settings → Webhooks → Add Webhook.

URL: https://your-bridge/github/webhook.

Choose events: issues, milestone, pull_request.

🔧 Step 4. Write the bridge service

Basic flow (Python/Flask example):

from flask import Flask, request
import requests

app = Flask(__name__)

# Taiga → GitHub
@app.route("/taiga/webhook", methods=["POST"])
def taiga_webhook():
    data = request.json
    if data["type"] == "userstory":
        story_id = data["data"]["id"]
        title = f"[TG-{story_id}] {data['data']['subject']}"
        desc = data["data"]["description"]
        state = data["data"]["status"]

        sync_story_to_github(story_id, title, desc, state)
    return "ok"

# GitHub → Taiga
@app.route("/github/webhook", methods=["POST"])
def github_webhook():
    data = request.json
    if "issue" in data:
        issue = data["issue"]
        taiga_id = extract_taiga_id(issue["title"])
        if data["action"] == "closed":
            mark_story_done_in_taiga(taiga_id)
    return "ok"

🔧 Step 5. Implement API helpers

GitHub REST API v3

Create/Update Issue: POST /repos/{owner}/{repo}/issues

Close Issue: PATCH /repos/{owner}/{repo}/issues/{issue_number}

Taiga API

Stories: PATCH /userstories/{id} (to update status)

Tasks: PATCH /tasks/{id}

Epics: PATCH /epics/{id}

🔧 Step 6. Handle conflicts

Decide rules:

If Story closed in GitHub → always mark closed in Taiga.

If status changed in Taiga → update Project column in GitHub.

Epics/milestones: keep titles/descriptions in sync.

🔧 Step 7. Deploy + secure

Deploy the bridge (Heroku, Render, Fly.io, or your own VPS).

Add secret tokens to webhooks (both sides support a signing secret).

Store Taiga + GitHub API tokens as environment variables.

🔧 Step 8. Test

Create a Story in Taiga → should auto-create a GitHub Issue with the right milestone.

Close Issue via PR → Taiga Story moves to “Done.”

Update Epic in Taiga → milestone in GitHub updates.

## Note on Taiga Webhook URLs

Taiga does not accept webhook URLs that use `localhost` or private IP addresses (e.g., `192.168.*`).

To test webhooks locally, you must expose your bridge service to the public internet using a service like [ngrok](https://ngrok.com/) or [localtunnel](https://localtunnel.github.io/www/).

More simply - add something lie `127.0.0.1   slimphp.local` to the host (dev machine) /etc/hosts.


Set your webhook URL in Taiga to the public address provided by these tunneling services.
# Debugging with Xdebug and VS Code in Docker

## Step-by-step setup and troubleshooting

### 1. Install and configure Xdebug in Docker
- Use the official PHP Docker image (e.g., `php:8.2-apache`).
- Install Xdebug via PECL in your Dockerfile:
  ```Dockerfile
  RUN pecl install xdebug \
      && docker-php-ext-enable xdebug
  COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
  ```
- Example `xdebug.ini`:
  ```ini
  zend_extension=xdebug
  xdebug.mode=debug
  xdebug.start_with_request=yes
  xdebug.client_host=<your_host_ip>
  xdebug.client_port=9003
  xdebug.log=/tmp/xdebug.log
  xdebug.log_level=10
  ```
  Replace `<your_host_ip>` with your actual host IP (see below).

### 2. VS Code launch.json configuration
- Add to `.vscode/launch.json`:
  ```json
  {
    "name": "Listen for Xdebug",
    "type": "php",
    "request": "launch",
    "port": 9003,
    "pathMappings": {
      "/var/www/html": "${workspaceFolder}"
    }
  }
  ```

### 3. Remove port mapping for 9003 in Docker Compose
- Do **not** expose port 9003 in your `docker-compose.yml`:
  ```yaml
  # Remove or comment out:
  # ports:
  #   - "9003:9003"
  ```
  Xdebug connects out to VS Code; you do not need to expose this port.

### 4. Find your host IP address
- Run:
  ```bash
  hostname -I
  ```
- Use the first IP address in your `xdebug.ini` as `xdebug.client_host`.

### 5. Start VS Code debugger
- Open the Run and Debug panel in VS Code.
- Select "Listen for Xdebug" and click the green start button.
- The blue bar at the bottom of VS Code means it is listening.

### 6. Verify port usage with netstat
- To check which process is listening on port 9003:
  ```bash
  sudo netstat -tulnp | grep 9003
  ```
  - `-t` = TCP
  - `-u` = UDP
  - `-l` = Listening sockets
  - `-n` = Show numeric addresses/ports
  - `-p` = Show process ID/program name
- You should see VS Code (`code`) listening, not Docker.
- If Docker is listening, remove the port mapping and restart.

### 7. Troubleshooting Xdebug connection
- If breakpoints are not hit:
  - Check `/tmp/xdebug.log` inside the container for connection attempts/errors:
    ```bash
    docker exec -it <container_name> cat /tmp/xdebug.log
    ```
  - Common error: `getaddrinfo: No such file or directory` for `host.docker.internal` on Linux. Use your host IP instead.
  - Make sure VS Code is running and listening before triggering PHP code.

### 8. Test with a minimal script
- Create `public/test.php`:
  ```php
  <?php
  xdebug_break();
  echo "Test";
  ```
- Set a breakpoint and access `http://localhost:8081/test.php`.

### 9. Summary of key lessons
- On Linux, use your host IP for `xdebug.client_host`.
- Do not expose port 9003 in Docker Compose.
- Use netstat to check port usage and resolve conflicts.
- Always check Xdebug logs for connection issues.

---

Add these steps to your workflow for reliable PHP debugging in Docker with VS Code!