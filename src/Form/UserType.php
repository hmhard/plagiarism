<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $role=["System Admin" => "System Admin","Project Manager"=>"PROJECT_MANAGER","Student"=>"STUDENT"];

        $builder
         
               ->add('roles', ChoiceType::class,["choices" => $role,'mapped'=>false,"multiple"=>true,"placeholder"=>"Select Role"])
          
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('middleName')
            ->add('sex', ChoiceType::class,["choices" => ["Male"=>"Male","Female"=>"Female"],"placeholder"=>"Select Sex"])
            ->add('phone')
            // ->add('isActive')
            // ->add('lastLogin')
            // ->add('createdAt')
            // ->add('updatedAt')
            // ->add('registeredBy')
            ->add('userType')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
