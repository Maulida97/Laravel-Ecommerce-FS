<?php

namespace App\Livewire;

use App\Models\Newsletter;
use Livewire\Component;

class NewsletterForm extends Component
{
    public $email = '';
    public $successMessage = '';

    protected $rules = [
        'email' => 'required|email|unique:newsletters,email',
    ];

    protected $messages = [
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email is already subscribed.',
    ];

    public function subscribe()
    {
        $this->validate();

        Newsletter::create([
            'email' => $this->email,
        ]);

        $this->email = '';
        $this->successMessage = 'Thank you for subscribing to our newsletter!';
        
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.newsletter-form');
    }
}
