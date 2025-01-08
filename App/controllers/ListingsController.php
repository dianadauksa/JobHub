<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingsController {
    protected $db;

    public function __construct() {
        $config = require base_path('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Display all listings
     * 
     * @return void
     */
    public function index(): void {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();

        load_view('listings/index', [
            'listings' => $listings
        ]);
    }

    /**
     * Display a single listing
     * 
     * @param array $params
     * @return void
     */
    public function show($params): void {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        load_view('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Display the create listing form
     * 
     * @return void
     */
    public function create(): void {
        load_view('listings/create');
    }

    /**
     * Store a listing in DB
     * 
     * @return void
     */
    public function store(): void {
        $allowed_fields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email',
            'requirements', 'benefits', 'user_id'
        ];
        $required_fields = ['title', 'description', 'salary', 'city', 'state', 'email'];

        $new_listing = array_intersect_key($_POST, array_flip($allowed_fields));
        $new_listing['user_id'] = 1; // TODO: get user id from session
        $new_listing = array_map('sanitize', $new_listing);

        $errors = [];

        foreach ($required_fields as $field) {
            if (empty($new_listing[$field]) || !Validation::string($new_listing[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (!empty($errors)) {
            load_view('listings/create', [
                'errors' => $errors,
                'listing' => $new_listing
            ]);
        }
        else {
            $fields = [];
            foreach ($new_listing as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);

            $values = [];
            foreach ($new_listing as $field => $value) {
                if ($value === '') {
                    $new_listing[$field] = null;
                }
                $values[] = ":{$field}";
            }

            $values = implode(', ', $values);

            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
            $this->db->query($query, $new_listing);

            redirect('/');
        }
    }

    /**
     * Delete a listing from DB
     * 
     * @param array $params
     * @return void
     */
    public function destroy($params): void {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', [
            'id' => $id
        ]);

        $_SESSION['success_message'] = 'Listing deleted successfully';
        redirect('/listings');
    }

    /**
     * Display the edit listing form
     * 
     * @param array $params
     * @return void
     */
    public function edit($params): void {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        load_view('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * Update a listing in DB
     * 
     * @param array $params
     * @return void
     */
    public function update($params): void {
        $id = $params['id'] ?? '';

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', [
            'id' => $id
        ])->fetch();

        if (!$listing) {
            ErrorController::not_found('Listing not found');
            return;
        }

        $allowed_fields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email',
            'requirements', 'benefits', 'user_id'
        ];
        $required_fields = ['title', 'description', 'salary', 'city', 'state', 'email'];
        
        $updates = [];
        $updates = array_intersect_key($_POST, array_flip($allowed_fields));
        $updates = array_map('sanitize', $updates);

        $errors = [];

        foreach ($required_fields as $field) {
            if (empty($updates[$field]) || !Validation::string($updates[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (!empty($errors)) {
            load_view('listings/edit', [
                'errors' => $errors,
                'listing' => $listing
            ]);
            exit;
        }
        else {
            $update_fields = [];

            foreach (array_keys($updates) as $field) {
                $update_fields[] = "{$field} = :{$field}";
            }

            $update_fields = implode(', ', $update_fields);
            $update_query = "UPDATE listings SET {$update_fields} WHERE id = :id";
            $updates['id'] = $id;

            $this->db->query($update_query, $updates);

            $_SESSION['success_message'] = 'Listing updated successfully';

            redirect("/listings/{$id}");
        }
    }
}
