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
   <img width="1309" alt="Screenshot 2023-12-14 at 1 16 56 PM" src="https://github.com/kejek/henrymeds/assets/3529051/61ff935e-9c45-46f5-856f-20b550f1b549">
   <img width="1298" alt="Screenshot 2023-12-14 at 1 23 36 PM" src="https://github.com/kejek/henrymeds/assets/3529051/6ac29142-85e8-4e01-95a6-042f500852a7">

## Retrieve Provider Schedules

1. You can use a get request to the route: `http://henrymeds.test/api/schedules/{provider_id}` to get the schedules available for that provider.
   <img width="1324" alt="Screenshot 2023-12-14 at 2 19 53 PM" src="https://github.com/kejek/henrymeds/assets/3529051/aa47a69b-7150-447f-adcd-afa715448bb7">

## Updating Schedules

1. Just like creating a schedule, you can use a put request to the route: `http://henrymeds.test/api/schedules/{provider_id}/{schedule_id}` to update the schedule of that provider.

## Deleting Schedules

1. Just like creating, you can use a delete request to the route: `http://henrymeds.test/api/schedules/{provider_id}/{schedule_id}` to delete the schedule.

## Creating a Reservation

1. As a client, you can post to the route: `http://henrymeds.test/api/reservations/{provider_id}` with the following body:
    - time (format: yyy-dd-mm hh:ii:ss)
2. This should return a success with the reservation slot. Confirmed is false by default.
3. You should not be able to create another reservation as a different client or same client on the same provider for the same slot.
   <img width="1313" alt="Screenshot 2023-12-14 at 1 48 20 PM" src="https://github.com/kejek/henrymeds/assets/3529051/d133bdba-871e-45f2-b06b-1b1d11c0bef6">

## Retrieve all Reservations for a provider

1. There is a list of available schedules that have not been filled by a get request to `http://henrymeds.test/api/reservations/{provider_id}` however.
   <img width="1297" alt="Screenshot 2023-12-14 at 2 23 14 PM" src="https://github.com/kejek/henrymeds/assets/3529051/cd688018-435b-4f17-a012-46ff9f75f351">
2. No current endpoint for all reserved slots for a provider yet.

## Updating a reservation

1. Use a put request to the route `http://henrymeds.test/api/reservations/{provider_id}/{reservation_id}` to update a reservation.

## Deleting a reservation

1. use a delete request to the route `http://henrymeds.test/api/reservations/{provider_id}/{reservation_id}` to delete the reservation

## Logging out

1. Create a get request to `http://henrymeds.test/api/auth/logout`.
2. You should get a successfully logged out message.
   <img width="1293" alt="Screenshot 2023-12-14 at 1 01 49 PM" src="https://github.com/kejek/henrymeds/assets/3529051/49337951-a89e-4de9-be9a-d9c5f516a00c">

## Things I was not able to get to

1. ~~I was not able to make sure that all reservations are within 15 minute increments (ie, 12:00, 12:15, 12:30, etc). Right now it allows for any time (12:01, 12:10, etc). I would definitely not allow that and make sure the client gave 15 minute increments.~~ Added
2. ~~The schedule (appointment ranges) just come back as a range between the providers start and end. I would have broken this up into the 15 minute slots that a client can see.~~ Added
3. ~~There is no confirmation endpoint, this was something I wanted to add but to be legitimate with the 2 hour window I stopped and started updating this readme at the 2 hour mark.~~ Can Confirm via put now
4. Tests. LOTS OF TESTING. I did not add any tests, as I just wanted to get the functionality in.
5. ~~There is no check on 24 hour advanced notice. I needed to think a little on how to add this, and just hit the 2 hour mark.~~ Required Reservations to be made 24 hours in advance.
6. ~~Reservations do not expire. I would have created a check on the current time vs when the reservation was created, and removed that reservation if it was not confirmed, and allowed a client to create a new reservation at that time.~~ Reservations expire if they are not confirmed within 30 minutes. Reservations older than that will be deleted. (Prefer to use job, but for this we just use another user reserving the timeslot)
7. ~~Time Zones - i would have probably had something along the line of a time zone for the client/provider. For now, everything is assumed to be UTC, which in the real world isn't the greatest, but saving them as UTC is probably ok (as long as i know the provider/client timezone). Timezones are super fun right? hah~~ Added user timezone. Default UTC.
8. ~~I believe that you can also create a reservation at the last available hour (ie, if the schedule range is from 1pm to 5pm, the client could request an appointment at 5pm). I would definitely not allow this!~~ Fixed, ignores the endDate for the 15 minute increments.
9. ~~I did not specifically give response codes to all response errors. While the error message should be correct, the response code is probably not. Quick change but, in the fairness of the 2 hour limit, it was left out for now.~~ Added response codes.
