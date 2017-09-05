<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class PayUGatewayConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('environment', ChoiceType::class, [
                'choices' => [
                    'bitbag.payu_plugin.secure' => 'secure',
                    'bitbag.payu_plugin.sandbox' => 'sandbox',
                ],
                'label' => 'bitbag.payu_plugin.environment',
            ])
            ->add('signature_key', TextType::class, [
                'label' => 'bitbag.payu_plugin.signature_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.payu_plugin.gateway_configuration.signature_key.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
            ->add('pos_id', TextType::class, [
                'label' => 'bitbag.payu_plugin.pos_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.payu_plugin.gateway_configuration.pos_id.not_blank',
                        'groups' => ['sylius'],
                    ])
                ],
            ])
        ;
    }
}
