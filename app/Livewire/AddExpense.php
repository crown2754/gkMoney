<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AddExpense extends Component
{
    public $date;
    public $amount_cents;
    public $currency = 'USD';
    public $category_id;
    public $description = '';
    public $receipt_image_url = '';

    protected $rules = [
        'date' => ['required', 'date', 'before_or_equal:tomorrow'],
        'amount_cents' => ['required', 'integer', 'min:1', 'max:999999999'],
        'currency' => ['required', 'size:3', 'upper'],
        'category_id' => ['required', 'uuid', Rule::exists('expense_categories', 'id')->where('user_id', Auth::id())],
        'description' => ['nullable', 'string', 'max:250'],
        'receipt_image_url' => ['nullable', 'url', 'max:500'],
    ];

    public function mount()
    {
        $this->date = today()->toDateString();
    }

    public function save()
    {
        $this->validate();

        Expense::create([
            'user_id' => Auth::id(),
            'date' => $this->date,
            'amount_cents' => $this->amount_cents,
            'currency' => $this->currency,
            'category_id' => $this->category_id,
            'description' => $this->description,
            'receipt_image_url' => $this->receipt_image_url,
        ]);

        session()->flash('message', 'Expense saved.');
        $this->emitTo('expense-list', 'refresh');
        $this->reset(['amount_cents', 'category_id', 'description', 'receipt_image_url']);
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function render()
    {
        $categories = ExpenseCategory::where('user_id', Auth::id())
            ->get(['id', 'name', 'icon', 'color']);

        return view('livewire.add-expense', compact('categories'));
    }
}