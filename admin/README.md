# Kusikusi Admin Frontend (kusikusi-admin)

The generic Vue based front end for Kusikusi based projects

## Install the dependencies
```bash
npm install
```

### Start the app in development mode (hot-code reloading, error reporting, etc.)
```bash
quasar dev
```
By default, in dev mode, the admin will try to connect to the API in `http://127.0.0.1:8000/api` and get media from `http://127.0.0.1:8000` If you want to change these addresses, set the environmental variables `API_URL` and `MEDIA_URL`

### Lint the files
```bash
npm run lint
```

### Build the app for production
```bash
quasar build
```
By default, in production mode, the admin will try to connect to the API in `/api` and get media from `/` If you want to change these URLS, set the environmental variables `API_URL` and `MEDIA_URL`.

### Customize the configuration
See [Configuring quasar.conf.js](https://quasar.dev/quasar-cli/quasar-conf-js).
