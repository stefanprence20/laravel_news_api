## News Aggregator API

A RESTful API for a news aggregator service that pulls articles from various sources and provides endpoints for a frontend application to consume.

### Prerequisites

Ensure you have the following installed on your machine:

- [Docker](https://docs.docker.com/get-docker/)
- [Docker Compose](https://docs.docker.com/compose/install/)

### Getting Started

1. **Clone the Repository**:
    ```bash
   git clone https://github.com/stefanprence20/laravel_news_api
   cd laravel_news_api
   
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

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
