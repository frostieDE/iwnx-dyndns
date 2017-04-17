# IWNX DynDNS Updater

App which can update DNS records for IWNX Domain accounts. Useful for dynamic DNS functionality.

## Installation

### From source

1. Ensure, you have at least PHP 7 and Composer installed (the app may work on older versions of PHP but it was not tested)
2. Download the app from the releases page and unzip it into a directory of your choice.
3. Create a custom `config.prod.yml` file which includes all the domains you want to update.
4. Configure your web-server to browser the `web/` directory. You may take a look at the [Silex documentation](https://silex.sensiolabs.org/doc/2.0/web_servers.html).

### Docker

You can grad a dockerized version of the app:

    $ docker pull frostiede/iwnx-dyndns
    
Then, create a custom `config.prod.yml` and mount it into the `app` directory:

    $ docker run --rm -name iwnx-updater -v config.prod.yml:/usr/src/dyndns-updater/app/config.prod.yml -p 9000:9000
    
**Note:** See the Docker documentation on how to mount volumes. You may also specify a local port other than `9000` if 
 this port is already in use.
 
## Configuration

A sample configuration is found here:

```yml
domrobot:
  username: 'Your IWNX username'
  password: 'Your IWNX password'
  shared_secret: 'Your 2FA Token (if any)'

domains:
  - { domain: 'dyndns.example.com', ipv4: 1234567890, ipv6: 1234567890 }
```

**Notes:**

* `shared_secret` should have the value `null` (not `'null'`) if you do not use 2FA
* `1234567890` are the DNS record IDs which can be found out in the web interface
* You do not need to provide both `ipv4` and `ipv6`

## Usage

    https://dyndns.example.com/update?domain=<Domain identified>&ipv4=<IPv4>&ipv6=<IPv6>
    
From the above `config.prod.yml`, `dyndns.example.com` is the domain identifier.
    
Note: based on your web server configuration, the URI may be `/index.php/update?domain=...`.


