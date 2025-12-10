git clone <repo>
cd <repo>

cp backend/.env.example backend/.env

cp frontend/.env.example frontend/.env

docker compose build
docker compose up -d

docker compose exec backend bash

composer install
php artisan key:generate
php artisan jwt:secret
php artisan migrate

docker compose exec backend bash
php artisan results:import storage/app/results.csv

//gdy front nie wstaje 
docker compose run --rm frontend npm install
docker compose up -d
