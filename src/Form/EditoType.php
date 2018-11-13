<?php

namespace App\Form;

use App\Entity\Edito;
use Symfony\Component\Form\AbstractType;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditoType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',
                TextType::class,
                $this->getConfiguration(true,'Titre *', 'Titre de l\'édito')
            )
            ->add('content',
                TextareaType::class,
                $this->getConfiguration(true,'Texte de l\'édito *', 'Votre texte')
            )
            ->add(
                'image',
                TextType::class,
                $this->getConfiguration(true,'Image *', 'Url de l\'image')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Edito::class,
        ]);
    }
}
