<?php

// HOME is required for AWS store credentials to work.
putenv('HOME=/var/www');

// REQUIRES a file called credentials in /var/www/.aws containing access key and pwd with chmod -R www-data:www-data .aws
// REQUIRES aws.phar in /usr/share/php

require 'aws.phar';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Sns\SnsClient;


function fn_sns ($recipientphone, $message) {

/*

Sends a text message (SMS message) directly to a phone number using Amazon SNS.

$recipientphone = intended recipient's full phone number with country code at beginning
				  eg +19999999999

$message = text message to be sent. If > 140 char will be split into sequential messages

*/



	$SnSclient = new SnsClient([
	    'profile' => 'default',
	    'region' => 'us-west-2',
	    'version' => '2010-03-31'
	]);

	try {
	    $result = $SnSclient->publish([
	        'Message' => $message,
	        'PhoneNumber' => $recipientphone,
	    ]);
	    var_dump($result);
	} catch (AwsException $e) {
	    // output error message if fails
	    error_log($e->getMessage());
	}

}



#function fn_ses($sender_email, $recipient_emails, $subject, $plaintext_body, $html_body) {
function fn_ses($sender_email, $recipient_emails, $subject, $plaintext_body) {

  /* 

    REQUIRES a file called credentials in /var/www/.aws containing access key and pwd with chmod -R www-data:www-data .aws
    REQUIRES aws.phar in /usr/share/php

    $sender_email must be verified with Amazon SES

    $recipient_emails comma separated

    Comment line 9 and uncomment lines 8 and 55-58 if you want html text

  */


  // Create an SesClient. Change the value of the region parameter if you're 
  // using an AWS Region other than US West (Oregon). Change the value of the
  // profile parameter if you want to use a profile in your credentials file
  // other than the default.
  $SesClient = new SesClient([
      'profile' => 'default',
      'version' => '2010-12-01',
      'region'  => 'us-west-2'
  ]);

  /*
  $subject = 'Amazon SES test (AWS SDK for PHP)';
  $plaintext_body = 'This email was sent with Amazon SES using the AWS SDK for PHP.' ;
  $html_body =  '<h1>AWS Amazon Simple Email Service Test Email</h1>'.
                '<p>This email was sent with <a href="https://aws.amazon.com/ses/">'.
                'Amazon SES</a> using the <a href="https://aws.amazon.com/sdk-for-php/">'.
                'AWS SDK for PHP</a>.</p>';
  */

  $char_set = 'UTF-8';

  try {
      $result = $SesClient->sendEmail([
          'Destination' => [
              'ToAddresses' => [$recipient_emails],
          ],
          'ReplyToAddresses' => [$sender_email],
          'Source' => $sender_email,
          'Message' => [
            'Body' => [
#                'Html' => [
#                    'Charset' => $char_set,
#                    'Data' => $html_body,
#                ],
                'Text' => [
                    'Charset' => $char_set,
                    'Data' => $plaintext_body,
                ],
            ],
            'Subject' => [
                'Charset' => $char_set,
                'Data' => $subject,
            ],
          ],
      ]);
      $messageId = $result['MessageId'];
      return $messageId;
  } catch (AwsException $e) {
      // output error message if fails
      echo $e->getMessage();
      $errortext = "The email was not sent. Error message: ".$e->getAwsErrorMessage();
      return $errortext;
  }

}

?>