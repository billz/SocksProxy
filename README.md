# 🧦 SocksProxy Plugin
This plugin adds SOCKS v5 proxy server support to RaspAP.

> ⚠️ The SocksProxy plugin is currently in BETA. Please [create an issue](https://github.com/billz/SocksProxy/issues) to report bugs or [start a discussion](https://github.com/billz/SocksProxy/discussions) for anything else. Thanks!

Proxy servers act as gateway that routes traffic between an end user and an internet resource. In the context of RaspAP, or network devices generally, this provides an additional layer of privacy and security. Likewise, proxies are useful for other purposes such as bypassing geo-restrictions or managing network traffic more efficiently via caching.

## Contents
 - [Installation](#installation)
 - [Usage](#usage)

## Installation
The `SocksProxy` plugin makes use of [Dante](https://www.inet.no/dante/), a free and open source SOCKS proxy server that implements [RFC 1928](https://datatracker.ietf.org/doc/html/rfc1928) and related standards. Dante provides a great deal of flexibility and is often used in Linux for secure network connectivity. This plugin uses the `dante-server` Debian package. The steps to install Dante and enable the plugin are provided in the next sections.

### Install packages
Begin by executing the following to update your system packages, then install Dante:

```
sudo apt update
sudo apt install dante-server
```

Dante will automatically set up a `systemd` background service and start it after the installation. However, it ships with all of its features disabled so will gracefully quit with an error message the first time it runs. This is normal.

### Edit sudoers
Next, execute `visudo` to safely edit RaspAP's `sudoers` file:

```
sudo visudo /etc/sudoers.d/090_raspap
```

Append the following to the end of this file:

```
www-data ALL=(ALL) NOPASSWD:/bin/systemctl * danted.service
www-data ALL=(ALL) NOPASSWD:/bin/cat /etc/danted.conf
www-data ALL=(ALL) NOPASSWD:/bin/cp /tmp/danted.conf /etc/danted.conf
```

Save and exit the file. This grants the restricted `www-data` user limited control over Dante's service and configuration.

### Clone the plugin
RaspAP's default application path `/var/www/html` is used here. If you've chosen a different install location, substitute this in the steps below:

1. Change to your RaspAP install location and create a `/plugins` directory.
   ```
   cd /var/www/html
   sudo mkdir plugins
   ```
3. Change to the `/plugins` directory and clone the `SocksProxy` repository:
   ```
   cd plugins
   sudo git clone https://github.com/billz/SocksProxy
   ```
4. The PluginManager will autoload the plugin. A new 'Socks Proxy' item will appear in the sidebar.

### Apply the Dante configuration
A streamlined Dante configuration is provided to get the server up and running. Change to the new plugin directory and move this file to its destination. Note that this will overwrite the default Dante config:
```
cd SocksProxy
sudo cp config/danted.conf /etc/danted.conf
```

### Create a SOCKS user
For better security, create a dedicated user to authenticate with Dante that doesn't have login privileges: 
```
sudo useradd -r -s /bin/false danteuser
sudo passwd danteuser
```
The `passwd` command will prompt you for a new password. In the [usage](#usage) example below `sockspass` is used, however you should choose your own secure password.

## Usage
Restart the Dante service from the Socks Proxy plugin UI. This will apply the basic Dante configuration and should start up a functional SOCKS proxy server. Confirm this by checking the service output on the **Status** tab. In the example output below, `danted.service` indicates that its current state is "active (running)":

```
● danted.service - SOCKS (v4 and v5) proxy daemon (danted)
     Loaded: loaded (/lib/systemd/system/danted.service; enabled; preset: enabled)
     Active: active (running)
```

With the Dante server running, you may now perform a basic connection test on your local machine. Substitute `danteuser` and `sockspass` for the values you used in the previous step:
```
curl -v -x socks5://danteuser:sockspass@0.0.0.0:1080 http://github.com/
```
Output:
```
*   Trying 0.0.0.0:1080...
* Connected to 0.0.0.0 (127.0.0.1) port 1080 (#0)
* SOCKS5 connect to IPv4 140.82.121.3:80 (locally resolved)
* SOCKS5 request granted.
* Connected to 0.0.0.0 (127.0.0.1) port 1080 (#0)
> GET / HTTP/1.1
> Host: github.com
> User-Agent: curl/7.88.1
> Accept: */*
...
```
The credentials you used for `curl` should work anywhere else you might want to use your proxy server.
