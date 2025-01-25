<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 */
function basePath($path = ''): string {
    return __DIR__.'/'.$path;
}

/**
 * Load a view
 * 
 * @param string $name
 * @param array $data
 * @return void
 */
function loadView($name, $data = []): void {
	$view = basePath("App/views/{$name}.php");

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
function loadPartial($name, $data = []): void {
	$partial = basePath("App/views/partials/{$name}.php");

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
function formatSalary($salary): string {
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