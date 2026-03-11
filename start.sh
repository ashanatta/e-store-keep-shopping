#!/bin/bash
cd Backend && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT