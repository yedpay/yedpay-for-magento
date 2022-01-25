# Yedpay for Magento Extension

## Description

Yedpay for Magento allows merchants to use Alipay, WeChat Pay, UnionPay, Visa and Mastercard payment on your E-commerce store in a safe way.

### Install from zip

1. Download yedpay-for-magento extension.
2. Navigate to your Magento store root folder.
3. Create `path_to_your_Magento_directory/app/code/Yedpay/YedpayMagento`.
4. Extract the module content to `path_to_your_Magento_directory/app/code/Yedpay/YedpayMagento`.
5. Install Yedpay PHP Library.
`composer require yedpay/php-library`
6. Set up Yedpay for Magento Module.
```
php bin/Magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```
7. If you are running Yedpay for Magento on production, run `php bin/magento setup:static-content:deploy`
8. Log out from Magento admin panel and login again. You will see Yedpay at *"Stores"* > *"Configuration"* > *"Sales"* > *"Payment Methods"* > *"Other Payment Methods"*.

### Install from composer
Follow the below instructions once you have chosen Yedpay for Magento from Magento Marketplace.

1. Log in to Magento Marketplace.
2. Select "My Profile" at the top right hand corner of the page.
3. Navigate to "My Purchases" under "My Products".
4. Copy the component name and version number to form a command as follows:
```
composer require yedpay/yedpaymagento:1.0.0
```
5. Enter access keys generated from Magento Marketplace.
6. Set up Yedpay for Magento Module.
```
php bin/Magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```
7. If you are running Yedpay for Magento on production, run `php bin/magento setup:static-content:deploy`
8. Log out from Magento admin panel and login again. You will see Yedpay at *"Stores"* > *"Configuration"* > *"Sales"* > *"Payment Methods"* > *"Other Payment Methods"*.

### Configuration

1. In Magento Admin Page, navigate to *"Configuration"* (Stores > Configuration).
2. Select *"Sales"*, then *"Payment Methods"*.
3. At *"Other Payment Methods"*, locate Yedpay and select "Yes" for *Enabled*.
4. Enter "Sign Key" and "API Key". *(Refer to [Key Materials](#key-materials) to obtain Sign Key and API Key)*
5. For "Environment", select "Staging" for testing, "Production" for production.
6. Click the "Save Config" button.


### Key Materials

#### Sign Key

1. Log into [Yedpay's Merchant Portal](https://merchant.yedpay.com) as owner.
2. Navigate to "App Keys" *(Admin > App Keys)*.
3. If Sign Key is absent, click the "Generate" button.
4. Copy the "Sign Key" shown.

#### API Key

1. Log into [Yedpay's Merchant Portal](https://merchant.yedpay.com) as owner.
2. Navigate to "App Keys" *(Admin > App Keys)*.
3. In the "API Keys" section, click the "Add" button.
4. Enter "Key Name" and select your online store, then click the "Add" button again to get a new API Key.
5. Copy the "API Key" shown. (The API Key will only be displayed once, save it immediately!)
