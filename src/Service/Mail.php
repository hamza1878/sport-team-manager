<?php

namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class Mail
{
    private string $apiKey = "43f9e54b7de7ecbaf5e10b5bc91b077c";
    private string $apiKeySecret = "93bb48dfc2f4a44a9ebf8e954041e531";
    private string $apiUrl = "https://api.mailjet.com/v3.1/send";

    public function __construct()
    {
    }
   
    public function send(string $fromEmail, string $toEmail, string $subject, string $htmlContent): bool
    {
        error_log('=== MAIL SERVICE CALLED ===');
        error_log('FROM: ' . $fromEmail);
        error_log('TO: ' . $toEmail);
        error_log('SUBJECT: ' . $subject);
        error_log('HTML LENGTH: ' . strlen($htmlContent));
        
        try {
            $httpClient = HttpClient::create();

            $payload = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => "aymenpower99@gmail.com",
                            'Name' => 'Manchester United Dashboard'
                        ],
                        'To' => [
                            [
                                'Email' => $toEmail, // IMPORTANT: Utiliser le paramètre
                                'Name' => 'Recipient'
                            ]
                        ],
                        'Subject' => $subject,
                        'HTMLPart' => $htmlContent,
                    ]
                ]
            ];

            error_log('Payload created, sending to Mailjet...');

            $response = $httpClient->request('POST', $this->apiUrl, [
                'auth_basic' => [$this->apiKey, $this->apiKeySecret],
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);

            $statusCode = $response->getStatusCode();
            $responseContent = $response->getContent(false);
            
            error_log('Response Status: ' . $statusCode);
            error_log('Response Body: ' . $responseContent);
            
            if ($statusCode === 200) {
                error_log('✅ Email sent successfully to ' . $toEmail);
                return true;
            } else {
                error_log('❌ Email failed with status: ' . $statusCode);
                error_log('Response: ' . $responseContent);
                return false;
            }
        } catch (TransportExceptionInterface $e) {
            error_log('❌ TransportException: ' . $e->getMessage());
            error_log('Code: ' . $e->getCode());
            return false;
        } catch (\Exception $e) {
            error_log('❌ Exception: ' . $e->getMessage());
            error_log('Type: ' . get_class($e));
            error_log('File: ' . $e->getFile() . ':' . $e->getLine());
            error_log('Trace: ' . $e->getTraceAsString());
            return false;
        }
    }
}