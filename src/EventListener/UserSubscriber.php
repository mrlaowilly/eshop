<?php


namespace App\EventListener;

    use App\Entity\User;
    use Doctrine\ORM\Events;
    use Doctrine\Persistence\Event\LifecycleEventArgs;
    use Symfony\Component\Mailer\MailerInterface;
    use Symfony\Component\Mime\Email;

class UserSubscriber implements \Doctrine\Common\EventSubscriber
{

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postPersist
        ];
    }


    /**
     * Cette fonction se déclenche juste après l'insertion
     * d'un élément dans la BDD.
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->sendWelcomeEmail($args);
    }

    /**
     * Permet l'envoi d'un email de bienvenue
     * https://symfony.com/doc/current/mailer.html#creating-sending-messages
     * TODO : Mettre en place un service dédié pour cela.
     * @param LifecycleEventArgs $args
     */
    private function sendWelcomeEmail(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $email = (new Email())
            ->from('noreply@eshop.news')
            ->to($entity->getEmail())
            ->subject('Bienvenue sur notre site d\'achat en ligne !')
            ->html('<p>Bienvenue sur notre site d\'achat en ligne !</p>');

        $this->mailer->send($email);
    }
}
