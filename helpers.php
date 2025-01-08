<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function base_path($path = ''): string {
    return __DIR__.'/'.$path;
}

/**
 * Load a view
 * 
 * @param string $name
 * @param array $data
 * @return void
 */
function load_view($name, $data = []): void {
	$view = base_path("App/views/{$name}.php");

	if (!file_exists($view)) {
		die("View not found: {$name}");
	}

	extract($data);
	require $view;
}

/**
 * Load a partial view
 * 
 * @param string $name
 * @param array $data
 * @return void
 */
function load_partial($name, $data = []): void {
	$partial = base_path("App/views/partials/{$name}.php");

	if (!file_exists($partial)) {
		die("Partial not found: {$name}");
	}

	extract($data);
	require $partial;
}

/**
 * Inspect a variable
 * 
 * @param mixed $variable
 * @return void
 */
function inspect($variable): void {
	echo "<pre>";
	var_dump($variable);
	echo "</pre>";
}

/**
 * Inspect a variable and die
 * 
 * @param mixed $variable
 * @return void
 */
function dd($variable): void {
	inspect($variable);
	die();
}

/**
 * Format salary
 * 
 * @param string $salary
 * @return string
 */
function format_salary($salary): string {
	return '$'.number_format(floatval($salary));
}

/**
 * Sanitize a value
 * 
 * @param string $value
 * @return string
 */
function sanitize($value): string {
	return filter_var(trim($value), FILTER_SANITIZE_SPECIAL_CHARS);
}

/**
 * Redirect to a URL
 * 
 * @param string $url
 * @return void
 */
function redirect($url): void {
	header("Location: {$url}");
	exit;
}