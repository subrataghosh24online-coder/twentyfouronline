# Environment Variables

twentyfouronline allows certain settings to be set via the environment or
through the .env file.

## Database

Set the variables to connect to the database.  The default values are shown below.

```dotenv
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=twentyfouronline
DB_USERNAME=twentyfouronline
DB_PASSWORD=
DB_SOCKET=
```

## Trusted Reverse Proxies

A comma separated list of trusted reverse proxy IPs or CIDR.

For legacy reasons the default is `'*'`, which means any proxy is allowed.
`'**'` means trust any proxy up the chain.

```dotenv
APP_TRUSTED_PROXIES=192.168.1.0/24,192.167.8.20
```

## Base url

Set the base url for generated urls.

This will be needed when using signed graph urls for alerting. It may
be needed when using reverse proxies combined with a subdirectory.

Generally, twentyfouronline will make correct URLs (especially if you have set
up your proxy variables correctly)

```dotenv
APP_URL=http://twentyfouronline/
```

## User / Group

The user and group that twentyfouronline should operate as.
Group will default to the same as the user if unset.

```dotenv
twentyfouronline_USER=twentyfouronline
twentyfouronline_GROUP=twentyfouronline
```

## Debug

Increases the amount of information shown when an error occurs.

> WARNING: This may leak information, do not leave enabled.

```dotenv
APP_DEBUG=true
```




