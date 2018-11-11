<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Member;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'title'
                ]
            )
            ->add('title')
            ->add('shortDescription')
            ->add('quotation')
            ->add('description')
            ->add('image')
            ->add('video')
            ->add(
                'member',
                EntityType::class,
                [
                    'class' => Member::class,
                    'choice_label' => 'name'
                ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'translation_domain' => 'forms'
        ]);
    }
}
