<?php

namespace App\Service;

use App\Entity\Emailing;
use App\Mailing\Mailer;
use App\Manager\SettingsManager;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class NewsletterService
{
    public function __construct(private Mailer $mailer, private SettingsManager $manager)
    {
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function send(Emailing $emailing)
    {
        $sender = $this->mailer->createEmail('mails/newsletter/particulier.twig', ['emailing' => $emailing])
            ->to($emailing->getDestinataire())
            ->subject($this->manager->get()->getName() . ' | '. $emailing->getSubject());

        $this->mailer->send($sender);
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendUser(Emailing $emailing, array $users)
    {
        foreach ($users as $user) {
            $sender = $this->mailer->createEmail('mails/newsletter/user.twig', ['emailing' => $emailing, 'user' => $user])
                ->to($user->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendNewsletter(Emailing $emailing, array $newsletters)
    {
        foreach ($newsletters as $newsletter) {
            $sender = $this->mailer->createEmail('mails/newsletter/newsletter.twig', ['emailing' => $emailing, 'newsletter' => $newsletter])
                ->to($newsletter->getEmail())
                ->subject($this->manager->get()->getName() . ' | ' . $emailing->getSubject());

            $this->mailer->send($sender);
        }
    }
}