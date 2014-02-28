<?php defined('SYSPATH') or die('No direct script access.');

class Lib_Form {

    public $data;
    public $files;
    public $validation;

    public $rules;
    public $rule_on;

    private $cache;
    private $errors;
    private $errors_autofocus_once;

    function __construct($data=array(), $rule_on='') {
        $this->data = $data;
        $this->rule_on  = $rule_on;

        $this->rules = $this->rules();
        if( isset($_FILES)  ){
            if( count($_FILES)>0){
                foreach( $_FILES as $key => $file ){

                    if( !isset($this->rules[$key]) ) continue;

                    if( is_array($file['name']) ){
                        foreach ($file['name'] as $i => $img) {

                            $this->files[$key][] = $this->data[$key.$i] = array(
                                        'name' => $file['name'][$i],
                                        'type' => $file['type'][$i],
                                        'tmp_name' => $file['tmp_name'][$i],
                                        'error' => $file['error'][$i],
                                        'size' => $file['size'][$i],
                                    );

                            $this->rules[$key.$i] =  $this->rules[$key];
                        }
                        unset($this->rules[$key]);
                    }else{
                        $this->files[$key] = $this->data[$key] = $file;
                    }
                }
            }
        }
    }

    function getLabels(){

        $fields  = $this->cacheFields();
        $labels = array();

        foreach($fields as $i => $l){
            $labels[$i] = $l['label'];
        }
        return $labels;
    }

    // overwrite this field
    public function fields(){
        return array();
    }

    // overwrite this field
    function rules(){
        return array();
    }

    public function cacheFields(){
        $id = 'fields';
        if( isset($this->cache[$id]) ) return $this->cache[$id];
        return $this->cache[$id] = $this->fields();
    }

    public function label($id){
        $a = $this->cacheFields();
        if(isset($a[$id])){
            return '<label for="'.$id.'">'.$a[$id]['label'].'</label>';
        }
        return '';
    }

    public function field($id){

        $is_array = false;
        $element = '';
        $name = $id;
        if( preg_match('/\[.*\]$/', $id, $matches) ){
            $is_array = true;
            $element = $matches[0];
            $id = str_replace($element, '', $id);
        }

        $a = $this->cacheFields();
        if( isset($this->validation) ){
            $errors = $this->validation->errors();
            $a = $this->cacheFields();
            if( isset($errors[$id]) ){
                $a[$id]['attr']['class'] = ( ( isset($a[$id]['attr']['class']) )?$a[$id]['attr']['class'].' ':'' ) . ' error_field';
                if( isset($this->errors_autofocus_once) ){
                    $a[$id]['attr']['autofocus'] = 'autofocus';
                    $this->errors_autofocus_once = true;
                }
            }
        }

        if(isset($a[$id])){
            $redisplay = true;
            if( isset($a[$id]['redisplay'])  ){
                $redisplay = $a[$id]['redisplay'];
            }

            if($redisplay){
                // re dedisplay data if error occured
                $POST = $this->data;
                if( isset($POST[$id]) ){
                    $val = $POST[$id];
                }elseif( isset($a[$id]['db_field']) ){
                    if( isset($POST[$a[$id]['db_field']]) ){
                        $val = $POST[$a[$id]['db_field']];
                    }
                }
                if( isset($val) ){

                    if( in_array($a[$id]['type'],array('select','radio'))){
                        $a[$id]['selected'] = $val;
                    }elseif(  $a[$id]['type'] == 'checkbox' ){
                        $a[$id]['checked'] = true;
                    }else{
                        $a[$id]['value'] = $val;
                    }

                }
            }
            // end redisplay code
            return Lib_Form::getHtml($name,$a[$id]);
        }
        return '';
    }

    static function getHtml($id, $a){
        $type =  @$a['type'];
        $attr =  @$a['attr'];
        $value = @$a['value'];

        $attr['id']     = $id;
        $attr['data-label'] = @$a['label'];

        $ret = '';
        if($type=='radio' ){
            $options = @$a['options'];
            $selected = @$a['selected'];
            foreach($options as $i => $op){
                $check = false;
                if( $selected == $i ) $check = true;
                $ret .= ' '.Form::radio($id, $i, $check, $attr) .' '.$op;
            }
        }elseif($type=='checkbox'){
            $options = @$a['options'];
            $checked = @$a['checked'];
            $ret = Form::$type($id, $value,  $checked, $attr);
        }elseif($type=='select'){
            $options = @$a['options'];
            $selected = @$a['selected'];
            $ret = Form::$type($id, $options,  $selected, $attr);
        }elseif($type=='file'){
             $ret = Form::$type($id, $attr);
        }else{
            $ret = Form::$type($id,$value, $attr);
        }
        return $ret;

    }

    function validate(){

        $this->validation = Validation::factory($this->data);

        $this->validation->labels( $this->getLabels() );
        foreach ($this->rules as $id => $rules){
               $this->validation ->rules($id, $rules);
        }

        foreach($this->rules as $field => $field_rules){
            $this->validation->rules($field, $field_rules);
        }

        if($this->validation->check()){
            return true;
        }

        return false;

    }

    function errors(){
        if( isset($this->errors) ) return $this->errors;
        list($prefix,$class) = explode('_',  strtolower(get_called_class()));
        if(isset($this->validation)){
            return $this->validation->errors($class);
        }else{
            return array();
        }
    }

    // override
    // $mapping -- FORM => DB
    public function mapModel($model, $mapping=array())
    {
        // do the default
        $fields = array_keys($this->cacheFields());
        $db_data = $model->as_array();
        $db_attributes_keys = array_keys($db_data);
        foreach ($fields as $field) {
            if( isset($mapping[$field]) ){
                $this->data[$field] = $db_data[$mapping[$field]];
            }elseif( in_array($field, $db_attributes_keys) ){
                $this->data[$field] = $db_data[$field];
            }
        }
        foreach ($mapping as $db_key => $form_key) {
            $this->data[$form_key] = $model->$db_key;
        }

    }

}

?>
