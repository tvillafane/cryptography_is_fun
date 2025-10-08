# Laravel Key Ownership Demo

DESCRIPTION

---

## Requirements
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Composer](https://getcomposer.org/)

---

## Full Setup with Laravel Sail (recommended)

```bash
git clone https://github.com/tvillafane/cryptography_is_fun
cd cryptography_is_fun
composer install

cp .env.example .env

./vendor/bin/sail up -d
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan test
```

### when you're ready to remove the project:

```bash
./vendor/bin/sail down -v
cd ..
rm -rf cryptography_is_fun
```