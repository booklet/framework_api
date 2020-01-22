<?php
class FWTestCustomModel
{
    public $variable;

    public function customValidation()
    {
        $errors = [];
        if ($this->variable <= 0) {
            $errors['variable'] = 'must be greater than 0.';
        }
        return $errors;
    }


}
