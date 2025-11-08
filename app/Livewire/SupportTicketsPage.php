<?php

namespace App\Livewire;

use App\Models\SupportTicket;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SupportTicketsPage extends Component
{
    public $subject = '';
    public $message = '';
    public $showCreateForm = false;
    public $selectedTicket = null;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:2000',
    ];

    protected $messages = [
        'subject.required' => 'موضوع تیکت الزامی است',
        'subject.max' => 'موضوع نباید بیشتر از 255 کاراکتر باشد',
        'message.required' => 'متن تیکت الزامی است',
        'message.max' => 'متن تیکت نباید بیشتر از 2000 کاراکتر باشد',
    ];

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->reset(['subject', 'message']);
            $this->resetValidation();
        }
    }

    public function createTicket()
    {
        $this->validate();

        // ایجاد تیکت
        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'ticket_number' => SupportTicket::generateTicketNumber(),
            'subject' => $this->subject,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        session()->flash('success', 'تیکت شما با موفقیت ثبت شد. شماره تیکت: ' . $ticket->ticket_number);
        
        $this->reset(['subject', 'message', 'showCreateForm']);
        $this->resetValidation();
    }

    public function viewTicket($ticketId)
    {
        $this->selectedTicket = SupportTicket::where('user_id', Auth::id())
            ->findOrFail($ticketId);
    }

    public function closeTicketView()
    {
        $this->selectedTicket = null;
    }

    public function render()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.support-tickets-page', [
            'tickets' => $tickets,
        ])->layout('layouts.app');
    }
}
