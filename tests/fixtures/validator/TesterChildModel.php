<?php
class TesterChildModel extends Model
{
    public function fields()
    {
        return [
            'id'                     => ['type' => 'integer'],
            'tester_parent_model_id' => ['type' => 'integer', 'validations' => ['required']],
            'address'                => ['type' => 'string', 'validations' => ['required', 'email', 'unique']],
            'created_at'             => ['type' => 'datetime'],
            'updated_at'             => ['type' => 'datetime'],
        ];
    }

    public static function relations()
    {
        return [
            'parent'      => ['relation' => 'belongs_to', 'class' => 'TesterParentModel'],
            'grandsons'   => ['relation' => 'has_many', 'class' => 'TesterGrandsonModel']
        ];
    }

    public function acceptsNestedAtributesFor()
    {
        return ['grandsons'];
    }
}
