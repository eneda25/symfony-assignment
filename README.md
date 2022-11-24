# symfony-task
Technical assignment

In this README file are documented the steps to get the console command up and run.

1. Clone the repository
2. Add `.env.local` file in root directory and specify the MAILER_DSN.
3. Run `composer install`
4. Run symfony console command: `php bin/console app:send-data`
5. Open mailtrap to check the email