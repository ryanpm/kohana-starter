<?php

class Lib_TALCode extends PHPTAL_Namespace_Builtin{
    public function __construct(){
         parent::__construct('code', 'htpp://mytalnamespace.com/code');
         $this->addAttribute(new PHPTAL_NamespaceAttributeSurround('section', 1));
     }
}
