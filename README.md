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
*   **Google Maps Integration:** As an alternative to OpenStreetMap, the system now natively supports Google Maps via an API Key configuration in the Settings module. When enabled, dispatch and trip views dynamically render interactive Google Maps with real-time Traffic Layer overlays, custom SVG markers, and rich POI styling directly into the CRM.
*   **Dynamic App Settings:** Company name, address, tax rate, and other variables are stored in `settings.json` and dynamically injected into all printable receipts, cheques, and statements.
*   **Driver Commission Rates:** Support for individualized driver commission percentages that automatically split trip fares into company profit and driver earnings.
*   **AJAX Filtering & Modals:** Dispatch board relies on fast AJAX requests for dispatching, driver assignment, rating submissions, and dispute reporting without full page reloads.

### 📱 Twilio SMS & Voice CRM Integration
We have successfully completed the mega update to convert the operational CRM core into a Twilio-powered SMS flow! The system acts as the "brain," routing interactions between Drivers and Customers entirely via text message.

#### 1. Twilio SDK & Environment Setup
We installed the official Twilio PHP SDK (`twilio/sdk: ^8.0`) so the application can communicate with Twilio APIs to send outbound messages. Placeholders for Twilio credentials (`TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_NUMBER`) were seamlessly integrated into the `.env` file and a new `Twilio` configuration class.

#### 2. The Twilio Webhook Receiver
A new CodeIgniter route `POST /api/webhooks/twilio/receive` was created. This endpoint maps to `TwilioWebhookController`. When any user sends a text to your Twilio number, Twilio instantly hits this public endpoint.
> [!NOTE] 
> The application uses CodeIgniter's `$routes->group()` with namespace routing under the `Dispatch` module for clean separation boundaries.

#### 3. The CRM Brain: `SmsLogicService`
This is the core state machine where the magic happens. Here is exactly how the logic flows when a text hits the system:

**Sender Identification & Context Matching**
The service extracts the 10-digit phone number and intelligently queries the `DriverModel` and `CustomerModel` to figure out who is texting. It then cross-references the `TripModel` to find any "active" rides associated with that user.

**Driver Logic Flow (Action Handling)**
If the system determines the text is from a **Driver**, it parses command keywords:
- `ACCEPT`: Searches for the oldest `pending` trip, assigns it to the driver, updates the status to `accepted`, and uses Twilio to text the Customer that their driver is on the way.
- `ARRIVED`: Changes status to `arrived` and texts the Customer that the driver is outside.
- `START`: Changes status to `started` and logs `started_at`.
- `DONE`: Changes status to `completed` and logs `completed_at`.
- *Proxy:* If the driver sends a regular message (not a command) while on a trip, the CRM masks their number and forwards the text to the Customer automatically.

**Customer Logic Flow**
If the texts comes from a registered **Customer**:
- If they have no active trip: The CRM creates a brand new `Trip` record in `pending` state, using the body of the text as the `pickup_address`.
- If they have an active trip: The CRM masks their number and forwards the message directly to the assigned Driver. 
- `CANCEL`: Instantly cancels the active ride and alerts the driver.

#### Phase 2: Programmable Voice & IVR Integration
We extended the CRM logic to support automated voice calls! Users can now call the system's Twilio number, hear an automated menu reflecting their trip status, and perform actions entirely over the phone.

##### 1. Inbound Call Routing
A new CodeIgniter route `POST /api/webhooks/twilio/voice` handles all incoming calls. When a call comes in, the system determines if the caller is a Driver or a Customer.

##### 2. The Interactive Voice Menus (`VoiceLogicService`)
Using Twilio's `<Gather>` TwiML verb, the system reads dynamic options using Text-to-Speech:
- **Drivers** have options to: Press `1` to arrive, `2` to start trip, `3` to complete trip, or `0` to call the Customer.
- **Customers** have options to: Press `1` to cancel their request, or `0` to call their Driver.
Each keypress triggers a separate webhook (`/api/webhooks/twilio/voice/gather-driver` or `gather-customer`) which instantly updates the database state just like the SMS flow or the web dashboard.

##### 3. Proxy Calling and Number Privacy
If either party presses `0`, the system automatically patches the call through to the other party's actual phone number using the TwiML `<Dial>` verb. The critical feature here is that **both parties will only see the main Twilio Business Number on their Caller ID**, keeping personal numbers completely hidden.

#### Code Architecture Overview
- [NEW] `app/Modules/Dispatch/Controllers/TwilioVoiceController.php` (Voice webhooks logic)
- [NEW] `app/Modules/Dispatch/Services/VoiceLogicService.php` (TwiML IVR & proxy dial logic)
- [NEW] `app/Modules/Dispatch/Controllers/TwilioWebhookController.php` (SMS webhooks logic)
- [NEW] `app/Modules/Dispatch/Services/SmsLogicService.php` (The Brain parser and dispatcher)
- [NEW] `app/Modules/Dispatch/Services/TwilioService.php` (Outbound SMS sender)
- [MODIFIED] `app/Modules/Dispatch/Config/Routes.php` (Created the webhook URL hooks)
- [MODIFIED] `.env` (Added placeholder credentials)
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
