<?php

class ReflectionAnnotatedMethod extends ReflectionMethod {
    private $annotations;

    public function __construct($class, $methodName) {
        parent::__construct($class, $methodName);
        $this->annotations = Annotation_build::do($this);
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

    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getParameters() {
        $parameters = array();
        foreach (parent::getParameters() as $parameter) {
            $function = array($this->getDeclaringClass()->getName(), $this->getName());
            $parameters[] = new ReflectionAnnotatedParameter($function, $parameter->getName());
        }
        return $parameters;
    }
}