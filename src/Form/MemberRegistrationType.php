<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MemberRegistrationType extends ApplicationType
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
                $this->getConfiguration(true,'Nom *', 'Nom du membre')
            )
            ->add(
                'username',
                TextType::class,
                $this->getConfiguration(true,'username *', 'username du membre')
            )
            ->add(
                'email',
                EmailType::class,
                $this->getConfiguration(true,'Email *', 'Adresse email provisoire')
            )
            ->add(
                'job',
                TextType::class,
                $this->getConfiguration(true,'Fonction *', 'Fonction au niveau de l\'association')
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
