<?php declare(strict_types=1);
/**
 * Fire Content Management System - A simple and secure piece of art.
 *
 * @license MIT License. (https://github.com/Commander-Ant-Screwbin-Games/firecms/blob/master/license)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * https://github.com/Commander-Ant-Screwbin-Games/firecms/tree/master/src/Core
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package Commander-Ant-Screwbin-Games/firecms.
 */

namespace FireCMS\Core;

use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Messenger\MessageHandler;
use Symfony\Component\Mailer\Messenger\SendEmailMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Messenger\Handler\HandlersLocator;
use Symfony\Component\Messenger\MessageBus;
use Symfony\Component\Messenger\Middleware\HandleMessageMiddleware;
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

    /** @var \Symfony\Component\Mailer\Mailer $mailer The mailer instance. */
    private $mailer;

    /** @var \Symfony\Component\Mailer\Messenger\MessageHandler $messageHandler The message handler. */
    private $messageHandler;

    /** @var array $options The mailer options. */
    private $options = [];

    /** @var \Symfony\Component\Mailer\Transport\TransportInterface $transport The mailer transport. */
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
        $this->messageHandler = new MessageHandler($this->transport);
        $this->bus = new MessageBus([
            new HandleMessageMiddleware(new HandlersLocator([
                SendEmailMessage::class => [$this->messageHandler],
            ])),
        ]);
        $this->mailer = new SymfonyMailer($this->transport, $this->bus);
    }

    /**
     * Attempt to send an email.
     *
     * @param string $to       The email to send this message to.
     * @param string $subject  The email messages subject.
     * @param array  $bindings The message bindings to bind to the template.
     * @param array  $cc       The cc email addresses.
     * @param array  $bcc      The bcc email addresses.
     * @param string $template The mailer template to use to customize the email.
     *
     * @return void Returns nothing.
     */
    public function send(string $to, string $subject, array $bindings = [], array $cc = [], array $bcc = [], string $template = 'default'): void
    {
        $contents = file_get_contents(__DIR__ . '/../../' . $this->options['path'] . $template);
        foreach ($bindings as $key => $message) {
            $contents = str_replace('{{' . $key . '}}', $message, $contents);
        }
        $email = (new Email())
            ->from($this->options['form'])
            ->to($to)
            ->cc(...$cc)
            ->bcc(...$bcc)
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
