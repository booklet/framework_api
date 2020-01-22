<?php
class Authorize
{
    /**
     * The name of the authorize policies class.
     *
     * @var string
     */
    private $auth_class;

    /**
     * The name of the authorize policies action.
     *
     * @var string
     */
    private $auth_action;

    /**
     * User object in system.
     *
     * @var object
     */
    private $current_user;

    /**
     * @param string $method 'UserController::index'
     * @param object $user
     */
    public function __construct(string $method, $user, array $params = [])
    {




        if (strpos($method, '\\') !== false) {
            // Booklet\Warehouse\Controllers\PrintingTechnologiesController::index =>
            // Booklet\Warehouse\Policies\PrintingTechnologiesPolicies
            $this->auth_class = str_replace('Controllers', 'Policies', explode('::', $method)[0]);
            $this->auth_class = str_replace('Controller', 'Policies', $this->auth_class);
        } else {
            // 'UserController::index' => 'UserPolicies'
            $this->auth_class = str_replace('Controller', 'Policies', explode('::', $method)[0]);
        }







        // 'UserController::index' => 'index'
        $this->auth_action = explode('::', $method)[1];

        $this->current_user = $user;
    }

    /**
     * Load related police class and execute action.
     *
     * @return bool
     */
    public function auth($obj = null)
    {
        $police_class_instance = new $this->auth_class($this->current_user, $obj);

        return $police_class_instance->{$this->auth_action}();
    }
}
