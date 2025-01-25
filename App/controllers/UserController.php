<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController {
    protected $db;

    public function __construct() {
        $config = require base_path('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Show the registration form
     * 
     * @return void
     */
    public function create() {
        load_view('users/create');
    }

    /**
     * Show the login form
     * 
     * @return void
     */
    public function login() {
        load_view('users/login');
    }

    /**
     * Store a new user
     * 
     * @return void
     */
    public function store() {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $password = $_POST['password'];
        $password_confirmation = $_POST['password_confirmation'];

        $errors = [];

        if (!Validation::email($email)) {
            $errors['email'] = 'Please provide a valid email address';
        }

        if(!Validation::string($name, 2, 50)) {
            $errors['name'] = 'Name must be between 2 and 50 characters';
        }

        if (!Validation::string($password, 6, 72)) {
            $errors['password'] = 'Password must be between 6 and 72 characters';
        }

        if (!Validation::match($password, $password_confirmation)) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }

        if (!empty($errors)) {
            load_view('users/create', [
                'errors' => $errors,
                'user' => [
                    'name' => $name,
                    'email' => $email,
                    'city' => $city,
                    'state' => $state
                ]
            ]);
            exit;
        }

        // Check if user already exists
        $user = $this->db->query('SELECT * FROM users WHERE email = :email', [
            'email' => $email
        ])->fetch();

        if ($user) {
            $errors['email'] = 'User with this email already exists';
            load_view('users/create', [
                'errors' => $errors
            ]);
            exit;
        }

        $params = [
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state,
            'password' => password_hash($password, PASSWORD_BCRYPT)
        ];

        $this->db->query('INSERT INTO users (name, email, city, state, password)
            VALUES (:name, :email, :city, :state, :password)', $params
        );

        $user_id = $this->db->connection->lastInsertId();
        Session::set('user', [
            'id' => $user_id,
            'name' => $name,
            'email' => $email,
            'city' => $city,
            'state' => $state
        ]);

        redirect('/listings');
    }

    /**
     * Authenticate the user
     * 
     * @return void
     */
    public function authenticate() {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $errors = [];

        if (!Validation::email($email)) {
            $errors['email'] = 'Please provide a valid email address';
        }

        if (!Validation::string($password, 6, 72)) {
            $errors['password'] = 'Password must be between 6 and 72 characters';
        }

        if (!empty($errors)) {
            load_view('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        $user = $this->db->query('SELECT * FROM users WHERE email = :email', [
            'email' => $email
        ])->fetch();

        if (!$user) {
            $errors['email'] = 'Incorrect email or password';
            load_view('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        if (!password_verify($password, $user->password)) {
            $errors['password'] = 'Incorrect email or password';
            load_view('users/login', [
                'errors' => $errors
            ]);
            exit;
        }

        Session::set('user', [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'city' => $user->city,
            'state' => $user->state
        ]);

        redirect('/listings');
    }

    /**
     * Logout the user
     * 
     * @return void
     */
    public function logout() {
        Session::destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain']);
        redirect('/');
    }
}