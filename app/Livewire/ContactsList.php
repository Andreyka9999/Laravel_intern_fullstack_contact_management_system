<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;

class ContactsList extends Component
{
    public $contacts;
    public $showModal = false;
    public $editing = false;
    public $confirmingDelete = false;
    public $newContact = [
        'name' => '',
        'email' => '',
        'phone' => '',
    ];
    public $contact_id;
    public $deleteContactId = null;

    protected $rules = [
        'newContact.name' => 'required|string|max:255',
        'newContact.email' => 'required|email|max:255',
        'newContact.phone' => 'required|string|max:15',
    ];

    public function mount()
    {
        $this->contacts = Auth::user()->contacts;
    }

    public function addContact()
    {
        $this->validate();

        $this->newContact['user_id'] = Auth::id();

        Contact::updateOrCreate(['id' => $this->contact_id], $this->newContact);

        $this->contacts = Auth::user()->contacts()->get();
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $contact = Contact::findOrFail($id);
        $this->contact_id = $id;
        $this->newContact = [
            'name' => $contact->name,
            'email' => $contact->email,
            'phone' => $contact->phone,
        ];

        $this->openModal();
        $this->editing = true;
    }

    public function deleteContact()
    {
        Contact::findOrFail($this->deleteContactId)->delete();

        $this->contacts = Auth::user()->contacts()->get();
        $this->confirmingDelete = false;
        $this->deleteContactId = null;  
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editing = false;
    }

    private function resetInputFields()
    {
        $this->newContact = [
            'name' => '',
            'email' => '',
            'phone' => '',
        ];
        $this->contact_id = null;
    }

    public function confirmDelete($id)
    {
        $this->deleteContactId = $id;
        $this->confirmingDelete = true;
    }

    public function render()
    {
        return view('livewire.contacts-list', ['contacts' => $this->contacts]);
    }
}
