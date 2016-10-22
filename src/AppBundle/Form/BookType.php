<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Translation\TranslatorInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\NoResultException;

/**
 * This class represents the form for the book entity
 */
class BookType extends AbstractType {
    
    private $translator;
    private $em;
    private $request;
    private $logger;
    
    /**
     * List the Editors
     * 
     * @return an array of Editors
     * @throws \Exception
     */
    private function getEditorList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Editor')->findAllOrderedByNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list editors from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingEditors')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Materials
     * 
     * @return an array of materials
     * @throws \Exception
     */
    private function getMaterialList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Material')->findAllOrderedByNameWp();
            foreach($arrResult as $material) {
                $material->setName($this->translator->trans(trim($material->getName())));
            }
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        } catch (Exception $ex) {
            $this->logger->info('Unable to list materials from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingMaterials')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Formats
     * 
     * @return an array of Formats
     * @throws \Exception
     */
    private function getFormatList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Format')->findAllOrderedByNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list formats from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingFormats')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Languages
     * 
     * @return an array of Languages
     * @throws \Exception
     */
    private function getLanguageList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Language')->findAllOrderedByNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list languages from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingLanguages')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Locations
     * 
     * @return an array of Locations
     * @throws \Exception
     */
    private function getLocationList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Location')->findAllOrderedByNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list locations from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingLocations')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Authors
     * 
     * @return an array of Authors
     * @throws \Exception
     */
    private function getAuthorList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Author')->findAllOrderedBySurNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list authors from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingAuthors')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * List the Locations
     * 
     * @return an array of Kinds
     * @throws \Exception
     */
    private function getKindList() {
        try {
            $arrResult = $this->em->getRepository('AppBundle:Kind')->findAllOrderedByNameWp();
        } catch (NoResultException $e) {
            $arrResult = array();
            unset($e);
        }catch (Exception $ex) {
            $this->logger->info('Unable to list kinds from booktype class ' . $ex->getMessage());
            throw new \Exception(
                $this->translator->trans('errorListingKinds')
            );
        }
        
        return $arrResult;
    }
    
    /**
     * Constructor
     * 
     * @param Doctrine $doctrine
     * @param RequestStack $requestStack is the request stacl
     */
    public function __construct(Doctrine $doctrine, RequestStack $requestStack, $logger, TranslatorInterface  $translator) {
        $this->em = $doctrine->getManager();
        $this->request = $requestStack;
        $this->logger = $logger;      
        $this->translator = $translator;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $this->options = $options;
        $builder
            ->add('title', TextType::class, array(
                    'label' => 'Title'
                )
            )
            ->add('description', TextareaType::class, array(
                    'label' => 'Description'
                )
            )
            ->add('year', TextType::class, array(
                    'label' => 'Year'
                )
            )
            ->add('editor', EntityType::class, array(
                    'class' => 'AppBundle:Editor',
                    'label' => 'Editor.single',
                    'choices' => $this->getEditorList()
                )
            )
            ->add('format', EntityType::class, array(
                    'class' => 'AppBundle:Format',
                    'label' => 'Format.single',
                    'choices' => $this->getFormatList()
                )
            )
            ->add('material', EntityType::class, array(
                    'class' => 'AppBundle:Material',
                    'label' => 'Material',
                    'choices' => $this->getMaterialList()
                )
            )
            ->add('language', EntityType::class, array(
                    'class' => 'AppBundle:Language',
                    'label' => 'Language.single',
                    'choices' => $this->getLanguageList()
                )
            )
            ->add('location', EntityType::class, array(
                    'class' => 'AppBundle:Location',
                    'label' => 'Location.plural',
                    'choices' => $this->getLocationList(),
                    'multiple' => true
                )
            )
            ->add('author', EntityType::class, array(
                    'class' => 'AppBundle:Author',
                    'label' => 'Author.plural',
                    'choices' => $this->getAuthorList(),
                    'multiple' => true
                )
            )
            ->add('kind', EntityType::class, array(
                    'class' => 'AppBundle:Kind',
                    'label' => 'Kind.plural',
                    'choices' => $this->getKindList(),
                    'multiple' => true
                )
            )
            ->add('isbn', TextType::class, array(
                    'label' => 'ISBN',
                    'required' => false
                )
            )
            ->add('keywords', TextType::class, array(
                    'label' => 'Keywords'
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
                'data_class' => 'AppBundle\Entity\Book',
                'locale' => 'en', // the default locale
            )
        );
    }
    
}