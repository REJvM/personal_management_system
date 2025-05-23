<?php

namespace App\Form;

use App\Entity\BlogPost;
use App\Entity\FileUpload;
use App\Form\BlogPostLinkType;
use Doctrine\ORM\QueryBuilder;
use App\Form\Type\CkeditorType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BlogPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('category', ChoiceType::class, [
                'choices' => BlogPost::AVAILABLE_CATEGORIES,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('image', EntityType::class, [
                'class' => FileUpload::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
                'required' => false,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->where('u.background = 0');
                },
            ])
            ->add('links', CollectionType::class, [
                'entry_type' => BlogPostLinkType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('content', CkeditorType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}
