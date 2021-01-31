<?php

namespace App\Form;

use App\Entity\ProjectSession;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectSessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
         
            ->add('timeFrom',DateType::class,[
                "widget"=>"single_text",
                "html5"=>true,
                "attr"=>[
                    "min"=>(new DateTime())->format('Y-m-d')
                ]
            ])
            ->add('timeTo',DateType::class,[
                "widget"=>"single_text",
                "html5"=>true,
                "attr"=>[
                    "min"=>(new DateTime())->format('Y-m-d')
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectSession::class,
        ]);
    }
}
