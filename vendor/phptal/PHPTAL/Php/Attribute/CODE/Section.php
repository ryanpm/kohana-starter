<?php

class PHPTAL_Php_Attribute_CODE_Section extends PHPTAL_Php_Attribute
{
    private $var;
    public function before(PHPTAL_Php_CodeWriter $codewriter)
    {
        list($section_name) = $this->parseSetExpression($this->expression);
        $codewriter->pushCode("Lib_View::section('". $section_name ."')");
    }

    public function after(PHPTAL_Php_CodeWriter $codewriter)
    {
        $codewriter->pushCode("Lib_View::end()");
    }
}

