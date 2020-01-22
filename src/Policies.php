<?php
abstract class Policies
{
    function __construct($current_user, $obj)
    {
        $this->current_user = $current_user;
        $this->obj = $obj;
    }

    /**
    * Return passed current user or return default www user
    * www user allow accest do data to not require login -> calculators, shipping cart, ...
    */
    public function currentUser()
    {
        if (isset($this->current_user)) {
            return $this->current_user;
        } else {
            // Default user
            // TODO move create deafult user to application
            return new User(['username'=>'WWW', 'email'=>'drukarnia@booklet.pl', 'role'=>'web', 'password' => 'not-important', 'password_confirmation' => 'not-important']);
        }
    }

    /**
    * Return false in current user is null
    * @return
    */
    public function returnFalseIfNullUser()
    {
        if ($this->current_user == null) {
            return false;
        }
    }
}
