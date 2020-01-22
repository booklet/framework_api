<?php
class FWTestRelationChildOne
{
    public static function relations()
    {
        return [
            'parent' => ['relation' => 'belongs_to', 'class' => 'FWTestRelationParent']
        ];
    }
}
