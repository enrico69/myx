<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

/**
 * This class represents the form for the language entity
 */
class AuthorType extends AbstractType {
    
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->options = $options;
        $builder
            ->add('surname', TextType::class, array(
                    'label' => 'Surname'
                )
            )
            ->add('name', TextType::class, array(
                    'label' => 'Human.Name'
                )
            )
            ->add('slug', TextType::class, array(
                    'label' => 'Slug'
                )
            )
            ->add('submit', SubmitType::class, array(
                'label' => 'Save'
            ));
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Author'
            )
        );
    }
    
}