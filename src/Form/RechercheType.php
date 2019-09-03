<?php

namespace App\Form;

use App\Entity\Site;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site',EntityType::class,[
                "required"=>false,
                "mapped"=>false,
                "choice_label"=>"nom",
                "class"=>Site::class,
                "label"=>"Site"
            ])
            ->add('nom', null,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Le nom de la sortie contient"
            ])
            ->add('sortiesOrga', CheckboxType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Sorties que j'organise"
            ])
            ->add('sortiesInsc', CheckboxType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Sorties auxquelles je suis inscrit.e"
            ])
            ->add('sortiesPasInsc', CheckboxType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Sorties auxquelles je peux m'inscrire"
            ])
            ->add('sortiesPass', CheckboxType::class,[
                "required"=>false,
                "mapped"=>false,
                "label"=>"Sorties passées"
            ])
            ->add('date1', DateType::class, [
                "label"=>"du"
            ])
			->add('date2', DateType::class, [
                "label"=>"au"
            ]);

        ;
        $builder->add('rechercher', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([

        ]);
    }
}
