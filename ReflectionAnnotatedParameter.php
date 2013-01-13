<?php

class ReflectionAnnotatedParameter extends ReflectionParameter {
    public function getDeclaringClass() {
        $class = parent::getDeclaringClass();
        return new ReflectionAnnotatedClass($class->getName());
    }

    public function getClass() {
        $class = parent::getClass();
        return new ReflectionAnnotatedClass($class->getName());
    }
}