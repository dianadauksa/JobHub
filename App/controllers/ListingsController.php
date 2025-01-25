<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;
use Framework\Authorization;

class ListingsController {
    protected $db;

    public function __construct() {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    /**
     * Display all listings
     * 
     * @return void
     */
    public function index(): void {
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();

        loadView('listings/index', [
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
            ErrorController::notFound('Listing not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Search listings by keywords and location
     * 
     * @return void
     */
    public function search(): void {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $listings = $this->db->query('SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)', [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ])->fetchAll();

        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }

    /**
     * Display the create listing form
     * 
     * @return void
     */
    public function create(): void {
        loadView('listings/create');
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
        $new_listing['user_id'] = Session::get('user')['id'];
        $new_listing = array_map('sanitize', $new_listing);

        $errors = [];

        foreach ($required_fields as $field) {
            if (empty($new_listing[$field]) || !Validation::string($new_listing[$field])) {
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
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

            Session::setFlash('success_message', 'Listing created successfully');

            redirect('/listings');
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
            ErrorController::notFound('Listing not found');
            return;
        }

        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to delete this listing');
            redirect('/listings/'.$listing->id);
            return;
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', [
            'id' => $id
        ]);

        Session::setFlash('success_message', 'Listing deleted successfully');
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
            ErrorController::notFound('Listing not found');
            return;
        }

        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to edit this listing');
            redirect('/listings/'.$listing->id);
            return;
        }

        loadView('listings/edit', [
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
            ErrorController::notFound('Listing not found');
            return;
        }

        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlash('error_message', 'You are not authorized to edit this listing');
            redirect('/listings/'.$listing->id);
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
            loadView('listings/edit', [
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

            Session::setFlash('success_message', 'Listing updated successfully');

            redirect("/listings/{$id}");
        }
    }
}
