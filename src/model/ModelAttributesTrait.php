<?php
trait ModelAttributesTrait
{
    // Contains model values as column_name => value.
    private $attributes = [];

    // Flag whether or not this model's attributes have been modified since
    // it will either be null or an array of column_names that have been modified.
    // private $__dirty = null;

    public function __construct(array $attributes = [])
    {
        // Setup default model values
        foreach ($this->fields() as $name => $value) {
            // Use assignAttribute to not call any callbacks
            $this->assignAttribute($name, $value['default'] ?? null);
        }

        // Assign object attributes when create
        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        // Initialize object relations if passed
    }

    /**
     * Throw exception if property not exist, else set property.
     *
     * class User extends Model {
     *   # define custom setter methods. Note you must
     *   # prepend set_ to your method name:
     *   function set_password($plaintext) {
     *     $this->encrypted_password = md5($plaintext);
     *   }
     * }
     *
     * $user = new User();
     * $user->password = 'plaintext';  # will call $user->set_password('plaintext')
     *
     * If you define a custom setter with the same name as an attribute then you
     * will need to use assignAttribute() to assign the value to the attribute.
     * This is necessary due to the way __set() works.
     *
     * class User extends Model {
     *   # INCORRECT way to do it
     *   # function set_name($name) {
     *   #   $this->name = strtoupper($name);
     *   # }
     *
     *   function set_name($name) {
     *     $this->assignAttribute('name',strtoupper($name));
     *   }
     * }
     */
    public function __set($name, $value)
    {
        $allowed_propertis = $this->allowedPropertis();
        if (!in_array($name, $allowed_propertis)) {
            throw new Exception(get_called_class() . " does not have '" . $name . "' property.");
        }

        // Set method from model if exists
        if (method_exists($this, "set_$name")) {
            $name = "set_$name";

            return $this->$name($value);
        }

        return $this->assignAttribute($name, $value);
    }

    /**
     * Magic method which delegates to readAttribute().
     *
     * You can also define customer getter methods for the model.
     *
     * EXAMPLE:
     * class User extends ActiveRecord\Model {
     *   # define custom getter methods. Note you must
     *   # prepend get_ to your method name:
     *   function get_middle_initial() {
     *     return $this->middle_name{0};
     *   }
     * }
     *
     * $user = new User();
     * echo $user->middle_name;  # will call $user->get_middle_name()
     * </code>
     *
     * If you define a custom getter with the same name as an attribute then you
     * will need to use readAttribute() to get the attribute's value.
     * This is necessary due to the way __get() works.
     *
     * For example, assume 'name' is a field on the table and we're defining a
     * custom getter for 'name':
     *
     * class User extends ActiveRecord\Model {
     *   # INCORRECT way to do it
     *   # function get_name() {
     *   #   return strtoupper($this->name);
     *   # }
     *
     *   function get_name() {
     *     return strtoupper($this->readAttribute('name'));
     *   }
     * }
     *
     * $user = new User();
     * $user->name = 'bob';
     * echo $user->name; # => BOB
     */
    public function &__get($name)
    {
        // Check for getter
        if (method_exists($this, "get_$name")) {
            $name = "get_$name";
            $value = $this->$name();

            return $value;
        }

        return $this->readAttribute($name);
    }

    // TODO After reorganize model nested attributes remove this function.
    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    public function &readAttribute($name)
    {
        // Check for attribute
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        // Check relationships if no attribute
        // if (array_key_exists($name,$this->__relationships))
        //   return $this->__relationships[$name];

        throw new Exception(get_called_class() . " does not have '" . $name . "' property.");
    }

    // Determines if an attribute exists for this Model
    // isset($myObj->item) call this function.
    public function __isset($attribute_name)
    {
        return array_key_exists($attribute_name, $this->attributes);
    }

    public function attributes()
    {
        return $this->attributes;
    }

    // Assign a value to an attribute.
    public function assignAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        // $this->flagDirty($name);

        return $value;
    }

    // Flags an attribute as dirty.
    public function flagDirty($name)
    {
        if (!$this->__dirty) {
            $this->__dirty = [];
        }

        $this->__dirty[$name] = true;
    }

    // Returns hash of attributes that have been modified since loading the model.
    // return mixed null if no dirty attributes otherwise returns array of dirty attributes
    public function dirtyAttributes()
    {
        if (!$this->__dirty) {
            return null;
        }
        $dirty = array_intersect_key($this->attributes, $this->__dirty);

        return !empty($dirty) ? $dirty : null;
    }

    // Check if a particular attribute has been modified since loading the model.
    public function attributeIsDirty($attribute)
    {
        return $this->__dirty && isset($this->__dirty[$attribute]) && array_key_exists($attribute, $this->attributes);
    }
}
