<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class GoPayPayumGatewayConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isProductionMode', ChoiceType::class, [
                'choices' => [
                    'sylius.ui.no_label' => false,
                    'sylius.ui.yes_label' => true,
                ],
                'label' => 'threebrs.gopay_payum_plugin.is_production_mode',
            ])
            ->add('goid', TextType::class, [
                'label' => 'threebrs.gopay_payum_plugin.goid',
                'constraints' => [
                    new NotBlank([
                        'message' => 'threebrs.gopay_payum_plugin.gateway_configuration.goid.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('clientId', TextType::class, [
                'label' => 'threebrs.gopay_payum_plugin.client_id',
                'constraints' => [
                    new NotBlank([
                        'message' => 'threebrs.gopay_payum_plugin.gateway_configuration.client_id.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ])
            ->add('clientSecret', TextType::class, [
                'label' => 'threebrs.gopay_payum_plugin.client_secret',
                'constraints' => [
                    new NotBlank([
                        'message' => 'threebrs.gopay_payum_plugin.gateway_configuration.client_secret.not_blank',
                        'groups' => ['sylius'],
                    ]),
                ],
            ]);
    }
}
