# Rollpix Google One Tap Sign-in for Magento 2

## Overview
**Rollpix Google One Tap Sign-in** is a Magento 2 extension developed by **Anatolii Dolia**, designed to provide a seamless and frictionless authentication experience for e-commerce customers. By integrating Google's One Tap Sign-in, this extension eliminates the need for traditional logins, allowing users to authenticate with a single tap.

This module enhances the user experience, increases conversion rates, and reduces cart abandonment by removing login barriers.

Unlike other similar solutions, this extension is built from scratch with full customization capabilities, ensuring flexibility for store owners. Additionally, it includes the **google/apiclient** dependency, ensuring a secure and efficient connection to Google's authentication services.

---

## Why Choose Rollpix Google One Tap Sign-in for Magento 2?
### 🔹 Hassle-Free Authentication
Forget about long and frustrating login forms. With **Google One Tap**, your customers can sign in instantly with their Google accounts, increasing engagement and checkout speed.

### 🔹 Seamless Cross-Device Experience
This extension provides a consistent login experience across desktops, tablets, and mobile devices, making authentication effortless.

### 🔹 Reduce Login Abandonment
Customers often forget passwords or abandon the login process due to lengthy authentication steps. **One Tap Sign-in** removes these barriers, ensuring a higher login success rate.

### 🔹 Enhanced Security
This module supports **Google's secure authentication protocols**, helping protect user credentials from unauthorized access. The included **google/apiclient** library ensures safe token validation.

### 🔹 Easy Customization
Store admins have full control over authentication settings, UI design, and security options to match their brand and requirements.

---

## What Can Store Admins Do with This Extension?
🔧 **Authentication Settings:**
- Enable or disable **Google One Tap Sign-in**
- Configure security settings and authentication policies

🎨 **UI & Design Customization:**
- Adjust the appearance and position of the One Tap login prompt
- Match the One Tap UI with the store’s branding

👥 **User Management:**
- Automatically create new customer accounts
- Manage existing user accounts with seamless Google authentication
- Reset passwords and configure user roles

🔐 **Security Features:**
- Monitor login activity for failed and suspicious login attempts
- Prevent unauthorized access with Google's authentication security measures

---

## Features
✅ Enable or disable the extension from the backend<br>
✅ Enter **Google Client ID** obtained during registration<br>
✅ Auto-sign-in without requiring users to click the login prompt<br>
✅ Choose the **prompt position** from the admin panel<br>
✅ Fully optimized for **mobile and desktop users**<br>
✅ Secure authentication with **google/apiclient**<br>

---

## Benefits
💡 **Improved User Experience** – Faster login with minimal effort, increasing customer satisfaction.<br>
💳 **Reduced Cart Abandonment** – A seamless login process leads to higher conversions.<br>
📱 **Mobile-Friendly** – Optimized for all devices, ensuring a smooth shopping experience.<br>
🔒 **Enhanced Security** – Integrates Google's **secure authentication API** for maximum data protection.<br>

---

## Technical Details
**Module Name:** `rollpix/google-one-tap`
**Magento Version:** Magento 2.x
**PHP Compatibility:** `^8.1.0 || ^8.2.0`
**Required Dependency:** `google/apiclient` (version `^2.15.0`)

---

## Installation
```sh
composer require rollpix/google-one-tap:dev-main
bin/magento module:enable Rollpix_GoogleOneTap
bin/magento setup:upgrade
bin/magento cache:flush
```

For more details, visit the [GitHub repository](https://github.com/ROLLPIX/M2-google-one-tap).

---

This **Magento 2 extension** was built with performance, security, and user experience in mind.<br> 
If you’re looking for a **reliable Google One Tap Sign-in solution**, this is the module for you! 🚀

<h3>Enjoy!</h3>
<h6>Magento 2.4.6 - 2.4.8</h6>
