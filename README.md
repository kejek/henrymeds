## Setup

1. Make sure you have composer installed. [Get Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
2. Set up a database for this - I used MySQL here, but you can use PostgreSQL as well. I believe the DB_CONNECTION value would be pgsql and port 5432
3. Clone this repository.
4. Copy `.env.example` and rename it to `.env`.
5. Change the DB\_ entries in the `.env` file to point to your database name with the correct Username and Password.
6. Run `composer install`.
7. Run `php artisan key:generate`.
8. Run `php artisan migrate`.
9. Run `npm i && npm run build`.
10. If you wish to use herd, check out Setup Herd below, otherwise you can use `php artisan serve`.

## Setup Herd

If you wish to run with Herd, you can install it from [Here](https://herd.laravel.com/). Once you have installed Herd, you can easily add the application to Herd.

1. Open up the Herd Settings.
2. Under General, Herd Paths, add the path to the application directory.
3. The website should now be available under Sites as `http://henrymeds.test`.

## Note: Replace All routes with `http://127.0.0.1:8000` if you did not use Herd

## Creating a Client

1. You can make a post to the route: `http://henrymeds.test/api/auth/register` with the following in the body:
    - name
    - email
    - password
    - c_password
    - client (true)
2. This should return a success message and an accessToken
   <img width="1287" alt="Screenshot 2023-12-14 at 12 58 30 PM" src="https://github.com/kejek/henrymeds/assets/3529051/f81f1ecb-c996-4f02-917c-4b24851e63f6">

## Creating a Provider

1. You can make a post to the route: `http://henrymeds.test/api/auth/register` with the following in the body:
    - name
    - email
    - password
    - c_password
    - provider (true)
2. This should return a success message and an accessToken
   <img width="1325" alt="Screenshot 2023-12-14 at 12 58 02 PM" src="https://github.com/kejek/henrymeds/assets/3529051/0b0e88ae-6f49-4e2c-bbd7-e74f360d15e6">

## Authentication

1. For the below routes, the header needs to be set: - Accept: application/json - Authorization: Bearer `AccessToken`
   <img width="1294" alt="Screenshot 2023-12-14 at 12 58 52 PM" src="https://github.com/kejek/henrymeds/assets/3529051/a4fa941a-0755-4ba9-84d8-3a168508fe1d">

## Creating a Schedule

1. As a provider, you can post to the route: `http://henrymeds.test/api/schedules` with the following body:
    - start_time (format: yyy-dd-mm hh:ii:ss)
    - end_time (format: yyy-dd-mm hh:ii:ss)
2. This should return a success message with the time block for schedule.
3. You should not be able to add overlapping schedules (ie: one from 12pm to 2pm, and another from 1pm to 3pm on the same day).

## Retrieve Provider Schedules

1. You can use a get request to the route: `http://henrymeds.test/api/schedules?provider={uuid}` to get the schedules available for that provider.

## Updating Schedules

1. Just like creating a schedule, you can use a put request to the route: `http://henrymeds.test/api/schedules/{uuid}` to update the schedule.

## Deleting Schedules

1. Just like creating, you can use a delete request to the route: `http://henrymeds.test/api/schedules/{uuid}` to delete the schedule.

## Creating a Reservation

1. As a client, you can post to the route: `http://henrymeds.test/api/reservations` with the following body:
    - time (format: yyy-dd-mm hh:ii:ss)
    - provider (string, uuid)
2. This should return a success with the reservation slot. Confirmed is false by default.
3. You should not be able to create another reservation as a different client or same client on the same provider for the same slot.

## Retrieve all Reservations for a provider

1. There is a list of available schedules that have not been filled by a get request to `http://henrymeds.test/api/reservations?provider={uuid}`.

## Retrieve all Reservations for a Client

1. Just a simple get to `http://henrymeds.test/api/reservations` will return the reservations for the current user as long as its a client.

## Updating a reservation

1. Use a put request to the route `http://henrymeds.test/api/reservations/{uuid}` to update a reservation.
    - time (format: yyy-dd-mm hh:ii:ss)
    - confirmed (true/false)
2. As you can see, you can confirm the reservation on updating it.

## Deleting a reservation

1. use a delete request to the route `http://henrymeds.test/api/reservations/{uuid}` to delete the reservation

## Logging out

1. Create a get request to `http://henrymeds.test/api/auth/logout`.
2. You should get a successfully logged out message.

## Notes

1. Still a work in progress. I am using this as a learning experience now to work with times, and being able to modify and update reservations and schedules.
2. There are bugs, and to be honest, I haven't fully tested every route right now. I am going to go through and make sure things work and fix with tests written.
3. I understand TDD should have been used here, which would fix the above issue, but the idea at first was to get something going quickly and in a timeframe (2 hours). This meant that tests were probably not going to be ready for this. Now that I do not have a time limit, I have the ability to start filling this out and adding tests.
