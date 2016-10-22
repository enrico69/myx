<?php
/**
 * Reflection
 * 
 * A little extension for Twig, allowing to access static elements of a class.
 * 
 * @author Eric COURTIAL
 * @since 2015
 * 
 */
namespace AppBundle\Twig\Extension;

class Reflection extends \Twig_Extension {

    /**
     * Return the names of the available methods
     * 
     * @return an array of \Twig_SimpleFunction
     */
    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('callStaticMethod', array($this, 'callStaticMethod')),
            new \Twig_SimpleFunction('getStaticAttribute', array($this, 'getStaticAttribute')),
        );
    }

    /**
     * 
     * @param string $className is the name of the targeted class
     * @param string $methodName is the name of the targeted method
     * @param array $args is an array of parameters for the called method
     * @return mixed The value returned by the called method
     * @throws \RuntimeException
     */
    public function callStaticMethod($className, $methodName, array $args = []) {
        $reflectionClass = new \reflectionClass($className);

        // Check if the requested method is both static and public
        if ($reflectionClass->hasMethod($methodName) 
                && $reflectionClass->getMethod($methodName)->isStatic() 
                && $reflectionClass->getMethod($methodName)->isPublic()) {
            return call_user_func_array($className . '::' . $methodName, $args);
        } else {
            throw new \RuntimeException(
                "The class '$className' has no static method '$methodName'");
        }
        
    }

    /**
     * 
     * @param string $className is the name of the targeted class
     * @param string $attributeName is the name of the targeted attribute
     * @return mixed The value of the called attribute
     * @throws \RuntimeException
     */
    public function getStaticAttribute($className, $attributeName) {
        $reflectionClass = new \reflectionClass($className);

        // Check if the requested attribute is both static and public
        if ($reflectionClass->hasProperty($attributeName) 
                && $reflectionClass->getProperty($attributeName)->isStatic() 
                && $reflectionClass->getProperty($attributeName)->isPublic()) {
            return $reflectionClass->getProperty($attributeName)->getValue();
        }

        throw new \RuntimeException(
            "The class '$className' has no static attribute '$attributeName'");
    }

    /**
     * Return the name of the extension
     * 
     * @return string the name of the extension
     */
    public function getName() {
        return 'reflectionExtension';
    }

}
