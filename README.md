# Laravel CMS – Dockerized Setup

This repository contains a Laravel-based CMS configured to run inside Docker containers for easy development and deployment.

## 📦 Prerequisites

Before you begin, ensure you have the following installed on your machine:

-   **Docker Desktop** (Windows/macOS)  
    or
-   **Docker Engine** and **Docker Compose** (Linux)

---

## ⚙️ Installation & Setup

Follow these steps to get the Laravel CMS up and running:

```bash
# 1. Clone the repository
git clone https://github.com/ahmedadelattya/technical-test.git

# 2. Navigate into the project directory
cd technical-test

# 3. Build and start the Docker containers
docker-compose up -d --build

# 4. Generate Laravel application key
docker exec -it laravel-app php artisan key:generate

# 5. Run database migrations and seed the database
docker exec -it laravel-app php artisan migrate --seed
```
