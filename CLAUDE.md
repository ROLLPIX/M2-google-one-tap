# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Rollpix Google One Tap Sign-in** is a Magento 2 extension that integrates Google's One Tap authentication to provide seamless, frictionless customer login.

- **Module Name**: `Rollpix_GoogleOneTap`
- **Namespace**: `Rollpix\GoogleOneTap`
- **Magento Compatibility**: 2.4.6 - 2.4.8
- **PHP Requirements**: ^8.1.0 || ^8.2.0
- **Key Dependency**: `google/apiclient` (^2.15.0) for secure token verification

## Development Commands

### Installation & Setup
```bash
# Install module dependencies
composer install

# Enable module
bin/magento module:enable Rollpix_GoogleOneTap

# Run setup upgrade
bin/magento setup:upgrade

# Deploy static content (production mode)
bin/magento setup:static-content:deploy

# Clear cache
bin/magento cache:flush
```

### Configuration
Admin configuration is located at:
**Stores > Configuration > Rollpix Extensions > One Tap Login**

Required configuration:
- **Module Status**: Enable/disable the extension
- **Client ID**: Google OAuth Client ID (encrypted in database)
- **Auto Sign-in**: Auto-authenticate without clicking prompt
- **Position**: Display position of the One Tap prompt
- **Background Click**: Close prompt on background click behavior

## Architecture

### Authentication Flow

1. **Frontend Display** ([view/frontend/templates/onetap.phtml](view/frontend/templates/onetap.phtml)):
   - Renders only for non-logged-in customers when module is enabled
   - Loads Google GSI client script from `accounts.google.com`
   - Configures prompt with client ID, callback, and UI preferences

2. **User Interaction**:
   - Google One Tap prompt appears at configured position
   - User selects Google account (or auto-signs in if enabled)
   - Google returns JWT credential token

3. **Token Verification** ([Controller/Checkout/Response.php](Controller/Checkout/Response.php)):
   - JavaScript callback sends `id_token` via POST to `onetaplogin/checkout/response`
   - Controller uses `Google\Client` to verify token authenticity
   - Validates token audience matches configured Client ID
   - Extracts user email and name from payload

4. **Customer Management**:
   - Loads customer by email from current website
   - Creates new customer account if not exists (firstname, lastname, email)
   - Logs in customer using `Session::setCustomerAsLoggedIn()`
   - Returns JSON response with success/failure status

5. **Frontend Completion**:
   - Reloads customer data and cart sections
   - Refreshes page to reflect logged-in state

### Key Components

**Block Layer**:
- [Block/OneTap.php](Block/OneTap.php): Template block that provides configuration data and customer login status to template

**Controller Layer**:
- [Controller/Checkout/Response.php](Controller/Checkout/Response.php): Implements `CsrfAwareActionInterface` to handle Google callback without CSRF validation (required for external POST requests)

**Model Layer**:
- [Model/Config/Data.php](Model/Config/Data.php): Configuration provider implementing `DataInterface`
- [Model/API/DataInterface.php](Model/API/DataInterface.php): Defines XML config path constants
- [Model/Config/Source/Position.php](Model/Config/Source/Position.php): Source model for prompt position dropdown

**View Layer**:
- [view/frontend/layout/default.xml](view/frontend/layout/default.xml): Adds Google GSI script to head, renders OneTap block in content container
- [view/frontend/layout/customer_account_logoutsuccess.xml](view/frontend/layout/customer_account_logoutsuccess.xml): Layout for logout success page
- [view/frontend/templates/onetap.phtml](view/frontend/templates/onetap.phtml): Template rendering Google One Tap UI with JavaScript callback

**Configuration**:
- [etc/module.xml](etc/module.xml): Module registration
- [etc/adminhtml/system.xml](etc/adminhtml/system.xml): Admin configuration fields
- [etc/frontend/routes.xml](etc/frontend/routes.xml): Defines `onetaplogin` route prefix
- [etc/acl.xml](etc/acl.xml): Admin ACL resources for module configuration
- [etc/csp_whitelist.xml](etc/csp_whitelist.xml): Whitelists `accounts.google.com` for script-src and style-src CSP policies

