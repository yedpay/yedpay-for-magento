# Installation Guide on localhost

For easier setup, it is suggested to use bitnami. You may take the following website as reference.
https://hub.docker.com/r/bitnami/magento/

## 1. Creating a local directory and mounting bitnami/magento

You may wish to create a local directory at `Home`
For illustration, assume our username is `yedpayuser`

1.1 Open a folder with GUI or open the terminal and input

```bash
home/yedpayuser/magento
```

1.2 At the terminal, make a copy of the docker-compose.yml at `Home/yedpayuser/magento` with the following command:

```bash
curl -sSL https://raw.githubusercontent.com/bitnami/bitnami-docker-magento/master/docker-compose.yml > docker-compose.yml
```

The docker-compose.yml file should be at `Home/yedpayuser/magento/docker-compose.yml`

1.3 Create folders with the following command.
Warning: the command is case-sensitive:

```bash
mkdir -p code/Yedpay
```

This is where the Yedpay folder should locate: `home/yedpayuser/magento/code/Yedpay`

1.4 Get yedpay-magento-plugin at https://git.yedpay.com/Backend/yedpay-magento-plugin, its path should be `Home/yedpayuser/magento/code/Yedpay/yedpay-magento-plugin`. Alternatively, you can run:

```bash
git clone https://git.yedpay.com/Backend/yedpay-magento-plugin.git
```

1.5 Change the folder name `yedpay-magento-plugin` to `YedpayMagento`

1.6 Check `docker-compose.yml` and make sure the following content is correct:

```bash
magento:
   image: docker.io/bitnami/magento:2
   ports:
     - '8081:8080' // If 8081 port is in use on your computer, choose another port
     - '8443:8443'
```

```bash
  environment:
     - MAGENTO_EXTERNAL_HTTP_PORT_NUMBER=8081 // If you have not included this line, all website content will lose CSS and hyperlinks will err.
```

```bash
   volumes:
     - './MagentoMount:/bitnami/magento' //MagentoMount is the folder where bitnami will be mounted on and it's customizable.
```

1.7 Run `docker-compose up -d` on your terminal

1.8 Wait for a few minutes to allow the docker to run. Then, you should be able to access localhost:8081

## 2. Setting up Yedpay/YedpayMagento

2.1 Drag or input command to move `code/Yedpay/YedpayMagento` to `home/yedpayuser/magento/MagentoMount/bitnami/magento/app` folder. The path should look like this: `home/yedpayuser/magento/MagentoMount/app/code/Yedpay/YedpayMagento`

2.2 Check if registration.php matches with your folder name `Yedpay/YedpayMagento`:

```bash
<?php
\Magento\Framework\Component\ComponentRegistrar::register(
   \Magento\Framework\Component\ComponentRegistrar::MODULE,
   'Yedpay_YedpayMagento',
   __DIR__
);
```

2.3 Check if composer.json matches with your folder name `Yedpay/YedpayMagento`:

```bash
   "psr-4": {
     "Yedpay\\YedpayMagento\\": "",
```

2.4 Enter bitnami shell at VS Code left sidebar: Docker > magento > right click docker.io/bitnami/magento > Attach shell.
Inside the shell, run:
```bash
cd bitnami/magento
```

2.5 Run the following commands:

```bash
bin/magento cache:disable
bin/magento setup:upgrade
bin/magento cache:enable
```

Disabling cache is very important for `Yedpay_YedpayMagento` to work. Flushing or cleaning cache is not enough to do the work.

2.6 If you see:
   `An error has happened during application run. See exception log for details.`

Run `php bin/magento deploy:mode:set developer` to enable developer mode.

2.7 Go to localhost:8081/admin. You should be able to see Yedpay at the admin page at System > Configuration > Sales > Payment method.

2.8 In order for "Place order" to work, you must install yedpay/php-library in the magento folder **inside the docker**.

```bash
composer require yedpay/php-library
```

### Available services

[Magento admin page](localhost:8081/admin)

Credentials:

- username: user
- email: user@example.com
- password: bitnami1

### FAQ

1. I have something wrong with the `docker-compose.yml` and I have made some changes, but when I run `docker-compose down` and `docker-compose up -d`, they're not working properly. `docker.io/bitnami/magento:2` went on but went off very quickly.

- You need to delete the mounted folder in local directory. Also, open VSCode and remove all magento volumes (`magento_elasticsearch_data`, `magento_mariadb_data` and `magento_magento_data`). Double check if `docker-compose.yml` contains no error before you run `docker-compose up -d` again.

