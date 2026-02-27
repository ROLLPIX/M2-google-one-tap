# Rollpix Google One Tap Sign-in for Magento 2

**Sponsor: [www.rollpix.com](https://www.rollpix.com)**

> **[Leer en Español](README_es.md)**

---

## Overview

**Rollpix Google One Tap Sign-in** is a Magento 2 extension designed to provide a frictionless authentication experience for e-commerce customers. By integrating Google One Tap Sign-in and a configurable Google Sign-In Button, this extension eliminates the need for traditional logins, allowing users to authenticate with a single click.

This module improves user experience, increases conversion rates and reduces cart abandonment by removing login barriers.

Unlike other similar solutions, this extension is built from scratch with full customization capabilities, ensuring flexibility for store owners. It includes the **google/apiclient** dependency for secure server-side token verification.

---

## Table of Contents

- [Why Choose Rollpix Google One Tap?](#why-choose-rollpix-google-one-tap)
- [Features](#features)
- [Technical Requirements](#technical-requirements)
- [Installation](#installation)
- [Google Cloud Console Setup](#google-cloud-console-setup)
- [Module Configuration](#module-configuration)
- [Configuration Options](#configuration-options)
- [Troubleshooting](#troubleshooting)
- [Changelog](#changelog)
- [License](#license)

---

## Why Choose Rollpix Google One Tap?

### Frictionless Authentication
Forget long, frustrating login forms. With **Google One Tap**, your customers can sign in instantly with their Google accounts, increasing engagement and checkout speed.

### Seamless Experience Across Devices
This extension provides a consistent login experience on desktop, tablets and mobile devices, making authentication effortless.

### Reduce Login Abandonment
Customers often forget passwords or abandon the login process due to long authentication steps. **One Tap Sign-in** eliminates these barriers, ensuring a higher login success rate.

### Enhanced Security
This module supports **Google's secure authentication protocols**, helping protect user credentials from unauthorized access. The included **google/apiclient** library ensures secure token validation.

### Full Customization
Store administrators have full control over authentication settings, UI design, button appearance, and page-specific positioning to match their brand and requirements.

---

## Features

- Enable or disable the extension from the backend
- **Google One Tap Popup** with configurable position, auto sign-in, context, ITP support, and dismiss callback
- **Google Sign-In Button** on login, registration, checkout, and custom CSS selector pages
- **Card-style button** on login and checkout pages with title and styled container
- **Side column card** on registration page (next to the form)
- **Configurable button position** per page (above/below for login, above/below/side for registration, above/below for checkout)
- **Button appearance customization**: theme, size, shape, text, and logo alignment
- Secure authentication with **google/apiclient** server-side token verification
- Automatic customer account creation for new users
- Rate limiting to protect against brute force attacks
- Debug logging for troubleshooting
- **Amasty Checkout** compatibility (One Tap + optional button disable when Amasty Social Login is active)
- Spanish translations (Spain, Mexico, Argentina)
- **Module Information** section in admin showing version, module name, and repository URL
- CSP (Content Security Policy) compliant

---

## Technical Requirements

| Requirement | Version |
|---|---|
| **Module Name** | `rollpix/google-one-tap` |
| **Magento** | 2.4.6 - 2.4.8 |
| **PHP** | ^8.1.0 \|\| ^8.2.0 |
| **Dependency** | `google/apiclient` ^2.15.0 |

---

## Installation

### Step 1: Configure Repository Access

```bash
# Option 1: Private Composer repository
composer config --auth http-basic.repo.rollpix.com [username] [password]

# Option 2: Private GitHub repository
composer config --global github-oauth.github.com [your-personal-access-token]
```

### Step 2: Add the Repository

```bash
# Private Composer repository
composer config repositories.rollpix-google-one-tap composer https://repo.rollpix.com/

# Or GitHub repository
composer config repositories.rollpix-google-one-tap vcs https://github.com/ROLLPIX/M2-google-one-tap
```

### Step 3: Install the Module

```bash
composer require rollpix/google-one-tap:^2.0
bin/magento module:enable Rollpix_GoogleOneTap
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Step 4: Verify Installation

```bash
bin/magento module:status Rollpix_GoogleOneTap
```

---

## Google Cloud Console Setup

### Step 1: Create a Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Click **"Select a project"** at the top
3. Click **"New Project"**, enter a name, and click **"Create"**

### Step 2: Enable Google Identity API

1. Go to **APIs & Services > Library**
2. Search for **"Google Identity Services"**
3. Click **"Enable"**

### Step 3: Configure OAuth Consent Screen

1. Go to **APIs & Services > OAuth consent screen**
2. Select **"External"** as user type
3. Fill in: App name, support email, authorized domains, developer contact
4. Click through the remaining steps and **"Back to Dashboard"**

### Step 4: Create OAuth 2.0 Credentials

1. Go to **APIs & Services > Credentials**
2. Click **"Create Credentials" > "OAuth 2.0 Client ID"**
3. Select **"Web application"**
4. Configure:
   - **Authorized JavaScript origins**: `https://yourdomain.com` (and `https://www.yourdomain.com`)
   - **Authorized redirect URIs**: `https://yourdomain.com/customer/account`
5. Click **"Create"**

### Step 5: Copy the Client ID

Copy the **Client ID** (format: `123456789-abc123def456.apps.googleusercontent.com`). You do NOT need the Client Secret for One Tap.

> **Important:** Use HTTPS in production. Add all domains where you want to use One Tap (www and non-www).

---

## Module Configuration

### Access Configuration

**Stores > Configuration > Rollpix > One Tap Login**

---

## Configuration Options

### 1. General

| Field | Type | Description |
|---|---|---|
| **Module Status** | Enable/Disable | Activates or deactivates the entire module |

### 2. One Tap Popup Configuration

| Field | Type | Description |
|---|---|---|
| **Client ID** | Encrypted text | Google OAuth Client ID from Cloud Console |
| **Close Prompt on Background Click** | Yes/No | Close the prompt when clicking outside |
| **Auto Sign in** | Yes/No | Auto-authenticate without clicking the prompt |
| **Position** | Select | Prompt position: Top Right, Top Left, Bottom Right, Bottom Left |
| **Prompt Context** | Select | Text in prompt: "Sign In" or "Sign Up" |
| **Prompt Parent Element ID** | Text | Optional DOM element ID to anchor the popup |
| **ITP Support (Safari)** | Yes/No | Intelligent Tracking Prevention support |
| **Show Message on Dismiss** | Yes/No | Show notification when user dismisses the prompt |

### 3. Google Sign-In Button

| Field | Type | Description |
|---|---|---|
| **Enable Button** | Enable/Disable | Show a Google Sign-In button on selected pages |
| **Show Button On** | Multiselect | Pages: Login, Registration, Checkout, Custom CSS Selector |
| **Custom CSS Selector** | Text | CSS selector for custom button placement |
| **Login Page Position** | Select | Above Login Form / Below Login Form |
| **Registration Page Position** | Select | Above Registration Form / Below Registration Form / Side Column (Next to Form) |
| **Checkout Page Position** | Select | Above Checkout Form / Below Email Section |
| **Button Theme** | Select | Outline, Filled Blue, Filled Black |
| **Button Size** | Select | Large, Medium, Small |
| **Button Shape** | Select | Rectangular, Pill, Circle, Square |
| **Button Text** | Select | Sign in with Google, Sign up with Google, Continue with Google, Signin |
| **Logo Alignment** | Select | Left, Center |

### 4. Security Settings

| Field | Type | Description |
|---|---|---|
| **Enable Debug Logging** | Yes/No | Log detailed info for troubleshooting (never use in production) |
| **Enable Rate Limiting** | Yes/No | Limit authentication attempts per IP |
| **Maximum Attempts** | Number | Max attempts within time window (default: 10) |
| **Time Window** | Number | Time window in seconds (default: 60) |

### 5. Compatibility

| Field | Type | Description |
|---|---|---|
| **Disable Button when Amasty Social Login is active** | Yes/No | Avoid duplicate Google buttons when Amasty Social Login is installed |

### 6. Module Information

Displays module name, current version (read from `composer.json`), and repository URL.

---

## Button Display Styles

### Login & Checkout Pages
The Google Sign-In button is displayed inside a **styled card** with a title ("Quick sign in with your Google account") and the button centered. The card provides clean visual separation from the surrounding form.

### Registration Page
- **Above/Below**: The button appears with an "Or" divider line separating it from the form.
- **Side Column**: A styled card is placed to the right of the registration form with a title ("Quick sign up with your Google account") and the button inside. On mobile, it stacks below the form.

---

## Troubleshooting

### The prompt does not appear on the frontend

1. Verify the module is **enabled** in configuration
2. Verify the **Client ID** is correctly configured
3. Clear cache: `bin/magento cache:flush`
4. Verify you are not logged in (prompt only appears for non-authenticated users)
5. Verify your domain is in the **authorized origins** in Google Cloud Console
6. Open browser console (F12) and check for JavaScript errors

### Error "Invalid Client ID"

1. Verify the Client ID is correct (no extra spaces)
2. Verify the current domain is in the **authorized origins** in Google Cloud Console
3. Clear Magento cache after changing the Client ID

### The prompt appears but does not sign in

1. Check browser console (F12) for errors
2. Check Magento logs: `var/log/system.log` and `var/log/exception.log`
3. Verify the Google account has a verified email
4. Verify the domain uses **HTTPS** in production

---

## Changelog

### v2.1.3
- Card-style button for login and checkout pages (styled container with title)
- Removed broken "Left/Right of Login Button" checkout positions
- Added "Above Checkout Form" checkout position
- Fixed registration "above" divider order via JS DOM swap

### v2.1.2
- Fix button positioning: divider order, side column inline styles, checkout selectors

### v2.1.1
- Module Information section in admin (name, version, repository URL)

### v2.1.0
- Configurable button position per page (login, register, checkout)

### v2.0.0
- Google Sign-In Button on login, registration, checkout, and custom pages
- Enhanced One Tap configuration (context, ITP support, prompt parent, dismiss callback)
- Button appearance customization (theme, size, shape, text, logo alignment)
- Amasty Checkout and Amasty Social Login compatibility
- Rate limiting and debug logging

### v1.x
- Google One Tap popup with configurable position and auto sign-in
- Secure server-side token verification with google/apiclient
- Automatic customer account creation
- Spanish translations

---

## Security Features

- **Token Verification**: All Google tokens are verified server-side using `google/apiclient`
- **Verified Email**: Only Google-verified emails are accepted
- **Encrypted Client ID**: Stored encrypted in the database using Magento's encryption
- **Rate Limiting**: Configurable limits to prevent brute force attacks
- **CSP Compliant**: Whitelists `accounts.google.com` for script-src and style-src
- **Error Logging**: All errors are logged for auditing

---

## Support

- **GitHub Issues**: https://github.com/ROLLPIX/M2-google-one-tap/issues
- **Repository**: https://github.com/ROLLPIX/M2-google-one-tap
- **Website**: [www.rollpix.com](https://www.rollpix.com)

---

## License

- **OSL-3.0** (Open Software License 3.0)
- **AFL-3.0** (Academic Free License 3.0)

---

Built with performance, security and user experience in mind.
Magento 2.4.6 - 2.4.8
