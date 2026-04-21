<?php
// Livewire component - form to create a new tag
// dispatches event after save so Notes component can refresh its tag list

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tag;

/* CLASS */
class TagForm extends Component
{
    // public prop - two-way bound to input field, state updates on every keystroke
    public $name = '';

    // unique:tags,name - no duplicate tag name allowed
    protected $rules = [
        'name' => 'required|string|max:50|unique:tags,name',
    ];

    /* PUBLIC METHOD */
    /* save */
    public function save()
    {
        $this->validate();

        Tag::create(['name' => $this->name]);

        // reset = clear input field after save
        $this->reset('name');

        // dispatch event - sends a signal to other components listening for tagCreated
        $this->dispatch('tagCreated');

        session()->flash('message', 'Tag added!');
    }

    /* PUBLIC METHOD */
    /* render */
    public function render()
    {
        // return blade view - the template rendered for this component
        return view('livewire.tag-form');
    }
}
