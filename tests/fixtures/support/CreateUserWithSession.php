<?php
trait CreateUserWithSession
{
    public function createUserWithSession()
    {
        $user = UserFactory::user();
        $user->save();

        // create user session token
        $token = $user->createToken();
        return [$user, $token];
    }
}
