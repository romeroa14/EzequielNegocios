<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AdSenseBanner extends Component
{
    public $type;
    public $style;

    /**
     * Create a new component instance.
     */
    public function __construct($type = 'banner', $style = 'responsive')
    {
        $this->type = $type;
        $this->style = $style;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.ad-sense-banner');
    }
}
