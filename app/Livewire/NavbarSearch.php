<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class NavbarSearch extends Component
{
    public $query = '';
    public $suggestions = [];

    public function updatedQuery()
    {
        if (strlen($this->query) >= 3) {
            $this->suggestions = Product::active()
                ->where('name', 'like', '%' . $this->query . '%')
                ->with(['category', 'primaryImage'])
                ->limit(5)
                ->get()
                ->all();
        } else {
            $this->suggestions = [];
        }
    }

    public function submitSearch()
    {
        if (strlen($this->query) >= 3) {
            return redirect()->route('catalog', ['search' => $this->query]);
        }
    }

    public function selectProduct($name)
    {
        $this->query = $name;
        return redirect()->route('catalog', ['search' => $name]);
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->suggestions = [];
    }

    public function render()
    {
        return view('livewire.navbar-search');
    }
}
