<?php
class ExamplePolicies extends Policies
{
    public function index()
    {
        return true;
    }

    public function show()
    {
        return true;
    }

    public function create()
    {
        return true;
    }

    public function edit()
    {
        $this->returnFalseIfNullUser();

        // admin can edit all users
        if ($this->current_user->isAdmin()) {
            return true;
        }

        // user can edit only himself
        if ($this->current_user->id == $obj->id) {
            return true;
        }

        return false;
    }

    public function update()
    {
        return true;
    }

    public function destroy()
    {
        return true;
    }
}
