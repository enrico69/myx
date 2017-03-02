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
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This class represents the form for the editor entity
 */
class NoteType extends AbstractType {
    
    private $em;
    private $request;
    private $translator;
    
    /**
     * 
     * @param Doctrine $doctrine
     * @param RequestStack $requestStack
     * @param \AppBundle\Form\TranslatorInterface $translator
     */
    public function __construct(Doctrine $doctrine, RequestStack $requestStack, TranslatorInterface  $translator) {
        $this->em = $doctrine->getManager();
        $this->request = $requestStack;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        
        $entity = $builder->getData();
        $bookId = null;
        if(!is_null($entity->getBook())) {
            $bookId = $entity->getBook()->getId();
        }
        
        $this->options = $options;
        $builder
            ->add('filename', FileType::class, array('label' => $this->translator->trans('AuthFileType')))
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