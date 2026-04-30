# Acura

A proof of concept ERP metrology app, it uses Laravel 12, Inertia.js 3.0,
Vue 3 and Weasyprint.

To test it, clone the repo then configure weasyprint:

```sh
python -m venv weasyprint
source weasyprint/bin/activate
pip install weasyprint
```

Then in the `.env` configure

```env
WEASYPRINT_BINARY=path/to/weasyprint
```

Finally run migrations and seed the database

```sh
php artisan migrate
php artisan db:seed
```

The test user is ADMIN with password admin123@

## Disclaimer
The app is in alpha state and not ready for production, at this point is for
educational use only
