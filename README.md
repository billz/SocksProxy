# 🧦 SocksProxy Plugin
This plugin will add SOCKS proxy support to RaspAP.

> This plugin is under development and is **not functional** in its present state.

## Contents
 - [Installation](#installation)
 - [Usage](#usage)

## Installation
The `SocksProxy` plugin makes use of [Dante](https://www.inet.no/dante/), a free and open source SOCKS proxy server. It consists of a both SOCKS _server_ and a SOCKS _client_, implementing RFC 1928 and related standards. Dante provides a great deal of flexibility and can be used for secure network connectivity. This plugin uses the `dante-server` Debian package.

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

Save and exit the file.

### Clone the plugin
RaspAP's default application path `/var/www/html` is used here. If you've chosen a different install location, substitute this in the steps below:

1. SSH into the device hosting RaspAP, change to the install location and create a `/plugins` directory.
   ```
   cd /var/www/html
   sudo mkdir plugins
   ```
3. Change to the `/plugins` directory and clone the `SocksProxy` repository:
   ```
   cd plugins
   sudo git clone https://github.com/billz/SocksPlugin
   ```
4. The PluginManager will autoload the plugin; a new 'Socks Proxy' item will appear in the sidebar.

### Apply the Dante configuration
A streamlined Dante configuration is provided to get the server up and running. Change to the new plugin directory and move this file to its destination. Note that this will overwrite the default Dante config:
```
cd SocksPlugin
sudo mv config/danted.conf /etc/danted.conf
```

## Usage
TBD
