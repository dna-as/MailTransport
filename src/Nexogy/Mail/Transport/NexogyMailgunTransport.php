<?php namespace Nexogy\Mail\Transport;

use Swift_Transport;
use Swift_Mime_Message;

class NexogyMailgunTransport extends \Illuminate\Mail\Transport\MailgunTransport implements Swift_Transport {
	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();

		$response = $client->post($this->url, ['auth' => ['api', $this->key],
			'body' => [
			   'to' => $this->getTo($message),
			   'message' => new PostFile('message', (string) $message),
			],
		]);

		return $response->json();
	}
}