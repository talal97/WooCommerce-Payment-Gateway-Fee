# WooCommerce Payment Gateway Fee

A WooCommerce plugin that allows store administrators to add customizable fees to specific payment gateways. The plugin calculates fees based on the cart total, charging a configurable amount per 10 KD (Kuwaiti Dinar) block.

## Features

- Add extra fees to specific payment gateways
- Customizable fee amount per 10 KD in cart
- Configurable fee label at checkout
- Dynamic fee calculation based on cart total
- Automatic fee updates when payment method changes
- Easy-to-use admin interface

## Installation

1. Download the plugin zip file
2. Go to WordPress admin panel > Plugins > Add New
3. Click on "Upload Plugin" and choose the downloaded zip file
4. Click "Install Now"
5. After installation, click "Activate"

## Configuration

1. Navigate to WooCommerce > Gateway Fee in your WordPress admin panel
2. Configure the following settings:
   - **Fee Label**: Set the text that will appear for the fee at checkout
   - **Fee Amount**: Set the amount in KD to charge per 10 KD in cart
   - **Payment Gateways**: Select which payment gateways should include the extra fee
3. Click "Save Changes"

## How It Works

- The plugin calculates fees based on complete 10 KD blocks in the cart total
- For example, if the fee is set to 0.5 KD and the cart total is 25 KD:
  - Number of complete 10 KD blocks = 2
  - Total fee = 2 × 0.5 KD = 1 KD
- Fees are automatically updated when customers switch payment methods during checkout

## Requirements

- WordPress 5.0 or higher
- WooCommerce 3.0 or higher
- PHP 7.0 or higher

## Support

For support or feature requests, please contact the plugin author.

## Author

Talal Al-Ashab

## Version

1.3
