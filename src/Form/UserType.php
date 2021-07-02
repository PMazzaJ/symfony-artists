<?php

namespace App\Form;

use App\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Username'
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Email'
            ])
            ->add('password', PasswordType::class,[
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Password'
            ])
            ->add('fullName', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label' => 'Full Name'
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::getRolesNames(),
                'multiple' => true,
                'label' => 'Role',
                'attr' => [
                    'class' => 'form-control'
                ],                
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'save btn btn-primary'],
                'label' => 'Submit',
            ]);
        ;       

    }    

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