### Configuration Paths

All configuration values use website scope:
- `googleonetap/module_status/status` - Enable/disable module
- `googleonetap/general/client_id` - Google OAuth Client ID (encrypted)
- `googleonetap/general/background_click` - Close on background click
- `googleonetap/general/auto_signin` - Auto sign-in setting
- `googleonetap/general/position` - Prompt position class

### Security Considerations

1. **Token Verification**: All Google tokens are verified server-side using `google/apiclient` library before authentication
2. **CSRF Exemption**: Response controller implements `CsrfAwareActionInterface` and exempts CSRF validation (necessary for Google's POST callback)
3. **Client ID Validation**: Token audience (`aud`) must match configured Client ID
4. **Encrypted Storage**: Google Client ID is encrypted using Magento's encryption system
5. **CSP Compliance**: Content Security Policy whitelist allows Google accounts domain

## File Structure Pattern

Standard Magento 2 module structure:
- `Block/` - View-related business logic
- `Controller/Checkout/` - Frontend controllers (route: `onetaplogin/checkout/*`)
- `Model/` - Data models and business logic
- `etc/` - Module configuration (module.xml, acl.xml, csp_whitelist.xml)
- `etc/adminhtml/` - Admin-specific configuration (system.xml)
- `etc/frontend/` - Frontend-specific configuration (routes.xml)
- `view/frontend/layout/` - Layout XML files
- `view/frontend/templates/` - PHTML templates
- `registration.php` - Module registration entry point
- `i18n/` - Translation files (Spanish supported: es_ES, es_MX, es_AR)

## Translations

The module includes Spanish translations for all user-facing messages:

**Available Languages:**
- `i18n/es_ES.csv` - Spanish (Spain)
- `i18n/es_MX.csv` - Spanish (Mexico)
- `i18n/es_AR.csv` - Spanish (Argentina)

**Translated Elements:**
- Admin configuration labels and fields
- Error messages (missing token, invalid email, etc.)
- Position options (Top Right, Top Left, Bottom Right, Bottom Left)
- System messages

**Adding New Translations:**
1. Create new CSV file in `i18n/` directory (e.g., `i18n/fr_FR.csv`)
2. Use format: `"English text","Translated text"`
3. Include all strings from existing translation files
4. After adding translations, clear cache: `bin/magento cache:flush`

**Key Translated Messages:**
- Authentication errors: Token validation, email verification
- Configuration validation: Missing Client ID
- Admin interface: All labels and descriptions

## Adding Features

When extending this module:

1. **New Configuration Fields**: Add to [etc/adminhtml/system.xml](etc/adminhtml/system.xml), define constants in `Model/API/DataInterface.php`, add getter methods in `Model/Config/Data.php`

2. **Template Changes**: Modify [view/frontend/templates/onetap.phtml](view/frontend/templates/onetap.phtml) and ensure proper escaping (`$escaper->escapeHtml()`, `$escaper->escapeUrl()`)

3. **Authentication Logic**: Update [Controller/Checkout/Response.php](Controller/Checkout/Response.php) - remember this controller bypasses CSRF for Google callbacks

4. **New External Scripts**: Update [etc/csp_whitelist.xml](etc/csp_whitelist.xml) with appropriate CSP policies

5. **Block Methods**: Add to [Block/OneTap.php](Block/OneTap.php) to expose data to templates

## Google API Integration

The module uses Google's One Tap JavaScript library and backend verification:
- **Frontend**: `https://accounts.google.com/gsi/client` (loaded in layout XML)
- **Backend**: `google/apiclient` composer package for JWT token verification
- **API Client**: Instantiated with Client ID: `new Google_Client(['client_id' => $googleOauthClientId])`
- **Token Verification**: `$client->verifyIdToken($idToken)` returns decoded payload or false

## Testing After Changes

1. Clear cache: `bin/magento cache:flush`
2. Verify admin configuration loads correctly
3. Test on frontend as non-logged-in user
4. Verify Google One Tap prompt appears
5. Test authentication flow end-to-end
6. Check new customer creation and existing customer login
7. Verify customer data reload after authentication
