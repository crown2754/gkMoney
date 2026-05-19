<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use App\Models\User;
use App\Models\Account;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class AddExpense extends Component
{
    // Form fields
    public $amount;
    public $currency = 'USD';
    public $expense_date;
    public $description = '';
    public $category_id;
    public $tags = []; // array of tag IDs
    public $account_id;
    public $receipt_id; // UUID of pre‑uploaded receipt
    public $metadata = [];

    // Validation rules
    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'currency' => ['required', 'string', 'size:3', Rule::in(['USD','EUR','GBP','JPY','CAD'])],
        'expense_date' => ['required', 'date', 'after_or_equal:today', 'before:today +8 days'],
        'description' => 'nullable|string|max:255',
        'category_id' => ['nullable', 'uuid', Rule::exists('expense_categories','id')->whereNull('deleted_at')],
        'tags' => 'array',
        'tags.*' => ['uuid', Rule::exists('expense_tags','id')
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')],
        'account_id' => ['nullable', 'uuid', Rule::exists('accounts','id')
            ->where('user_id', Auth::id())],
        'receipt_id' => ['nullable', 'uuid', Rule::exists('expense_receipts','id')],
        'metadata' => 'nullable|array',
    ];

    public function mount()
    {
        $this->expense_date = today()->toDateString();
        $user = Auth::user();
        if ($user) {
            $defaultAccount = $user->accounts()->first();
            $this->account_id = $defaultAccount?->id ?? null;
        }
    }

    public function save()
    {
        $this->validate();

        $expense = Expense::create([
            'id' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'account_id' => $this->account_id,
            'amount_cents' => (int) round($this->amount * 100),
            'currency' => $this->currency,
            'expense_date' => $this->expense_date,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'is_recurring' => false,
            'receipt_id' => $this->receipt_id,
            'external_id' => null,
            'metadata' => $this->metadata,
        ]);

        if (!empty($this->tags)) {
            $expense->tags()->attach($this->tags);
        }

        Session::flash('message', 'Expense added successfully.');
        return redirect()->route('expenses.index'); // adjust route as needed
    }

    public function render()
    {
        $categories = ExpenseCategory::whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $accounts = Auth::user() ? Auth::user()->accounts()->get() : collect();

        $tags = Auth::user() ? Auth::user()->tags()
            ->where('is_active', true)
            ->get() : collect();

        return view('livewire.add-expense', compact('categories', 'accounts', 'tags'));
    }
}