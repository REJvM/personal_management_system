<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('roles', ChoiceType::class, [
            //     'choices' => User::class::AVAILABLE_ROLES,
            //     'multiple' => true,
            //     'expanded' => true
            // ])
            
            ->add('roles', ChoiceType::class, [
                'choices' => User::class::AVAILABLE_ROLES,
                'multiple' => true,
                'expanded' => true
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'mapped' => false,
                'invalid_message' => 'The password fields must match.',
                'attr' => ['autocomplete' => 'new-password'],
                'required' => false,
                'first_options' => [
                    'label' => 'Password',
                    'mapped' => false,
                ],
                'second_options' => [
                    'label' => 'Repeated Password',
                    'mapped' => false,
                ],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
