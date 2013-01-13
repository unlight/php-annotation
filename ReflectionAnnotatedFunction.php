<?php

class ReflectionAnnotatedFunction extends ReflectionFunction {
    
    private $annotations;

    public function __construct($functionName) {
        parent::__construct($functionName);
        $this->annotations = Annotation_build($this);
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

    public function getParameters() {
        $parameters = array();
        foreach (parent::getParameters() as $parameter) {
            $parameters[] = new ReflectionAnnotatedParameter($this->getName(), $parameter->getName());
        }
        return $parameters;
    }
}