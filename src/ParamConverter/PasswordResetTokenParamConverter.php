<?php

namespace App\ParamConverter;

use App\Entity\PasswordResetToken;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class PasswordResetTokenParamConverter implements ParamConverterInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $token = $this->em->getRepository(PasswordResetToken::class)->findOneBy([
            'token' => $request->get('token'),
        ]);

        $request->attributes->set('token', $token);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    #[Pure] public function supports(ParamConverter $configuration): bool
    {
        return PasswordResetToken::class === $configuration->getClass() && 'token' === $configuration->getName();
    }
}
