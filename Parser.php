<?php

$FILE_PATH = dirname(__FILE__);
require_once $FILE_PATH . '/Annotation.php';
require_once $FILE_PATH . '/Token.php';
require_once $FILE_PATH . '/TokenBuffer.php';
require_once $FILE_PATH . '/Lexer.php';
require_once $FILE_PATH . '/ParseException.php';


/**
 * Parses annotation attributes in ([key =] value, ...) format.
 * Eg. ('Cameron', 'Zemek', age = 25, town = 'Maryborough')
 */
class AnnotationAttributeParser {
    
    private $lookAheadBuffer;

    public function __construct($lexer) {
        $this->lookAheadBuffer = new TokenBuffer($lexer, 1);
    }

    private function error($message) {
        throw new ParseException($message);
    }

    private function lookAhead($i = 1) {
        if ($this->lookAheadBuffer->isEmpty() || $i > $this->lookAheadBuffer->size()) {
            return null; // EOF
        }
        $token = $this->lookAheadBuffer->getToken($i - 1); // lookAhead index is 1-based
        return $token->type;
    }

    private function match($tokenType) {
        $token = $this->lookAheadBuffer->readToken();
        if ($token->type == $tokenType) {
            return $token;
        } else {
            $this->error("Expecting type " . $tokenType . " but got " . $token->type);
        }
        return null;
    }

    private function matchIn($tokenTypes) {
        $token = $this->lookAheadBuffer->readToken();
        foreach ($tokenTypes as $tokenType) {
            if ($token->type == $tokenType) {
                return $token;
            }
        }
        $this->error("Expecting type of (" . implode(", ", $tokenTypes) . ") but got " . $token->type);
        return null;
    }

    public function parse() {
        return $this->attributes();
    }

    private function attributes() {
        // ( attribute (, attribute)* )
        $args = array();
        $this->match(Token::LPAREN);
        if ($this->lookAhead() != Token::RPAREN) {
            $this->attribute($args);
            while ($this->lookAhead() == Token::COMMA) {
                $this->match(Token::COMMA);
                $this->attribute($args);
            }
        }
        $this->match(Token::RPAREN);
        return $args;
    }

    private function attribute(&$args) {
        // [key = ] value
        if ($this->lookAhead() == Token::KEYWORD) {
            $key = $this->match(Token::KEYWORD)->text;
            $this->match(Token::EQUAL);
            $value = $this->atom();
            $args[$key] = $value;
        } else {
            $args[] = $this->atom();
        }
    }

    private function atom() {
        if ($this->lookAhead() == Token::LPAREN) {
            return $this->attributes();
        } else {
            // NUMBER | VARIABLE | STRING_LITERAL
            return $this->matchIn(array(Token::NUMBER, Token::VARIABLE, Token::STRING_LITERAL))->text;
        }
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