# RideApp - Fleet & Dispatch Management System

## Recently Developed Features (Feb 2026)

### 🗺️ Advanced Dispatch & Trip Management
*   **Quick Dispatch with Autocomplete:** A unified modal for admins to rapidly create new trips. Features real-time address autosuggestion powered by Nominatim (OpenStreetMap) and instantly auto-calculates distance, duration, and fare based on vehicle type.
*   **Dynamic Saved Addresses:** When a customer is selected during dispatch, their saved profile addresses dynamically appear as clickable suggestion "chips" that auto-fill the pickup/dropoff fields and automatically calculate the fare.
*   **Trip Dispute System:** Dedicated dispute management with categorization (Fare Issue, Driver Behavior, Lost Item, etc.).
*   **Return/Resolution Trips:** Directly from the dispute dashboard, admins can dispatch a linked "Return Trip" or "Resolution Trip". This uses Mapbox/Nominatim autocomplete and auto-assigns the original driver to easily handle lost items or route redos.

### 💰 Wallet & Financial System
*   **Customer & Driver Wallets:** Independent digital wallets for users. 
*   **Dynamic Balance Calculation:** Wallets accurately reflect real-time balances generated on-the-fly (`WalletService`) from all historical ledger transactions (deposits, payouts, trip deductions, earnings) rather than relying on static database columns.
*   **Printable Wallet Statements:** Standalone, print-ready HTML views for both Customer and Driver financial statements. Includes beautiful headers, summary boxes, and running transaction balances row-by-row.
*   **CSV Statement Exports:** One-click CSV exports of complete wallet ledgers with proper formatting, headers, and running balances for Excel/accounting software.
*   **Printable Dispatch Receipts:** Fully formatted, print-ready trip receipts outlining pickup, dropoff, fare breakdown, and company branding.
*   **Driver Bank Cheque Generation:** Print-ready, MICR-styled physical cheque generation for driver wallet payouts.

### ⚙️ System & Admin Enhancements
*   **Dynamic App Settings:** Company name, address, tax rate, and other variables are stored in `settings.json` and dynamically injected into all printable receipts, cheques, and statements.
*   **Driver Commission Rates:** Support for individualized driver commission percentages that automatically split trip fares into company profit and driver earnings.
*   **AJAX Filtering & Modals:** Dispatch board relies on fast AJAX requests for dispatching, driver assignment, rating submissions, and dispute reporting without full page reloads.

---

## CodeIgniter 4 Application Starter
## CodeIgniter 4 Application Starter

### What is CodeIgniter?

CodeIgniter is a PHP full-stack web framework that is light, fast, flexible and secure.
More information can be found at the [official site](https://codeigniter.com).

This repository holds a composer-installable app starter.
It has been built from the
[development repository](https://github.com/codeigniter4/CodeIgniter4).

More information about the plans for version 4 can be found in [CodeIgniter 4](https://forum.codeigniter.com/forumdisplay.php?fid=28) on the forums.

You can read the [user guide](https://codeigniter.com/user_guide/)
corresponding to the latest version of the framework.

### Installation & updates

`composer create-project codeigniter4/appstarter` then `composer update` whenever
there is a new release of the framework.

When updating, check the release notes to see if there are any changes you might need to apply
to your `app` folder. The affected files can be copied or merged from
`vendor/codeigniter4/framework/app`.

### Setup

Copy `env` to `.env` and tailor for your app, specifically the baseURL
and any database settings.

### Important Change with index.php

`index.php` is no longer in the root of the project! It has been moved inside the *public* folder,
for better security and separation of components.

This means that you should configure your web server to "point" to your project's *public* folder, and
not to the project root. A better practice would be to configure a virtual host to point there. A poor practice would be to point your web server to the project root and expect to enter *public/...*, as the rest of your logic and the
framework are exposed.

**Please** read the user guide for a better explanation of how CI4 works!

### Repository Management

We use GitHub issues, in our main repository, to track **BUGS** and to track approved **DEVELOPMENT** work packages.
We use our [forum](http://forum.codeigniter.com) to provide SUPPORT and to discuss
FEATURE REQUESTS.

This repository is a "distribution" one, built by our release preparation script.
Problems with it can be raised on our forum, or as issues in the main repository.

### Server Requirements

PHP version 8.2 or higher is required, with the following extensions installed:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

> [!WARNING]
> - The end of life date for PHP 7.4 was November 28, 2022.
> - The end of life date for PHP 8.0 was November 26, 2023.
> - The end of life date for PHP 8.1 was December 31, 2025.
> - If you are still using below PHP 8.2, you should upgrade immediately.
> - The end of life date for PHP 8.2 will be December 31, 2026.

Additionally, make sure that the following extensions are enabled in your PHP:

- json (enabled by default - don't turn it off)
- [mysqlnd](http://php.net/manual/en/mysqlnd.install.php) if you plan to use MySQL
- [libcurl](http://php.net/manual/en/curl.requirements.php) if you plan to use the HTTP\CURLRequest library
