<?php

namespace LaravelMandra\Http\Controllers;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class Controller
 *
 * @package LaravelMandra\Http\Controllers
 */
class Controller extends BaseController
{
    public function index()
    {
        $implementations = [];

        foreach (require base_path('vendor/composer/autoload_classmap.php') as $class => $file) {
            if (strpos($class, 'App\\Mail') === 0) {
                $refl = new \ReflectionClass($class);

                if (!$refl->isInterface() && !$refl->isAbstract()) {
                    $implementations[] = $refl->newInstanceWithoutConstructor();
                }
            }
        }

        return view('mandra::index', ['mails' => $implementations]);
    }
}