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

# 3. Copy the example environment file
cp .env.example .env

# 4. Build and start the Docker containers
docker-compose up -d --build

# 5. Generate Laravel application key
docker exec -it laravel-app php artisan key:generate

# 6. Run database migrations and seed the database
docker exec -it laravel-app php artisan migrate --seed
```

---

## ⚙️ Environment Configuration Notes

After copying `.env.example` to `.env`, ensure the following database settings are correctly set for Docker:

```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
DB_ROOT_PASSWORD=root
```
---

## 🌐 Access the Application

Your Laravel CMS should now be accessible at:
**http://localhost:8000**

---

## 🔐 Default Login Credentials

You can log in using the following admin credentials:

-   **Email:** `admin@test.com`
-   **Password:** `P@ssw0rd`

---

## 🧭 CMS Dashboard Overview

Once logged in, you can:

-   Manage Users: Create, edit, assign roles
-   Manage Roles: CRUD operations
-   Manage Products: CRUD operations with name, description, price, etc.
-   Manage Orders: View orders, update status, assign handlers

---

## ✅ Running Tests

The application includes **unit and feature tests** for CMS functionalities.

To run all tests inside the Laravel container:

```bash
docker exec -it laravel-app php artisan test
````
