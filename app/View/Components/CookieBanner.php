<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CookieBanner extends Component
{
    public $show;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->show = !session('cookies_accepted');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.cookie-banner');
    }
}
