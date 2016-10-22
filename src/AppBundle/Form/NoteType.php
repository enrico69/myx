<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This class represents the form for the editor entity
 */
class NoteType extends AbstractType {
    
    private $em;
    private $request;
    
    /**
     * Constructor
     * 
     * @param Doctrine $doctrine
     * @param RequestStack $requestStack is the request stacl
     */
    public function __construct(Doctrine $doctrine, RequestStack $requestStack) {
        $this->em = $doctrine->getManager();
        $this->request = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $entity = $builder->getData();
        $bookId = null;
        if(!is_null($entity->getBook())) {
            $bookId = $entity->getBook()->getId();
        }
        
        $this->options = $options;
        $builder
            ->add('filename', FileType::class, array('label' => $this->getTranslator()->trans('AuthFileType')))
            ->add('bookId', HiddenType::class, array(
                "mapped" => false,
                'data' => $this->options['bookId']))
            ->add('submit', SubmitType::class, array(
                'label' => 'Save'
            ));
    }
    
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\Note',
                'bookId' => null
            )
        );
    }
    
}