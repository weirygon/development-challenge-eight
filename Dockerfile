FROM weirygon/laravel

#Installing Fortify
RUN composer require laravel/fortify

#Copy all files and put in image
COPY . .

#Generating Key
RUN php artisan key:generate
#RUN php artisan migrate:refresh

CMD php artisan serve --host=0.0.0.0 --port=80