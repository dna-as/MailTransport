<?php namespace Nexogy\Mail;

class Mailer extends \Illuminate\Mail\Mailer {


	public function send($view, array $data, $callback)
	{
		// First we need to parse the view, which could either be a string or an array
		// containing both an HTML and plain text versions of the view which should
		// be used when sending an e-mail. We will extract both of them out here.
		list($view, $plain) = $this->parseView($view);

		$data['message'] = $message = $this->createMessage();

		$this->callMessageBuilder($callback, $message);

		// Once we have retrieved the view content for the e-mail we will set the body
		// of this message using the HTML type, which will provide a simple wrapper
		// to creating view based emails that are able to receive arrays of data.
		$this->addContent($message, $view, $plain, $data);

		$message = $message->getSwiftMessage();

		return $this->sendSwiftMessage($message);
	}



	/**
	 * Send a Swift Message instance.
	 *
	 * @param  \Swift_Message  $message
	 * @return void
	 */
	protected function sendSwiftMessage($message)
	{
		if ($this->events) $this->events->fire('mailer.sending', array($message));

		if ( ! $this->pretending)
		{
			$response = $this->swift->send($message, $this->failedRecipients);
			if($this->events) $this->events->fire('mailer.sent', array($message, $response));
			return $response;
		}
		elseif (isset($this->logger))
		{
			return $this->logMessage($message);
		}
	}
}
