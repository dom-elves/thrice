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

#### linting
`./vendor/bin/pint` for anything laravel
`./vendor/bin/sail npm run lint:check` for anything vue/js

I have both of these baked in locally to run pre-push by adding them as a script in .git/hooks/pre-push, so github actions *should* never fail because of either

#### packages
- Laravel Sail, for local development containerisation using Docker
- Laravel Telescope & Horizon, for app, event etc monitoring
- Laravel Fortify, for auth - though I need to properly look into how this works
- Laravel Reverb, for websocket usage
- tbc, but redis and bits like that will be added in time, when I get to that stage of building


#### info

Before I've started raising PRs, I built some, currently unstyled, bits. Just to make the app workable:
- Login
- Register
- Password reset
- Authenicated Layout
- Guest Layout
- Welcome page
- Dashboard, but with no content

Also included test coverage for the auth related bits. Stopping that for now as a "part 1", because wiring up the rest of laravel fortif
