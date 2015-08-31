<?php namespace Nexogy\Mail\Transport;

use Swift_Transport;
use Swift_Mime_Message;

class NexogyMandrillTransport extends \Illuminate\Mail\Transport\MandrillTransport implements Swift_Transport {
	/**
	 * {@inheritdoc}
	 */
	public function send(Swift_Mime_Message $message, &$failedRecipients = null)
	{
		$client = $this->getHttpClient();

		$response = $client->post('https://mandrillapp.com/api/1.0/messages/send-raw.json', [
			'body' => [
				'key' => $this->key,
				'raw_message' => (string) $message,
				'async' => false,
			],
		]);

		return $response->json();
	}
}