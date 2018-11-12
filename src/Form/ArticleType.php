<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends ApplicationType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'category',
                EntityType::class,
                [
                    'class' => Category::class,
                    'choice_label' => 'title',
                    'label' => 'Catégorie *'
                ]
            )
            ->add(
                'title',
                TextType::class,
                $this->getConfiguration(true,'Titre *', 'Titre de l\'article')
            )
            ->add(
                'shortDescription',
                TextareaType::class,
                $this->getConfiguration(true,'Introduction *', 'Texte d\'introduction')
            )
            ->add(
                'quotation',
                TextareaType::class,
                $this->getConfiguration(false,'Texte entre les quotes', 'Petit texte')
            )
            ->add(
                'description',
                TextareaType::class,
                $this->getConfiguration(true,'Texte de l\'article *', 'Votre texte')
            )
            ->add(
                'image',
                TextType::class,
                $this->getConfiguration(true,'Image *', 'Url de l\'image')
            )
            ->add(
                'video',
                TextType::class,
                $this->getConfiguration(false,'Vidéo', 'Url de la vidéo')
            )
            ->add(
                'liked',
                IntegerType::class,
                $this->getConfiguration(false,'Nombre de Like', 'Entier')
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
