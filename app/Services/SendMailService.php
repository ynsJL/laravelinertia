<?php

namespace App\Services;

use App\Mail\SendMailTemplate;
use Carbon\Carbon;
use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Support\Facades\{
    Mail,
    URL,
    View,
};

class SendMailService
{
    /**
     * Send an email.
     *
     * @param string $subject    The email subject.
     * @param string $sender     The sender's email address.
     * @param mixed $recipient   The recipient's email address.
     * @param string $body       The email body content (it should be a blade file under resource/views/emails/).
     * @param string $template   The email template to use.
     * @param array<string, mixed> $data Additional data to pass to the email view.
     *
     * @return void
     */
    public static function sendEmail($subject, $sender, $recipient, $body, $template, $data = []): void
    {
        $mailInfo = self::buildMailInfo($subject, $sender, $recipient, $body, $template, $data);
        self::sendMail($mailInfo);
    }

    /**
     * Build an email information object.
     *
     * @param string $subject    The email subject.
     * @param string $sender     The sender's email address.
     * @param mixed $recipient   The recipient's email address.
     * @param string $body       The email body content (it should be a blade file under resource/views/emails/).
     * @param string $template   The email template to use.
     * @param array<string, mixed> $data Additional data to pass to the email view.
     *
     * @return object  An object containing email information.
     */
    private static function buildMailInfo($subject, $sender, $recipient, $body, $template, $data = []): object
    {
        // Ensure $recipient is always an array, even if a single email address is provided.
        if (is_string($recipient)) {
            $recipient = [$recipient];
        }

         // Set the view for the email based on user role
        $emailContentTemplate = 'emails.' . $body;
        $layout = 'emails.layouts.' . $template . '_layout';
        $convertedBody = '';

        if (View::exists($emailContentTemplate) && View::exists($layout)) {
            // For additional data to be passed on email view
            $viewData = $data ?: [];

            // Render the email content along with the data
            $body = view($emailContentTemplate, $viewData)->render();
            $convertedBody = self::convertTextLinksToAnchors(nl2br($body));

            // Programmatically include the common signature
            $commonSignatureView = config('mail.common_signature');
            if (View::exists($commonSignatureView)) {
                /** @var string $signatureView*/
                $signatureView = view($commonSignatureView);
                $convertedBody .= '<br><br>' . self::convertTextLinksToAnchors(nl2br($signatureView));
            }
        }

        return (object) [
            'subject' => $subject,
            'sender' => $sender,
            'recipient' => $recipient,
            'body' => $convertedBody,
            'template' => $layout
        ];
    }

    /**
     * Send the email using Laravel's Mail facade.
     *
     * @param object $mailInfo  An object containing email information.
     *
     * @return void
     */
    private static function sendMail($mailInfo): void
    {
        /** @var mixed $mailData */
        $mailData = $mailInfo;

        Mail::to($mailData->recipient)
            ->send(new SendMailTemplate($mailData));
    }

    /**
     * Convert text links in a string into HTML anchor tags.
     *
     * @param string $text The text containing links.
     *
     * @return string|null The text with links converted to anchor tags.
     */
    public static function convertTextLinksToAnchors($text)
    {
        //  regular expression pattern to match URLs
        $hostRegex = "([a-z\d][-a-z\d]*[a-z\d]\.)*[a-z][-a-z\d]*[a-z]";
        $portRegex = "(:\d{1,})?";
        $pathRegex = "(\/[^?<>#\"\s]+)?";
        $queryRegex = "(\?[^<>#\"\s]+)?";

        $urlRegex = "/(?:(?<=^)|(?<=\s))((ht|f)tps?:\/\/" . $hostRegex . $portRegex . $pathRegex . $queryRegex . ")/";

        // Replace URLs with anchor tags
        return preg_replace($urlRegex, '<a href="$1" target="_blank">$1</a>', $text);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public static function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(config('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
