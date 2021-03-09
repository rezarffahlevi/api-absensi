<?php

use Config\Services;
/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */
function view(string $name, array $data = [], array $options = []): string
{
    /**
     * @var CodeIgniter\View\View $renderer
     */
    $renderer = Services::renderer();

    $saveData = config(View::class)->saveData;

    if (array_key_exists('saveData', $options))
    {
        $saveData = (bool) $options['saveData'];
        unset($options['saveData']);
    }
    
    $name = env('theme.name'). "/{$name}";

    return $renderer->setData($data, 'raw')
                    ->render($name, $options, $saveData);
}
    
/**
 * Grabs the page title
 *
 * @return string
 * added by rahmat1929 08.10.2020
 */
function getTitle(): string
{
    $session    = session();
    $title      = $session->get('title');

    $titles     = [env('site.name')];
    
    if (!empty($title)) {
        $titles[] = ucfirst(trim($title));
    }
    
    return @implode(' :: ', $titles);
}

/**
 * Set the page title
 * 
 * @param string $title
 *
 * @return string
 * added by rahmat1929 08.10.2020
 */
function setTitle(string $title = ''): string
{
    $session        = session();
    $uri            = Services::uri();
    
    if (!empty($title)) {
        $session->set('title', $title);
    }

    return true;
}


/**
 * Return the base URL to use in views
 * 
 * @param string $title
 *
 * @return string
 * added by rahmat1929 08.10.2020
 */
function asset_url($uri = '', string $protocol = null): string
{
    $newuri = 'assets/'. env('theme.name') . "/{$uri}";

    if (preg_match('/plugins/', $uri)) {
        $newuri = "assets/{$uri}";
    }

    return base_url($newuri, $protocol);
}

/**
 * Set the page title
 * 
 * @param string $title
 *
 * @return string
 * added by rahmat1929 08.10.2020
 */
function saveImage(string $data = ''): int
{
    $result = 0;
    if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
        $data = substr($data, strpos($data, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            $result = -1;
            // throw new \Exception('invalid image type');
        }
        $data = str_replace( ' ', '+', $data );
        $data = base64_decode($data);

        if ($data === false) {
            $result = -2;
            // throw new \Exception('base64_decode failed');
        }
    } else {
        $result = -3;
        // throw new \Exception('did not match data URI with image data');
    }

    $result = file_put_contents(WRITEPATH."uploads/img.{$type}", $data);

    return $result;
}

/**
 * Return string error message
 * 
 * @param array $errors
 *
 * @return string
 * added by rahmat1929 26.11.2020
 */
function parseErrorValidation($errors): string
{
    $error_message  = "";
    $last_error     = "";
    $errors         = array_values($errors);

    if (count($errors) > 1) {
        $last_error     = " dan {$errors[count($errors)-1]}";
        unset($errors[count($errors)-1]);
        $error_message  = @implode(', ', $errors) . $last_error;
    } else {
        $error_message  = @implode(', ', $errors);
    }

    return $error_message;
}