Unofficial RSS Feeds for Stitcher Premium
===

Source code for the [Unofficial RSS Feeds for Stitcher Premium](https://stitcher-rss.128.io)
website.

Requirements
---

* MySQL >= 5.5
* PHP >= 7.1
* Composer
* NodeJS and Yarn

Installation
---

```sh
# Clone Repository
git clone https://gitlab.128.io/adduc/stitcher-rss.git
cd stitcher-rss

# Install PHP dependencies
composer install

# Install JS dependencies, compile CSS/JS assets
composer webpack

# Copy environment file and edit (for db credentials, cache, etc.)
cp .env.example .env
nano .env

# Create database
./artisan migrate
```

Updating
---

```sh
git pull
composer install
composer webpack
./artisan migrate
```

License
---

This project is licensed under the terms of the [MIT license](LICENSE.md).
