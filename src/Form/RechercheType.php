<?php

namespace App\Form;

use App\Entity\Site;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\View\ChoiceView;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('site',EntityType::class,[
                "required"=>true,
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
                "label"=>"du",
                "data"=>new \DateTime("2019-01-01")
            ])
			->add('date2', DateType::class, [
                "label"=>"au",
                "data"=>new \DateTime("2022-01-01"),
            ]);

        ;
        $builder->add('rechercher', SubmitType::class);
    }

/*   public function finishView(FormView $view, FormInterface $form, array $options)
    {
        //parent::finishView($view, $form, $options); // TODO: Change the autogenerated stub
        $newChoice = new ChoiceView(array(), 'add', 'Tous les sites');
        $view->children['site']->vars['choices'][0] = $newChoice;
    } */


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
'validation_groups'=> false,
        ]);
    }
}
