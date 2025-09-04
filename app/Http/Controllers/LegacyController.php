<?php

namespace App\Http\Controllers;

use App\Checks;
use App\Facades\twentyfouronlineConfig;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use twentyfouronline\Util\Debug;

class LegacyController extends Controller
{
    public function index(Request $request, Session $session)
    {
        Checks::postAuth();

        // Set variables
        $no_refresh = false;
        $init_modules = ['web', 'auth'];
        require base_path('/includes/init.php');

        Debug::set(Str::contains($request->path(), 'debug'));

        ob_start(); // protect against bad plugins that output during start
        \twentyfouronline\Plugins::start();
        ob_end_clean();

        if (Str::contains($request->path(), 'widescreen=yes')) {
            $session->put('widescreen', 1);
        }
        if (Str::contains($request->path(), 'widescreen=no')) {
            $session->forget('widescreen');
        }

        // Load the settings for Multi-Tenancy.
        if (twentyfouronlineConfig::has('branding') && is_array(twentyfouronlineConfig::get('branding'))) {
            $branding = Arr::dot(twentyfouronlineConfig::get('branding.' . $request->server('SERVER_NAME'), twentyfouronlineConfig::get('branding.default')));
            foreach ($branding as $key => $value) {
                twentyfouronlineConfig::set($key, $value);
            }
        }

        // page_title_prefix is displayed, unless page_title is set FIXME: NEEDED?
        if (twentyfouronlineConfig::has('page_title')) {
            twentyfouronlineConfig::set('page_title_prefix', twentyfouronlineConfig::get('page_title'));
        }

        // render page
        ob_start();
        $vars['page'] = basename($vars['page'] ?? '');
        if ($vars['page'] && is_file('includes/html/pages/' . $vars['page'] . '.inc.php')) {
            require 'includes/html/pages/' . $vars['page'] . '.inc.php';
        } else {
            abort(404);
        }

        $html = ob_get_clean();
        ob_end_clean();

        if (isset($pagetitle) && is_array($pagetitle)) {
            // if prefix is set, put it in front
            if (twentyfouronlineConfig::get('page_title_prefix')) {
                array_unshift($pagetitle, twentyfouronlineConfig::get('page_title_prefix'));
            }

            // if suffix is set, put it in the back
            if (twentyfouronlineConfig::get('page_title_suffix')) {
                $pagetitle[] = twentyfouronlineConfig::get('page_title_suffix');
            }

            // create and set the title
            $title = implode(' - ', $pagetitle);
            $html .= "<script type=\"text/javascript\">\ndocument.title = '$title';\n</script>";
        }

        return response()->view('layouts.legacy_page', [
            'content' => $html,
            'refresh' => $no_refresh ? 0 : twentyfouronlineConfig::get('page_refresh'),
        ]);
    }

    public function dummy()
    {
        return 'Dummy page';
    }
}




