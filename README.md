
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Setup

- Clone the Repository
- Run `composer install` command in root of repo
- Create SQL database and update below variable in .env file
> copy `.env.example`  to `.env`  for reference
- DB_CONNECTION
- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD
> Other envs to update
- APP_NAME
- APP_URL
- SHOPIFY_WEBHOOK_URL   
- SHOPIFY_API_KEY
- SHOPIFY_API_SECRET

> For shopify api key and secret, you can find in your shopify app (partner dashboard) for more information visit [laravel-shopify](https://github.com/Kyon147/laravel-shopify/wiki/Installation)

- Run `php artisan migrate` command in root
- Run `php artisan key:generate` command in root
- Run `npm install` command in root it will install the front-end dependency

- Run  command `php artisan serve`
  It will start server on http://127.0.0.1:8000

> If you are mac or linux user and uses the [Valet](https://laravel.com/docs/10.x/valet) then link the directory and open it wth linked URL

### Webhook Setup

To receive shopify webhook call, update the `SHOPIFY_WEBHOOK_URL` env variable.
> Make sure it is secure server URL( can be used APP_URL).

For local, if you have valet then run `valet share` (don't have valet run `ngrok http 8000` ) in root it will start ngrok tunnel copy secure ngrok url and update `SHOPIFY_WEBHOOK_URL`  
It will redirect webhook traffic to your local. Also make sure your laravel app is running on localhost port 8000 ( can be done by `php artisan serve`).

### Front-end
After above setup run `npm run dev` in root of project it will compile the front-end code.

- At last open the shopify app in your one of shopify stores, if not installed then install by clicking on  **Select Store** in app overview.
- When you open the shopify app it will show your front-end output.


###  Required Tech Stack
-   PHP: 8.1.13
-   MySql: 8.0.30
-   Composer: 2.5
-   Valet (Optional)

For windows OS, you can follow `wamp` or `xampp` server
