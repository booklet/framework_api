<?php
trait ModelValidationTrait
{
    // Check if object is valid.
    public function isValid(array $params = [])
    {
        // Clear error
        unset($this->errors);

        // Callback function beforeValidate()
        if (method_exists($this, 'beforeValidate')) {
            $this->beforeValidate();
        }

        // TODO
        // In uniqes validator rule validator nedd db connection to check database
        // that not good, so we need to pass this database records as validator params
        $validation = new Validator($this, $this->validationRules(), $params);
        $validation->isValid();

        if ($this->validNestedObjects()) {
            // object OK
        } else {
            // save errors
        }

        if (empty($this->errors)) {
            return true;
        }

        return false;
    }

    // Extact validation rules form fields array.
    public function validationRules()
    {
        // return ValidationRules::getRulesFromModel($this);

        $rules = [];
        $fields = $this->fields();
        foreach ($fields as $key => $value) {
            // Field type validator
            $validations_rules = [];
            $type_validator = 'type:' . $value['type'];
            array_push($validations_rules, $type_validator);

            // Custom validators:
            // required, lenght, uniques, etc
            if (isset($value['validations'])) {
                $validations_rules = array_merge($validations_rules, $value['validations']);
            }
            $rules[$key] = $validations_rules;
        }

        return $rules;
    }

    // Valid objects passed by items_attribute field
    private function validNestedObjects()
    {
        // Check if object has declared nested attributes
        if (!method_exists($this, 'acceptsNestedAtributesFor')) {
            return;
        }

        $nested_objects_params = $this->getNestedAttributesPrams();

        // Loop current object nested atributes
        foreach ($nested_objects_params as $nested_object_param) {
            foreach ($this->{$nested_object_param['wrapper_name']} as $index => $item) {
                // If object has id, then update/delete
                if (isset($item['id'])) {
                    // Check if ID contains in parent object children
                    // for security reason, if user manipulate ids in form
                    $children_objects = $this->{$nested_object_param['attribute_name']}();
                    $children_ids = array_map(function ($o) { return $o->id; }, $children_objects);
                    if (!empty($children_objects) && !in_array($item['id'], $children_ids)) {
                        $this->errors[$nested_object_param['attribute_name'] . '[' . $index . '].' . 'id'] = ['Item not belongs to this parent.'];
                        continue;
                    }

                    // Update or destroy
                    if (isset($item['_destroy']) and $item['_destroy'] == 1) {
                        // If to destroy, do not valid
                    } else {
                        // Find element to update
                        $nested_obj = $nested_object_param['objects_class_name']::find($item['id']);

                        // Update object with new params
                        foreach ($item as $key => $value) {
                            $nested_obj->$key = $item[$key];
                        }

                        if (!$nested_obj->isValid()) {
                            $this->saveErrorsInParentObject($nested_object_param['attribute_name'], $index, $nested_obj);
                        }
                    }
                } else {
                    $params = $item;
                    $params = $this->addFakeParentId($params);

                    $nested_obj = new $nested_object_param['objects_class_name']($params);

                    $unique_attribs_values = $this->getUniqueAttributesValues($nested_obj, $nested_object_param['wrapper_name']);

                    if (!$nested_obj->isValid(['unique_attribs' => $unique_attribs_values])) {
                        $this->saveErrorsInParentObject($nested_object_param['attribute_name'], $index, $nested_obj);
                    }
                }
            }

            if (!empty($this->errors)) {
                return false;
            }
        }
    }

    private function saveErrorsInParentObject($attribute_name, $index, $nested_object)
    {
        $nested_obj_underscore_class_name = StringUntils::camelCaseToUnderscore(get_class($nested_object));
        foreach ($nested_object->errors as $key => $value) {
            if (!isset($this->errors)) {
                $this->errors = [];
            }
            $this->errors[$attribute_name . '[' . $index . '].' . $key] = $value;
        }
    }

    private function getUniqueAttributesValues($nested_object, $nested_object_wrapper_name)
    {
        $unique_attribs = [];
        // Get fields that required unique validation
        foreach ($nested_object->validationRules() as $attribute => $rules) {
            foreach ($rules as $rule) {
                if ($rule == 'unique') {
                    $unique_attribs[] = $attribute;
                }
            }
        }

        // Get values from that fields
        $unique_attribs_values = [];
        foreach ($unique_attribs as $unique_attrib) {
            $unique_items = [];

            foreach ($this->$nested_object_wrapper_name as $nested_object_arr) {
                $unique_items[] = $nested_object_arr[$unique_attrib];
            }

            if (!empty($unique_items)) {
                $unique_attribs_values[$unique_attrib] = $unique_items;
            }
        }

        return $unique_attribs_values;
    }

    // Add fake parent id to pass parent required id validation
    private function addFakeParentId($params)
    {
        $class_reflex = new ReflectionClass(get_called_class());
        $class_name = $class_reflex->getShortName();

        $underscore_class_name = StringUntils::camelCaseToUnderscore($class_name);
        $parent_key_name = $underscore_class_name . '_id';
        $params[$parent_key_name] = 0;

        return $params;
    }

    // Return all allowed model propertis
    public function allowedPropertis()
    {
        // Allowed attributes
        // errors -> store errors validation informations
        // oryginal_record -> clone object attributes when get object from database
        // _destroy -> use to delete object
        $allowed_propertis = ['errors', 'oryginal_record', '_destroy'];

        // Declared attributes in model
        foreach ($this->fields() as $key => $value) {
            $allowed_propertis[] = $key;
        }

        // Custom model allowed attributes
        if (method_exists($this, 'specialPropertis')) {
            $allowed_propertis = array_merge($allowed_propertis, $this->specialPropertis());
        }

        // Allow nested attributes params
        // TODO relations add this params
        if (method_exists($this, 'acceptsNestedAtributesFor')) {
            foreach ($this->acceptsNestedAtributesFor() as $attr) {
                $allowed_propertis[] = $attr . '_attributes';
            }
        }

        // Allow habtm ids array
        if (method_exists($this, 'relations')) {
            foreach ($this->relations() as $relation_name => $relation_params) {
                if ($relation_params['relation'] == 'has_and_belongs_to_many') {
                    $allowed_propertis[] = $relation_name . '_ids';
                }
            }
        }

        return $allowed_propertis;
    }

    public function getNestedAttributesPrams()
    {
        $nested_attributes = [];
        // Loop current object accepts nested atributes
        foreach ($this->acceptsNestedAtributesFor() as $attribute_name) {
            $nested_attribute_object_name = $attribute_name . '_attributes';
            if (isset($this->$nested_attribute_object_name)) {
                $data = [];
                $data['attribute_name'] = $attribute_name;
                $data['wrapper_name'] = $nested_attribute_object_name;
                $data['objects_class_name'] = $this->relations()[$attribute_name]['class'];
                $nested_attributes[] = $data;
            }
        }

        return $nested_attributes;
    }
}
