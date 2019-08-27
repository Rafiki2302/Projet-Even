<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,["required"=>false,"label"=>"Nom : "])
            ->add('prenom',TextType::class,["required"=>false,"label"=>"Prénom : "])
            ->add('pseudo',TextType::class,["required"=>false,"label"=>"Pseudonyme : "])
            ->add('telephone',TextType::class,["required"=>false,"label"=>"Téléphone : "])
            ->add('mail',EmailType::class,["required"=>false,"label"=>"Adresse email :"])
            ->add('motDePasseEnClair',RepeatedType::class,[
                "mapped"=>false,
                "label"=>"Mot de passe",
                "first_options"=>["label"=>"Mot de passe"],
                "second_options"=>["label"=>"Confirmez votre mot de passe"],
            ])
            ->add('site',EntityType::class,[
                "choice_label"=>"nom",
                "class"=>Site::class,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
