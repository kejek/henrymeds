## Setup

1. Make sure you have composer installed. [Get Composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos)
2. Clone this repository.
3. Copy `.env.testing` and rename it to `.env`.
4. Change the DB\_ entries in the `.env` file to point to your database name with the correct Username and Password.
5. Run `composer install`.
6. Run `php artisan migrate`.
7. Run `npm i && npm run build`.
8. If you wish to use herd, check out Setup Herd below, otherwise you can use `php artisan serve`.

## Setup Herd

If you wish to run with Herd, you can install it from [Here](https://herd.laravel.com/). Once you have installed Herd, you can easily add the application to Herd.

1. Open up the Herd Settings.
2. Under General, Herd Paths, add the path to the application directory.
3. The website should now be available under Sites as `http://henrymeds.test`.

## Creating a Client

1. You can make a post to the route: `http://henrymeds.test/api/auth/register` with the following in the body:
    1. name
    2. email
    3. password
    4. c_password
    5. client (true)
2. This should return a success message and an accessToken

## Creating a Provider

1. You can make a post to the route: `http://henrymeds.test/api/auth/register` with the following in the body:
    1. name
    2. email
    3. password
    4. c_password
    5. provider (true)
2. This should return a success message and an accessToken

## Authentication

1. For the below routes, the header needs to be set:
    1. Accept: application/json
    2. Authorization: Bearer `AccessToken`

## Creating a Schedule

1. As a provider, you can post to the route: `http://henrymeds.test/api/provider/schedule/add` with the following body:
    1. start_time (format: yyy-dd-mm hh:ii:ss)
    2. end_time (format: yyy-dd-mm hh:ii:ss)
2. This should return a success message with the time block for schedule.
3. You should not be able to add overlapping schedules (ie: one from 12pm to 2pm, and another from 1pm to 3pm on the same day).

## Creating a Reservation

1. As a client, you can post to the route: `http://henrymeds.test/api/reservations/add/{provider_id}` with the following body:
    1. time (format: yyy-dd-mm hh:ii:ss)
2. This should return a success with the reservation slot. Confirmed is false by default.
3. You should not be able to create another reservation as a different client or same client on the same provider for the same slot.

## Logging out

1. Create a get request to `http://henrymeds.test/api/auth/logout`.
2. You should get a successfully logged out message.

## Things I was not able to get to

1. I was not able to make sure that all reservations are within 15 minute increments (ie, 12:00, 12:15, 12:30, etc). Right now it allows for any time (12:01, 12:10, etc). I would definitely not allow that and make sure the client gave 15 minute increments.
2. The schedule (appointment ranges) just come back as a range between the providers start and end. I would have broken this up into the 15 minute slots that a client can see.
3. There is no confirmation endpoint, this was something I wanted to add but to be legitimate with the 2 hour window I stopped and started updating this readme at the 2 hour mark.
4. Tests. LOTS OF TESTING. I did not add any tests, as I just wanted to get the functionality in.
5. There is no check on 24 hour advanced notice. I needed to think a little on how to add this, and just hit the 2 hour mark.
6. Reservations do not expire. I would have created a check on the current time vs when the reservation was created, and removed that reservation if it was not confirmed, and allowed a client to create a new reservation at that time.
7. Time Zones - i would have probably had something along the line of a time zone for the client/provider. For now, everything is assumed to be UTC, which in the real world isn't the greatest, but saving them as UTC is probably ok (as long as i know the provider/client timezone). Timezones are super fun right? hah
8.
