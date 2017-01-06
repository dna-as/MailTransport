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
		
		$fromDomain = array_keys($message->getFrom())[0];
		$fromDomain = substr($fromDomain, strrpos($fromDomain, '@') + 1);
		
		$this->setDomain($fromDomain);

		$token = "use_nexogy_com_";
		$email = array_keys($message->getFrom())[0];
		if(substr($email, 0, 15) == $token) {
			$email = str_replace("use_nexogy_com_", "", $email);
			$copyMessage->setFrom($email);
			$this->setDomain("nexogy.com");
		}

		$response = $client->post($this->url, ['auth' => ['api', $this->key],
			'body' => [
			   'to' => $to,
			   'message' => new PostFile('message', (string) $copyMessage),
			],
		]);

		return $response->json();
	}
}
