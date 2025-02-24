<?php

namespace App\Form;

use App\Entity\BlogPostLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BlogPostLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('icon', ChoiceType::class, [
                'choices' => BlogPostLink::LINK_ICONS,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('name', TextType::class, [
                'required'   => false,
                'attr' => array(
                    'placeholder' => 'Name'
                )
            ])
            ->add('url', UrlType::class, [
                'attr' => array(
                    'placeholder' => 'Url'
                )
            ])
            ->add('remove', ButtonType::class, [
                'label' => 'Remove link',
                'attr' => [
                    'class' => 'remove_item_link button button-secondary'
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPostLink::class,
        ]);
    }
}
