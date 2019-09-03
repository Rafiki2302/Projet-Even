<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,["required"=>false,"label"=>"Nom : ", "trim"=>true])
            ->add('rue', TextType::class,["required"=>false,"label"=>"Rue : ", "trim"=>true])
            ->add('latitude',NumberType::class,["scale"=>3,"required"=>false,"label"=>"Latitude : "])
            ->add('longitude',NumberType::class,["scale"=>3,"required"=>false,"label"=>"Longitude : "])
            ->add('ville',EntityType::class,[
                "class"=>Ville::class,
                "choice_label"=>'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
