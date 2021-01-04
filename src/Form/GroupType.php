<?php

namespace App\Form;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('phone')
            ->add('academicYear',ChoiceType::class, [
                "placeholder"=>"Academic Year",
                "choices"=>[
                "2019"=>"2019",
                "2019/20"=>"2019/20",
                "2020"=>"2020",
                "2020/21"=>"2020/21",
                "2021"=>"2021",
                "2021/22"=>"2021/22",
                "2022"=>"2022",
                "2022/23"=>"2022/23",
            ]])
            ->add('year',ChoiceType::class, [
                "placeholder"=>"Year",
                "choices"=>[
                "1"=>1,
                "2"=>2,
                "3"=>3,
                "4"=>4,
                "5"=>5,
                "6"=>6,
            ]])
            ->add('member'
            // ,null,
            , EntityType::class,
             [
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {

                    $res = $er->createQueryBuilder('u')
                 
                        ->where('u.owngroup is NULL')
                        ->andWhere('u.userType=1')
                        ->orderBy('u.id', 'ASC');
                    ;
                    // dd($res->getQuery()->getResult());
                    return $res;
                },
                'placeholder' => 'Add Member',
                 "mapped"=>false,
                 "multiple"=>true,

            ]
            )
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Group::class,
        ]);
    }
}
