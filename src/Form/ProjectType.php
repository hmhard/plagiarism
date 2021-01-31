<?php

namespace App\Form;

use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('projectSession')
            ->add('document',FileType::class,["mapped"=>false,"attr"=>["accept"=>".docx"]])
        
            ->add('ownerGroup',null,[
                "placeholder"=>"Select Group",
               
                    'query_builder' => function (EntityRepository $er) {
                        return $er->getMyGroups(["user_me"=>1]);
                          
                    },
               

                
            ])
        
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
