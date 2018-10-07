Shepherd Tour Guide
===================


#### Contents
*   [Overview](#over)
*   [Installation](#install)
*   [Tests](#tests)
*   [Contributors](#contrib)
*   [License](#lic)


## <a name="over"></a>Overview

A module that adds a tour guide feature to Magento 2.  
The module is powered by [Hubspot Shepherd](https://github.com/shipshapecode/shepherd).

It supports Magento 2 admin panel only.

## <a name="install"></a>Installation

Below, you can find two ways to install the shepherd module.

### 1. Install via Composer (Recommended)
First, make sure that Composer is installed: https://getcomposer.org/doc/00-intro.md

Make sure that Packagist repository is not disabled.

Run Composer require to install the module:

    php <your Composer install dir>/composer.phar require shopgo/shepherd:*

### 2. Clone the shepherd repository
Clone the <a href="https://github.com/shopgo-magento2/shepherd" target="_blank">shepherd</a> repository using either the HTTPS or SSH protocols.

### 2.1. Copy the code
Create a directory for the shepherd module and copy the cloned repository contents to it:

    mkdir -p <your Magento install dir>/app/code/ShopGo/Shepherd
    cp -R <shepherd clone dir>/* <your Magento install dir>/app/code/ShopGo/Shepherd

### Update the Magento database and schema
If you added the module to an existing Magento installation, run the following command:

    php <your Magento install dir>/bin/magento setup:upgrade

### Verify the module is installed and enabled
Enter the following command:

    php <your Magento install dir>/bin/magento module:status

The following confirms you installed the module correctly, and that it's enabled:

    example
        List of enabled modules:
        ...
        ShopGo_Shepherd
        ...

## <a name="tests"></a>Tests

TODO

## <a name="contrib"></a>Contributors

Ammar (<ammar@shopgo.me>)

## <a name="lic"></a>License

[Open Source License](LICENSE.txt)
