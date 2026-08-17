# Toolbox

Toolbox is a Laravel web app I built to connect customers with independent experts and companies who offer professional services. Think of it as a small marketplace: customers browse services by category, check out experts and companies, place orders, and chat with them directly — while experts, companies, and admins each get their own dashboard to manage things on their end.

I built this as a way to dig deeper into Laravel — working through role-based access, custom authentication, and a full order/messaging workflow from scratch.

## What it does

- **Five user roles**, each with a different experience: Admin, Customer, Expert, Company Owner, and Company Admin
- **Browse by category** — services are organized so customers can find what they need
- **Expert & company profiles**, with the option to bookmark favorites
- **Orders** — customers request a service, the expert/company accepts or declines, and customers can leave a review once it's done
- **Messaging** built in, so customers and providers can talk without leaving the platform
- **Admin tools** for managing companies and their owners
- **A contact form** for anyone who just wants to reach out

## Built with

- **Laravel** (PHP) on the backend
- **Blade** templates with custom CSS/JS on the frontend — no frontend framework, kept it simple
- **MySQL** for the database
- Custom middleware for auth and role checks, instead of relying on a package

## Getting it running locally

You'll need PHP 8.2+, Composer, and MySQL installed first.

```bash
# 1. Clone it
git clone https://github.com/shayanHadad/toolbox.git
cd <your-repo>

# 2. Install dependencies
composer install

# 3. Set up your environment
cp .env.example .env
php artisan key:generate
```

Next, open `.env` and point it at your database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=toolbox
DB_USERNAME=root
DB_PASSWORD=
```

Then run the migrations (add `--seed` if you want some sample data to play with):

```bash
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Open `http://localhost:8000` and you're in.

## How roles work

| ID  | Role          | Can do                                                    |
| --- | ------------- | --------------------------------------------------------- |
| 0   | Admin         | Manages companies and their owners, oversees the platform |
| 1   | Customer      | Browses services, places orders, leaves reviews           |
| 2   | Expert        | Offers services independently                             |
| 3   | Company Admin | Handles orders/messages for a company                     |
| 4   | Company Owner | Owns the company profile, manages its admins              |

## A quick tour of the code

```
app/
├── Http/
│   ├── Controllers/        # One controller per feature (orders, experts, companies...)
│   │   └── Dashboard/      # A controller per role's dashboard
│   ├── Middleware/         # Custom auth + role-checking middleware
│   └── Requests/           # Form validation
├── Models/                 # Eloquent models
database/
├── migrations/
└── seeders/                # Sample data if you want to try it out quickly
resources/views/            # Blade templates
routes/                     # Split by feature: orders.php, messages.php, dashboard.php, etc.
```

## Contributing

If you find a bug or have an idea for an improvement, feel free to open an issue or send a pull request. Nothing formal — just fork it, make your changes, and open a PR describing what you did.

## License

MIT — see the [LICENSE](LICENSE) file. Basically: use it, modify it, build on it, just keep the copyright notice.

## Questions?

Open an issue, or use the contact form on the site itself.
