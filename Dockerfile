FROM weirygon/laravel

#Installing Fortify
RUN composer require laravel/fortify

#Installing FlySystem
RUN composer require league/flysystem-aws-s3-v3

#Copy all files and put in image
COPY . .

#Generating Key
RUN php artisan key:generate

#Run Web Serve
EXPOSE 80
CMD php artisan serve --host=0.0.0.0 --port=80
