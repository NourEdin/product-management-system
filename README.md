# Product Management System

This project is being build using Symfony 6 and Vue.js 3.

## Requirements:
Before you can run this project, you need Docker to be installed on your machine, along with Docker Compose package

## Installation:

1. Pull the repository

```
git clone git@github.com:NourEdin/product-management-system.git 
```

2. Open .env file and make sure that the local ports are available in your environments `LOCAL_DB_PORT` and `LOCAL_BACKEND_PORT`

3. Set the current user's ID in the env var `LOCAL_UID`

4. Run Docker Compose Up command. The command may be different in your environment (`docker-compose` or `docker compose`).

```
cd product-management-system
sudo docker compose up
```
5. Open the browser and go to `http://localhost:LOCAL_BACKEND_PORT`
