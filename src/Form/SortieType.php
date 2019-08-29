<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
            "error_bubbling" => true,
            //  "trim" => true,
            "label" => "Nom",
            ])
            ->add('datedebut', DateTimeType::class, [
                "error_bubbling" => true,
                //  "trim" => true,
                "label" => "Date",
            ])
            ->add('duree', NumberType::class,[
                "error_bubbling" => true,
                //  "trim" => true,
                "label" => "Durée prévue",
                ])
            ->add('datecloture', DateTimeType::class, [
                "error_bubbling" => true,
                //  "trim" => true,
                "label" => "Date limite d'inscription",
            ])
            ->add('datedebut', DateTimeType::class, [
                "error_bubbling" => true,
                //  "trim" => true,
                "label" => "Date",
            ])
            ->add('nbinscriptionsmax', NumberType::class,[
                "error_bubbling" => true,
                //  "trim" => true,
                "label" => "Nombre de places",
                ])
            ->add('descriptioninfos', TextType::class,[
            "error_bubbling" => true,
            //  "trim" => true,
            "label" => "Description",
            ])

            ->add('lieu')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
