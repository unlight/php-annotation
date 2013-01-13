<?php

class ReflectionAnnotatedClass extends ReflectionClass {
    
    private $annotations;

    public function __construct($className) {
        parent::__construct($className);
        $this->annotations = Annotation_build::build($this);
    }

    public function hasAnnotation($annotation) {
        return isset($this->annotations[$annotation]);
    }

    public function getAnnotation($annotation) {
        return $this->annotations[$annotation];
    }

    public function getAnnotations() {
        return $this->annotations;
    }

    public function getConstructor() {
        return new ReflectionAnnotatedMethod(parent::getConstructor());
    }

    protected function createReflectionMethod($method) {
        return new ReflectionAnnotatedMethod($this->getName(), $method->getName());
    }

    public function getMethod($name) {
        return $this->createReflectionMethod(parent::getMethod($name));
    }

    public function getMethods($filter = '') {
        $methods = array();
        foreach (parent::getMethods($filter) as $method) {
            $methods[] = $this->createReflectionMethod($method);
        }
        return $methods;
    }

    protected function createReflectionProperty($property) {
        return new ReflectionAnnotatedProperty($this->getName(), $property->getName());
    }

    public function getProperty($name) {
        return $this->createReflectionProperty(parent::getProperty($name));
    }

    public function getProperties($filter = 0) {
        $properties = array();
        foreach (parent::getProperties($filter) as $property) {
            $properties[] = $this->createReflectionProperty($property);
        }
        return $properties;
    }

    protected function createReflectionClass($class) {
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getInterfaces() {
        $interfaces = array();
        foreach (parent::getInterfaces() as $interface) {
            $interfaces[] = $this->createReflectionClass($interface);
        }
        return $interfaces;
    }

    public function getParentClass() {
        return $this->createReflectionClass(parent::getParentClass());
    }
}