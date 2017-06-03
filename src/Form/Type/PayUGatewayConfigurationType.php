<?php

namespace BitBag\PayUPlugin\Form\Type;

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
                    ])
                ],
            ])
            ->add('pos_id', TextType::class, [
                'label' => 'bitbag.payu_plugin.pos_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.payu_plugin.gateway_configuration.pos_id.not_blank',
                    ])
                ],
            ])
        ;
    }
}
