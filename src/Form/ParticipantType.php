<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,["required"=>false,"label"=>"Nom : ", "trim"=>true])
            ->add('prenom',TextType::class,["required"=>false,"label"=>"Prénom : ", "trim"=>true])
            ->add('pseudo',TextType::class,["required"=>false,"label"=>"Pseudonyme : ", "trim"=>true])
            ->add('telephone',TextType::class,["required"=>false,"label"=>"Téléphone : ", "trim"=>true])
            ->add('mail',EmailType::class,["required"=>false,"label"=>"Adresse email :", "trim"=>true])
            ->add('motDePasseEnClair',PasswordType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Mot de passe :",
            ])
            ->add("motdePasseRepeat",PasswordType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Confirmez votre mot de passe : ",
            ])
            ->add('site',EntityType::class,[
                "choice_label"=>"nom",
                "class"=>Site::class,
                "label"=>"Site de l'ENI auquel vous êtes rattaché :",
            ])

            ->add('media',MediaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
