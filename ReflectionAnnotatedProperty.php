<?php

class ReflectionAnnotatedProperty extends ReflectionProperty {
    private $annotations;

    public function __construct($class, $propertyName) {
        parent::__construct($class, $propertyName);
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
}