<?php
class MysqlORMHABTM
{
    public static function getAllHabtmRelationsFromModelObject($model_obj)
    {
        $habtm_relations = [];
        foreach ($model_obj->relations() as $relation_key => $relation_params) {
            if ($relation_params['relation'] == 'has_and_belongs_to_many' and isset($model_obj->{$relation_key . '_ids'})) {
                $habtm_relations[$relation_key] = $relation_params;
            }
        }

        return $habtm_relations;
    }

    public static function getCurrentIdsForHabtmRelation($model_obj, $relation_key)
    {
        return array_map(function ($o) { return $o->id; }, $model_obj->$relation_key());
    }

    public static function getPassedIdsForHabtmRelation($model_obj, $relation_key)
    {
        // what if pass empty array
        $passed_ids = $model_obj->{$relation_key . '_ids'};

        // convert empty array passed as string '[]' to array object
        // http_build_query used in test request remove empty arrays
        if ($passed_ids == '[]') { $passed_ids = []; }

        // remove empty items (last field is mostly empty for deleting purpose)
        $passed_ids = array_filter($passed_ids);

        return $passed_ids;
    }
}
