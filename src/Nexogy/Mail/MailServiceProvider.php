<?php namespace Nexogy\Mail;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Transport\LogTransport;
use Illuminate\Mail\Transport\MailgunTransport;
use Illuminate\Mail\Transport\MandrillTransport;
use Nexogy\Mail\Transport\NexogyMandrillTransport;
use Swift_SendmailTransport as SendmailTransport;

class MailServiceProvider extends \Illuminate\Mail\MailServiceProvider {
	/**
	 * Register the Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function registerSwiftTransport($config)
	{
		switch ($config['driver'])
		{
			case 'smtp':
				return $this->registerSmtpTransport($config);
			case 'sendmail':
				return $this->registerSendmailTransport($config);
			case 'mail':
				return $this->registerMailTransport($config);
			case 'mailgun':
				return $this->registerMailgunTransport($config);
			case 'mandrill':
				return $this->registerMandrillTransport($config);
			case 'nexogy-mandrill':
				return $this->registerNexogyMandrillTransport($config);
			case 'log':
				return $this->registerLogTransport($config);
			default:
				throw new \InvalidArgumentException('Invalid mail driver.');
		}
	}

	/**
	 * Register the Mandrill Swift Transport instance.
	 *
	 * @param  array  $config
	 * @return void
	 */
	protected function registerNexogyMandrillTransport($config)
	{
		$mandrill = $this->app['config']->get('services.mandrill', array());
		$this->app->bindShared('swift.transport', function() use ($mandrill)
		{
			return new NexogyMandrillTransport($mandrill['secret']);
		});
	}
}
