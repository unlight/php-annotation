<?php
/*
 * @author <a href="cameronz@bundaberg.qld.gov.au">Cameron Zemek</a>
 */

$__DIR__ = dirname(__FILE__);
require_once  $__DIR__ . '/Parser.php';

function Annotation_build($reflector) {
    static $_Annotation_Parser;
    if (is_null($_Annotation_Parser)) {
        $_Annotation_Parser = new AnnotationParser();
    }
    $comment = $reflector->getDocComment();
    return $_Annotation_Parser->parse($comment);
}

require_once $__DIR__ . '/ReflectionAnnotatedClass.php';
require_once $__DIR__ . '/ReflectionAnnotatedMethod.php';
require_once $__DIR__ . '/ReflectionAnnotatedProperty.php';
require_once $__DIR__ . '/ReflectionAnnotatedFunction.php';
require_once $__DIR__ . '/ReflectionAnnotatedParameter.php';
