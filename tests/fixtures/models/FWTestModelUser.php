<?php
class FWTestModelUser extends Model
{
    //    use UserRoles;
    use HasSecurePassword;

    const PAGINATION_PER_PAGE = 30;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setSecurePassword();
    }

    // for database queries (type) and for validation
    public function fields()
    {
        return [
            'id' => ['type' => 'integer'],
            'username' => ['type' => 'string', 'validations' => ['required', 'max_length:190']],
            'email' => ['type' => 'string', 'validations' => ['required', 'email']],
            'role' => ['type' => 'string', 'validations' => ['required']],
            'password_digest' => ['type' => 'string', 'validations' => ['required', 'password']],
//            'rebound_device_user_id' => ['type' => 'integer'],
            'created_at' => ['type' => 'datetime'],
            'updated_at' => ['type' => 'datetime'],
        ];
    }

    //    public function afterSave()
//    {
//
//    }
//
//    // return user last (if user has more that one) session token
//    public function sessionToken()
//    {
//        $session = Session::where("user_id = ?", ['user_id' => $this->id]);
//        if (empty($session)) {
//            die("User has no sessions");
//        }
//        return end($session)->token; # end => get last element of array
//    }
//
//    // create and return new user auth token
//    public function createToken()
//    {
//        $token = SessionTokenGenerator::generate();
//        // new sesion and encode token
//        $hashed_token = SessionTokenGenerator::hashToken($token);
//        $session = new Session(['user_id' => $this->id, 'token' => $hashed_token]);
//        if ($session->save()) {
//            return $token;
//        }
//    }
//
//    // return user if login and password are correct
//    public static function autorize($login, $password)
//    {
//        $user = User::findBy('email', $login);
//        $password_digest = StringUntils::encryptPassword($password);
//
//        if ($user && ($user->password_digest == $password_digest)) {
//            return $user;
//        } else {
//            return false;
//        }
//    }
//
}
