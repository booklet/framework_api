<?php
class TesterParentModel extends Model
{
    public function fields()
    {
        return [
            'id'              => ['type' => 'integer'],
            'name'            => ['type' => 'string', 'validations' => ['required']],
            'created_at'      => ['type' => 'datetime'],
            'updated_at'      => ['type' => 'datetime'],
        ];
    }

    public static function relations()
    {
        return [
            'childs'          => ['relation' => 'has_many', 'class' => 'TesterChildModel']
        ];
    }

    public function acceptsNestedAtributesFor()
    {
        return ['childs'];
    }
}
