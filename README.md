## News Aggregator API

A RESTful API for a news aggregator service that pulls articles from various sources and provides endpoints for a frontend application to consume.

### Prerequisites

Ensure you have the following installed on your machine:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Installation & Usage

1. **Clone the Repository**:
    ```bash
   git clone https://github.com/stefanprence20/laravel_news_api
   cd laravel_news_api
   cp .env.example .env
2. **Build and start the containers**:
    ```bash
   docker compose up -d --build
3. **Install Laravel dependencies**:
    ```bash
   docker compose exec app composer install
4. **Generate the application key**:
    ```bash
   docker compose exec app php artisan key:generate
5. **Run the database migrations**:
    ```bash
   docker compose exec app php artisan migrate
6. **Generate Open API documentation**:
    ```bash
   docker compose exec app php artisan l5-swagger:generate
7. **Create the test database**:
    ```bash
   docker compose exec app php artisan app:create-test-database test_news_api
8. **To list all the available routes for API**:
    ```bash
   docker compose exec app php artisan route:list --path=api
9. **To fetch articles from registered sources manually**:
    ```bash
   docker compose exec app php artisan app:fetch-articles

These commands will set up the database and run the server on http://localhost.

You can check and test generated OpenAPI/Swagger API documentation on this link: http://localhost/api/documentation.
   
## License

This project is licensed under the [MIT license](https://opensource.org/licenses/MIT).
