FROM weirygon/laravel

COPY . .

CMD php artisan serve --host=0.0.0.0 --port=80