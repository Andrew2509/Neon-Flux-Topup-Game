<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    private function getDeviceView($name)
    {
        $device = (new CatalogController())->deviceType();
        $view = "{$device}.neonflux.calculators.{$name}";
        if (!view()->exists($view)) {
            $view = "desktop.neonflux.calculators.{$name}";
        }
        return $view;
    }

    public function index()
    {
        return view($this->getDeviceView('index'));
    }

    public function winrate()
    {
        return view($this->getDeviceView('winrate'));
    }

    public function magicwheel()
    {
        return view($this->getDeviceView('magicwheel'));
    }

    public function zodiac()
    {
        return view($this->getDeviceView('zodiac'));
    }
}
