<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;

class ExpenseForm extends Component
{
    public $amount;
    public $description;
    public $expense_date;

    protected $rules = [
        'amount'        => 'required|numeric|min:0',
        'description'   => 'required|string|max:255',
        'expense_date'  => 'required|date',
    ];

    public function submit()
    {
        $this->validate();

        Expense::create([
            'user_id'       => Auth::id(),      // integer ID from users table
            'amount'        => $this->amount,
            'description'   => $this->description,
            'expense_date'  => $this->expense_date,
        ]);

        $this->reset(['amount', 'description', 'expense_date']);
        session()->flash('message', 'Expense created successfully.');
    }

    public function render()
    {
        return view('livewire.expense-form');
    }
}