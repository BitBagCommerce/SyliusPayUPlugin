<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\SyliusPayUPlugin\Form\Type;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PayUGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'environment',
                ChoiceType::class,
                [
                    'choices' => [
                        'bitbag.payu_plugin.secure' => OpenPayUBridgeInterface::SECURE_ENVIRONMENT,
                        'bitbag.payu_plugin.sandbox' => OpenPayUBridgeInterface::SANDBOX_ENVIRONMENT,
                    ],
                    'label' => 'bitbag.payu_plugin.environment',
                ]
            )
            ->add(
                'signature_key',
                TextType::class,
                [
                    'label' => 'bitbag.payu_plugin.signature_key',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'bitbag.payu_plugin.gateway_configuration.signature_key.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'pos_id',
                TextType::class,
                [
                    'label' => 'bitbag.payu_plugin.pos_id',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'bitbag.payu_plugin.gateway_configuration.pos_id.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            )
            ->add(
                'oauth_client_id',
                TextType::class,
                [
                    'label' => 'bitbag.payu_plugin.oauth_client_id',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'bitbag.payu_plugin.gateway_configuration.oauth_client_id.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            )->add(
                'oauth_client_secret',
                TextType::class,
                [
                    'label' => 'bitbag.payu_plugin.oauth_client_secret',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'bitbag.payu_plugin.gateway_configuration.oauth_client_secret.not_blank',
                                'groups' => ['sylius'],
                            ]
                        ),
                    ],
                ]
            );
    }
}
