<?php
class FWTestRelationParent
{
    public static function relations()
    {
        return [
            'childs' => ['relation' => 'has_many', 'class' => 'FWTestRelationChildOne']
        ];
    }
}
