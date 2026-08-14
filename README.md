# thrice

## requirements
- php 8.3^
- composer
- docker

### install
1. clone down the repo in your preferred way
2. `composer install`
3. `cd thrice`
4. `./vendor/bin/sail up -d`, the `-d` flag runs the app in detached mode, so you can still use the terminal. This should automatically run `build` but if it doesn't, try `./vendor/bin/sail down` then `./vendor/bin/sail build` followed by `up -d`.
5. `./vendor/bin/sail npm install`
6. `./vendor/bin/sail artisan migrate`
7. app should now be running at 127.0.0.1



#### packages
- Laravel Sail, for local development containerisation using Docker
- Laravel Telescope, for app monitoring