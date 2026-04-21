<?php
// Livewire component - notes list with create and delete
// everything is here: state, validation, db calls - one class = one component

namespace App\Livewire;

use Livewire\Component;
use App\Models\Note;
use App\Models\Tag;
// facades call Larval service 
use Illuminate\Support\Facades\Auth;

/* CLASS */
class Notes extends Component
{
    // public props - component state, reactive, any change triggers re-render
    public $notes;
    public $text = '';
    public $tag_id = '';
    public $tags;

    // validation rules - server side, checked before any db write
    protected $rules = [
        'text' => 'required|string',
        'tag_id' => 'required|exists:tags,id',
    ];

    // listen for event from TagForm - inter-component communication via event bus
    protected $listeners = ['tagCreated' => 'refreshTags'];

    /* PUBLIC METHOD */
    /* mount */
    public function mount()
    {
        // mount = runs once on component load, initializes state
        $this->tags = Tag::all();
        $this->loadNotes();
    }

    /* PUBLIC METHOD */
    /* loadNotes */
    public function loadNotes()
    {
        // only get notes from logged in user - filter by user_id for security
        $this->notes = Note::with('tag')->where('user_id', Auth::id())->latest()->get();
    }

    /* PUBLIC METHOD */
    /* refreshTags */
    public function refreshTags()
    {
        // called when TagForm fires tagCreated event - refresh tag list
        $this->tags = \App\Models\Tag::all();
    }

    /* PUBLIC METHOD */
    /* save */
    public function save()
    {
        $this->validate();

        Note::create([
            'user_id' => Auth::id(),
            'tag_id' => $this->tag_id,
            'text' => $this->text,
        ]);

        // reset fields after save - clear state so form is empty again
        $this->text = '';
        $this->tag_id = '';

        $this->loadNotes();

        session()->flash('message', 'Note added.');
    }

    /* PUBLIC METHOD */
    /* delete */
    public function delete($noteId)
    {
        // double where - user can only delete his own note, not someone else
        Note::where('id', $noteId)->where('user_id', Auth::id())->delete();
        $this->loadNotes();
    }

    /* PUBLIC METHOD */
    /* render */
    public function render()
    {
        // return blade view - the template rendered for this component
        return view('livewire.notes');
    }
}
