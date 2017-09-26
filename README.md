# Send mail
## About
Send mail is simple script base on swift mailer, that make sending of emails from your php app a breeze.
## Installation
**Note that installing send mail also install swift mailer _version(^6.0)_  and all its dependency.**

```$xslt
composer require "ukorjidechi/send_mail:^0.1"
```
* Change the _username_ and _password_ property in ``` send_mail.php ```  (The file has been well commented for easy configuration).
 

### Example
```php
<?php

require __DIR__."/path/to/vendor/autoload.php";

try {
    
    $sender_email_addr = "sender@example.com";
    $sender_name = "Sender Name";
    $recipient_email_addr = "recipient@example.com";
    $recipient_name = "Recipient Name";
    $subject = "Email Subject";
    $html_message = "<h2>This is the HTML part of the email message</h2>";
    $plain_message = "This is the plain part of the email message";
    
    (new \ukorJidechi\mail\send_mail(
                                     $sender_email_addr, $sender_name, 
                                     $recipient_email_addr, $recipient_name, 
                                     $subject, $html_message, $plain_message
                                     ));
}catch(Exception $exception)
{
    echo $exception->getMessage() ."<br><br>". $exception->getCode(). "<br><br>".$exception->getTraceAsString();
}
?>
```

Happy coding.