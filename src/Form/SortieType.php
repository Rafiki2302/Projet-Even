<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
            //  "trim" => true,
            "label" => "Nom",
                "required"=>false,
            ])
            ->add('datedebut', DateTimeType::class, [
                //  "trim" => true,
                "label" => "Date de la sortie",
            ])
            ->add('duree', NumberType::class,[
                //  "trim" => true,
                "label" => "Durée prévue (en minutes)",
                "required"=>false,
                "invalid_message"=>"La durée doit être un nombre",
                ])
            ->add('datecloture', DateTimeType::class, [
                //  "trim" => true,
                "label" => "Date limite d'inscription",

            ])
            ->add('datedebut', DateTimeType::class, [
                //  "trim" => true,
                "label" => "Date",
                "required"=>false,

            ])
            ->add('nbinscriptionsmax', NumberType::class,[
                //  "trim" => true,
                "label" => "Nombre de places",
                "required"=>false,
                "invalid_message"=>"Le nombre de places doit être un nombre",
                ])
            ->add('descriptioninfos', TextareaType::class,[
            //  "trim" => true,
            "label" => "Description",
                "required"=>false,
            ])
            ->add("lieu",EntityType::class,[
                "choice_label"=>"nom",
                "class"=>Lieu::class,
                "label"=>"Lieu : "
            ])
            ->add('nouveauLieu',LieuType::class,[
                "mapped"=>false,
            ])
            ->add('enreg',SubmitType::class,["label"=>'Enregistrer en brouillon'])
            ->add('publi',SubmitType::class,["label"=>"Publier la sortie"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
