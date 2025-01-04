<?php

namespace App\Controllers;

use Framework\Database;

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
}