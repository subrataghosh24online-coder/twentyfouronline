To run twentyfouronline under a subdirectory on your Apache server, the
directives for the twentyfouronline directory are placed in the base server
configuration, or in a virtual host container of your choosing. If
using a virtual host, place the directives in the file where the
virtual host is configured. If using the base server on RHEL
distributions (CentOS, Scientific Linux, etc.) the directives can be
placed in `/etc/httpd/conf.d/twentyfouronline.conf`. For Debian distributions
(Ubuntu, etc.) place the directives in
`/etc/apache2/sites-available/default`.

```apache
#These directives can be inside a virtual host or in the base server configuration
AllowEncodedSlashes On
Alias /twentyfouronline /opt/twentyfouronline/html

<Directory "/opt/twentyfouronline/html">
    AllowOverride All
    Options FollowSymLinks MultiViews
</Directory>
```

The `RewriteBase` directive in `html/.htaccess` must be rewritten to
reference the subdirectory name. Assuming twentyfouronline is running at
<http://example.com/twentyfouronline/>, you will need to change `RewriteBase /`
to `RewriteBase /twentyfouronline`.

Finally, set `APP_URL=/twentyfouronline/` in .env and `lnms config:set base_url '/twentyfouronline/'`.




