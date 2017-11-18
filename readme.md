## Explore PHP SQL SRV

The purpose of this repo is to figure out the ins and outs of using the Microsoft's SQL Server with PHP.

# Usage
This repo uses Homestead so the first step is to get Virtual Box(it might work with other providers but I've never tried).

Once you have Virtual Box you can install Homestead by simply running `composer install`.

The next step is to run `vagrant up` but you may want to check `Homestead.yaml` to see if you agree with the configuration. Oh, while you're there you should add the hostname to you `/etc/hosts` file mapped to the ip address at the top of the file.

Now that you've got vagrant running you'll need to install the official Microsoft driver for PHP: https://github.com/Microsoft/msphpsql. If you plan to use PHP 7.1 I added a bash alias to `aliases` that can be run once the machine is booted to install the driver for PHP 7.1. It only works when run as root so you will need to do the following from the project directory:

1. `$ vagrant ssh`
1. `sudo su`
1. `souce code/aliases`
1. `install_sqlserver_php7.1`

That should get you setup with the driver. If you don't have a SQL Server to connect to I recommend the docker container: https://docs.microsoft.com/en-us/sql/linux/quickstart-install-connect-docker.

When you want to connect to the a docker container from a Virtual Box be aware you will need to use the host only network. Vagrant usually assigns one of these to the virtual machine either as vboxnet0 or vboxnet1. On my machine the address is `192.168.10.1`. My .env file for the database looks like this:

```text
DB_CONNECTION=sqlsrv
DB_HOST=192.168.10.1
DB_PORT=1433
DB_DATABASE=homestead
DB_USERNAME=sa
DB_PASSWORD="<YourStrong!Passw0rd>"
```

The `DB_PASSWORD` above is the one used in the example docs for running the SQL Server container(so don't get any ideas!).

All of the examples are written as unit tests. I like this as a way to present how the SQL commands work because unit tests have a neat way of laying things out.

You get the setup and tear down as well as a way to check that the result of the command is what you expect with the assertions. There's also the added bonus that as the SQL driver gets upgraded failing tests will highlight any changes.