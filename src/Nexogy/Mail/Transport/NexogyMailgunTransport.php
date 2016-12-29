<?php namespace Nexogy\Mail\Transport;

use Swift_Transport;
use Swift_Mime_Message;
use GuzzleHttp\Post\PostFile;

class NexogyMailgunTransport extends \Illuminate\Mail\Transport\MailgunTransport implements Swift_Transport {
	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();
		
		$to = $this->getTo($message);
		
		$copyMessage = clone $message;
		$copyMessage->setBcc(null);
		
		$domain = substr($this->url, strrpos($this->url, '/') + 1);
		$fromDomain = array_keys($this->getFrom())[0];
		$fromDomain = substr($fromDomain, strrpos($fromDomain, '@') + 1);
		
		$from = str_replace($domain, $fromDomain, $this->url);

		$response = $client->post($from, ['auth' => ['api', $this->key],
			'body' => [
			   'to' => $to,
			   'message' => new PostFile('message', (string) $copyMessage),
			],
		]);

		return $response->json();
	}
}
