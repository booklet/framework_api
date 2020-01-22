<?php
class TesterGrandsonModel extends Model
{
    public function fields()
    {
        return [
            'id'                     => ['type' => 'integer',],
            'tester_child_model_id'  => ['type' => 'integer', 'validations' => ['required']],
            'description'            => ['type' => 'text', 'validations' => ['required']],
            'created_at'             => ['type' => 'datetime'],
            'updated_at'             => ['type' => 'datetime'],
        ];
    }

    public static function relations()
    {
        return [
            'parent'                => ['relation' => 'belongs_to', 'class' => 'TesterChildModel']
        ];
    }
}
