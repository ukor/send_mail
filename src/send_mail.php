<?php
/**
 * Created with PhpStorm by proctor.
 * @author Ukor Jidechi Ekundayo << http://ukorjidechi.com || ukorjidechi@gmail.com>>.
 * Date: 8/10/17
 * Time: 3:51 AM
 */

namespace ukorJidechi\mail;


class send_mail
{
    /**
     * Username for the SMTP server (google)
     */
    private $username = "YOUR EMAIL ADDRESS";

    /**
     * Password for the SMTP server (Required)
     *
     * for gmail users, use your gmail or gsuite password
     */
    private $password = "YOUR GMAIL PASSWORD";

    /**
     * SMTP server URL (Required)
     *
     * Google's SMTP address is smtp.gmail.com
     */
    private $stmp_url = 'smtp.gmail.com';

    /**
     * SMTP server port (Required)
     *
     * Leave as it is if you are using google's gmail service
     */
    private $stmp_port = 465;

    /** SMPT Protocol(Required)
     *
     * Leave as it is for the above port
     */
    private $protocol  = "ssl";

    /** SMTP transport layer */
    private $transport = null;

    private $mailer = null;

    private $message = null;

    private $mail_details = array();

    private $number_of_emails_sent = 0;

    /**
     * Holds email address for failed recipient
     */
    private $failedRecipient = array();


    function __construct($sender_email_address, $sender_name, $recipient_email_address, $recipient_name, $subject, $html_message, $plain_message)
    {
        $md = $this->mail_details_template($sender_email_address, $sender_name, $recipient_email_address, $recipient_name, $subject, $html_message, $plain_message);
        if($this->send_mail($md))
        {
            return true;
        }
        else{
            throw new \Exception("message was not sent", 500);
        }
    }

    /**
     * @Method
     * @Description
     * @param $sender_email_address
     * @param $sender_name
     * @param $recipient_email_address
     * @param $recipient_name
     * @param $subject
     * @param null|string $html_message
     * @param null|string $plain_message
     * @return array
     */
    private function mail_details_template($sender_email_address, $sender_name, $recipient_email_address, $recipient_name, $subject, $html_message, $plain_message)
    {
        $this->mail_details = [
            'subject' => $subject,
            'from' => [
                $sender_email_address => $sender_name
            ],

            /**
             * @var $to array : refers to the recipient(s) name and email addresses
             * takes this pattern... The recipient name is optional
             * ['recipient_email_address' => 'recipient_name', 'recipient_email_address' => 'recipient_name', 'recipient_email_address', ...n ]
             */

            'to' => [$recipient_email_address => $recipient_name],

            /** @var $html */
            'html' => $html_message,
            'plain' => $plain_message
        ];

        return $this->mail_details;
    }

    private function send_mail(array $mail_details)
    {
        /**
         * Create the transport
         */
        $this->transport = (new \Swift_SmtpTransport($this->stmp_url, $this->stmp_port, $this->protocol))
            ->setUsername($this->username)
            ->setPassword($this->password);

        /**
         * Create the Mailer using the created transport
         */
        $this->mailer = new \Swift_Mailer($this->transport);

        /**
         * Create the message
         */
        $this->message = (new \Swift_Message())->setSubject($mail_details['subject'])
            ->setFrom($mail_details['from'])
            ->setBody($mail_details['html'], 'text/html')
            ->addPart($mail_details['plain'], 'text/plain');

        /**
         * Iterate through the recipient array, so that only the recipient address shows up in the 'to' field
         */
        foreach ($mail_details['to'] as $recipient_email_address => $recipient_name)
        {
            /**
             * Use the value($recipient_name) when the index(recipient_email_address) is an integer.
             */
            if(is_int($recipient_email_address))
            {
                $this->message->setTo([$recipient_name]);
            }
            else
            {
                $this->message->setTo([$recipient_email_address => $recipient_name]);
            }

            /**
             * Send the email using the message created
             */
            $this->number_of_emails_sent += $this->mailer->send($this->message, $this->failedRecipient);
        }

        /**
         * Check for failures
         */
        if($this->number_of_emails_sent === 0)
        {
            /**
             * There was a failure, return an array of recipient email address that was unsuccessful
             */
            return $this->failedRecipient;
        }
        else
        {
            /**
             * No failure, return true
             */
            return true;
        }
    }

}