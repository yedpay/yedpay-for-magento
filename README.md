# Yedpay for Magento Extension

## Description

Yedpay for Magento allows merchants to use Alipay, WeChat Pay, UnionPay, Visa and Mastercard payment on your E-commerce store in a safe way.

## Country Restriction

This plugin is built for Merchants located in Hong Kong. The settlement currency is HKD. 
The bank account needs to be a Hong Kong Bank Account. 

## Features and Benefits

* With Yedpay plugin, transactions can be made through Alipay CN, Alipay HK, WeChat Pay, UnionPay, Visa and Mastercard. No need to download and install multiple separate plugins.
* The only plugin of this kind to offer mobile-friendly checkout experience, enabling WeChat, Alipay and UnionPay APP redirection.
* Extremely user-friendly checkout experience to increase conversion rate.
* Fast deposit: receive money in your account in two business days.
* Trusted payment experience with several layers of security protection.
* Benefit from Yedpay's in-store payment solutions.

## Clear Pricing

There is no set up fee, no monthly fee, no other hidden costs. Merchants pay a flat fee corresponds to available gateways. Custom rates are available for businesses with very large volume. View pricing on https://www.yedpay.com/en/pricing/.

## How Does The Check Out Look Like?

* At the check out page, choose Alipay CN, Alipay HK, WeChat Pay or UnionPay.
* With a PC browser, a page with the QR code will open.
* Users can scan the QR code with the Alipay, WeChat or UnionPay App and confirm the payment on the phone.
* With the WeChat or Mobile browser, clicking check out will automatically wakes up the WeChat app, the Alipay app or the UnionPay app. Users confirms the payment on the phone.

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

## Frequently Asked Questions

### Could the customers shop outside Hong Kong?
* Yes, E-Commerce is not geographically bounded. Note that transactions can only be settled in HKD.

### What is the rate/fee for using Yedpay's gateway?
* There is no set up fee and such, only transaction fee applies. For details, please visit https://www.yedpay.com/en/pricing/

### Will I be charged a fee when issuing refund?
* The refund period is 90 days. If you issue a full refund of Union Pay, Alipay or WechatPay, the original transaction fee will be refunded to you and your customer will receive a full refund. For Visa and Mastercard, a full transaction amount will be refunded to the merchant and the customers only if the refund is made on the same day of the transaction. Otherwise, the transaction fee will be deducted after the day, but the customer will still get the full refund.

### Where do I issue refund?
* Please visit and issue the refund on Yedpay merchant portal: https://merchant.yedpay.com/login

### Is there a number I can reach if I have any question?
* Please contact our customer service. Reach us at +852 3690 8216, Whatsapp +852 5977 0850 or email : [cs@yedpay.com](mailto:cs@yedpay.com)

### When will I receive the money in my bank?
* You will receive the payment in 2-4 business days after the purchase was done.
