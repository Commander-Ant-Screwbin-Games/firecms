<?php declare(strict_types=1);
/**
 * Fire Content Management System - A simple and secure piece of art.
 */

namespace FireCMS\Core;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Messenger\MessageHandler;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Mime\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function file_get_contents;
use function str_replace;

/**
 * The mailer class.
 */
class Mailer implements Core
{
    /** @var \Symfony\Component\Messenger\MessageBus $bus The messenger bus. */
    private $bus;

    /** @var \Symfony\Component\Mailer\Messenger\MessageHandler $messageHandler The message handler. */
    private $messageHandler;

    /** @var array $options The mailer options. */
    private $options = [];

    /** @var \Symfony\Component\Mailer\Transport $transport The mailer transport. */
    private $transport;

    /**
     * Construct a new mailer class.
     *
     * @param array $options The mailer options.
     *
     * @return void Returns nothing.
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->transport = Transport::fromDsn($this->options['dsn']);
        $this->messageHandler = new MessageHandler($transport);
        $this->bus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                SendEmailMessage::class => [$this->messageHandler],
            ])),
        ]);
        $this->mailer = new Mailer($this->transport, $this->bus);
    }

    /**
     * Attempt to send an email.
     *
     * @param stirng $to       The email to send this message to.
     * @param string $subject  The email messages subject.
     * @param string $bindings The message bindings to bind to the template.
     * @param array  $cc       The cc email addresses.
     * @param array  $bcc      The bcc email addresses.
     * @param string $template The mailer template to use to customize the email.
     *
     * @return void Returns nothing.
     */
    public function send(string $to, string $subject, array $bindings, array $cc = [], array $bcc = [], string $template = 'default'): void
    {
        $contents = file_get_contents($this->options['path'] . $template);
        foreach ($messageBindings as $key => $message) {
            $contents = str_replace('{{' . $key . '}}', $message, $contents);
        }
        $email = (new Email())
            ->from($this->options['form'])
            ->to($to)
            ->cc($cc)
            ->bcc($bcc)
            ->replyTo($this->options['reply_to'])
            ->subject($subject)
            ->html($contents);
        $this->mailer->send($email);
    }

    /**
     * Configure the mailer options.
     *
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver The options resolver.
     *
     * @return void Returns nothing.
     */
    private function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'from'     => 'example@example.com',
            'reply_to' => 'example@example.com',
            'path'     => '/templates/mailer/',
        ]);
        $resolver->setRequired('dsn');
        $resolver->setAllowedTypes('dsn', 'string');
        $resolver->setAllowedTypes('from', 'string');
        $resolver->setAllowedTypes('reply_to', 'string');
        $resolver->setAllowedTypes('path', 'string');
    }
}
