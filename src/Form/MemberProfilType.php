<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberProfilType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'firstname',
                TextType::class,
                $this->getConfiguration(true,'Prénom *', 'Prénom')
            )
            ->add(
                'lastname',
                TextType::class,
                $this->getConfiguration(true,'Nom *', 'Nom')
            )
            ->add(
                'username',
                TextType::class,
                $this->getConfiguration(true,'Username *', 'Username')
            )
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration(true,'Email *', 'Adresse email')
            )
            ->add(
                'picture',
                TextType::class,
                $this->getConfiguration(true,'Avatar *', 'URL de votre avatar')
            )
            ->add(
                'presentation',
                TextType::class,
                $this->getConfiguration(true,'Présentation *', 'Présentez-vous brièvement')
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
