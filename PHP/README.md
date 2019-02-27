# Amazon Pay PHP SDK Samples macOS Sierra Quickstart:

Note: This instructions will only work if you are using macOS Sierra and have never previously made any changes to the built-in Apache server configuration.  If you have installed other web application servers that run on port 80 of your localhost, then these instructions may not work for you.

Commands listed would be run from a terminal command prompt.  Commands must be cut-and-pasted exactly as specified.

Clone the same repository from GitHub:
```
  cd ~
  mkdir www
  cd www
  git clone git@github.com:amzn/amazon-pay-sdk-samples.git
```
By default, Apache on the macOS has the PHP module disabled.  It can be enabled by simply removing the comment character (#) in front of the line in the configuration file.

If you have not previously enabled PHP on your Mac, you can edit the file manually, or, if you are feeling lucky, run a command from terminal to do it for you:
```
  sudo sed -i .backup '/#LoadModule php5_module libexec\/apache2\/libphp5.so/s/#//' /etc/apache2/httpd.conf
```
Next, it's probably easier to point Apache to use a directory under your normal home directory so that you don't have to use sudo to edit every file in it.  macOS uses the /Library/WebServer/Documents directory as the default for hosting files, but this is a protected directory.  Let's change
all references of it to the "www" directory under your home directory:
```
  sudo sed -i .backup 's@/Library/WebServer/Documents@'"$HOME"'/www@g' /etc/apache2/httpd.conf
```
After making these changes, you need to restart Apache:
```
  sudo apachectl restart
```
Next, you need to add your keys and secrets from Seller Central into the config.php file using nano or your text editor of choice.
```
  nano ~/www/config.php
```
From your favorite webrowser, navigate to:
  http://localhost/amazon-pay-sdk-samples/PHP

You will need a **sandbox test account** to run the sample.

To create a **sandbox test account**:
1. Login to Seller Central
1. Select "Amazon Pay (Sandbox View)" from the picklist at the top of the screen
1. Hover over the "Integration" option at the top of the screen
1. Select "Test Accounts" from the Integration menu
1. Click the "Create a new test account" button and follow the instructions
