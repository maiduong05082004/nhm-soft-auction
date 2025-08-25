<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SectionHome extends Component
{

    public $products;
    public $section;

    /**
     * Create a new component instance.
     */
    public function __construct($products, $section)
    {
        $this->section = $section;
        $this->products = $products; 
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.section-home');
    }
}
