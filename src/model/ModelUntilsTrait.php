<?php
trait ModelUntilsTrait
{
    public function isNewRecord()
    {
        return $this->id == null ? true : false;
    }

    public function pluralizeClassName()
    {
        $class_name = get_called_class();
        $pluralize_class_name = Inflector::pluralize($class_name);

        return $pluralize_class_name;
    }
}
