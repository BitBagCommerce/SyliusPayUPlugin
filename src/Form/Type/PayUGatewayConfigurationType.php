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
                    'bitbag.form.gateway_configuration.payu.secure' => 'secure',
                    'bitbag.form.gateway_configuration.payu.sandbox' => 'sandbox',
                ],
                'label' => 'bitbag.form.gateway_configuration.payu.environment',
            ])
            ->add('signature_key', TextType::class, [
                'label' => 'bitbag.form.gateway_configuration.payu.signature_key',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.gateway_configuration.payu.signature_key.not_blank',
                    ])
                ],
            ])
            ->add('pos_id', TextType::class, [
                'label' => 'bitbag.form.gateway_configuration.payu.pos_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'bitbag.gateway_configuration.payu.pos_id.not_blank',
                    ])
                ],
            ])
        ;
    }
}
