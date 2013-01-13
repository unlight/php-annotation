<?php

final class Annotation_build {

	public static function build() {
	    static $_Annotation_Parser;
	    if (is_null($_Annotation_Parser)) {
	        $_Annotation_Parser = new AnnotationParser();
	    }
	    $comment = $reflector->getDocComment();
	    return $_Annotation_Parser->parse($comment);		
	}
}