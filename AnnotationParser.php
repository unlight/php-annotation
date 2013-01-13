<?php

class AnnotationParser {
    /**
     * Strip the comment container characters. Eg. *
     */
    private function stripComment($comment) {
        $lines = explode("\n", $comment);

        $lines[0] = substr($lines[0], 3); // Remove /**
        $lastLine =& $lines[count($lines) - 1];
        $lastLine = substr($lastLine, 0, strlen($lastLine) - 3); // Remove */

        // Trim whitespace and remove the *
        foreach ($lines as $num => $line) {
            $lines[$num] = ltrim(rtrim($line), " \t*");
        }

        return implode("\n", $lines);
    }

    /**
     * Get the annotations text from the comment. This method is responsible
     * for handling multi-line annotations.
     *
     * @return array An array of the unparsed annotations
     */
    private function getAnnotations($comment) {
        $annotations = array();
        $tag = ""; // Currently match text for the annotation tag
        $lines = explode("\n", $comment);
        foreach ($lines as $num => $line) {
            if (strlen($line) == 0 || $line[0] == '@') {
                // blank lines and new annotation tags end the previous tag
                if ($tag != "") {
                    $annotations[] = $tag;
                    $tag = "";
                }
            }
            if (isset($line[0]) && $line[0] == '@') {
                $tag = $line;
            } elseif ($tag != "") {
                $tag .= ' ' . $line;
            }
        }
        return $annotations;
    }

    public function parse($comment) {
        $comment = $this->stripComment($comment);
        $rawAnnotations = $this->getAnnotations($comment);

        $annotations = array();
        foreach ($rawAnnotations as $rawAnnotation) {
            match('@[a-zA-Z][a-zA-Z0-9_]+', $rawAnnotation, $tag, $remaining);
            $tag = substr($tag, 1); // Strip off @
            $remaining = ltrim($remaining); // Strip off whitespace
            $attributes = array();
            if (strlen($remaining) > 0) {
                if ($remaining[0] == '(') {
                    try {
                        $lexer = new AnnotationLexer($remaining);
                        $attributeParser = new AnnotationAttributeParser($lexer);
                        $attributes = $attributeParser->parse();
                    } catch (Exception $e) {
                        // Warn user there is error with annotation
                        trigger_error("ParseException: Annotation @" . $tag . " - " . $e->getMessage(), E_USER_WARNING);
                    }
                }
            }
            $annotationClass = $tag . 'Annotation';
            if (!class_exists($annotationClass)) {
                $annotationClass = 'Annotation';
            }
            $annotations[$tag] = new $annotationClass($tag, $remaining, $attributes);
        }
        return $annotations;
    }
}

/**
 * Match regular expression at start of $subject and return the match
 * and the remaining unmatched text from $subject
 *
 * @param string $regex Regular Expression
 * @param string $subject String to match
 * @param string $match The matched string
 * @param string $remaining Remaining unmatched $subject
 * @return bool Return wether the regular expression matched
 */
function match($regex, $subject, &$match, &$remaining) {
    if (preg_match("/^$regex/", $subject, $matches)) {
        $match = $matches[0];
        $remaining = substr($subject, strlen($match));
        return true;
    }
    return false;
}