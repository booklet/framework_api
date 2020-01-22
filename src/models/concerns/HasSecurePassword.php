<?php
trait HasSecurePassword
{
    // require password_digest in model field
    // when create obcject with password, this required two fields: password and password_confirmation
    // then valid, and encrypt password to save in database password_digest field

    public function specialPropertis()
    {
        return ['password', 'password_confirmation'];
    }

    public function setSecurePassword()
    {
        $errors = [];

        if (!isset($this->password)) {
            $errors[] = 'password required.';
        }

        if (!isset($this->password_confirmation)) {
            $errors[] = 'confirmation doesn\'t match.';
        }

        if (empty($this->password)) {
            $errors[] = 'cannot be blank.';
        }

        if (empty($this->password_confirmation)) {
            $errors[] = 'confirmation cannot be blank.';
        }

        if (isset($this->password) && isset($this->password_confirmation) && ($this->password == $this->password_confirmation)) {
            $this->password_digest = StringUntils::encryptPassword($this->password);
        } else {
            $errors[] = 'confirmation doesn\'t match.';
        }

        // save errors in password_digest?
        // TODO reproject this
        if (!empty($errors)) {
            $this->password_digest = 'error|' . implode('|', $errors);
        }

        unset($this->password);
        unset($this->password_confirmation);
    }
}
