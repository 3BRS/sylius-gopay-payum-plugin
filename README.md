# Sylius GoPay payment gateway plugin  
<div align="center">
    <a href="https://www.gopay.com" title="GoPay" target="_blank"><img src="https://dl.dropboxusercontent.com/s/af8fiebcqmk9wgm/GoPay-logo-varianta-A-PANTONE.png" width="300" /></a>
</div>

## Installation
Until pull request is merged, require it this way:
```bash
composer require 3brs/gopay-payum-plugin
```

Add plugin dependencies to your bundles.php file:

```php
ThreeBRS\GoPayPaymPlugin\GoPayPayumPlugin::class => ['all' => true]
```

## Usage
Add your test credentials in Sylius admin as new payment method. Complete couple of orders with different states and send email to GoPay authorities. 
After the review you will get production credentials, so just change it in Sylius admin, and you are ready to go. 

## Credits

Built on top of https://github.com/Prometee/gopay-plugin, which is fork of https://github.com/bratiask/gopay-plugin, which is fork of https://github.com/czende/gopay-plugin.

Thank you all for your work!
