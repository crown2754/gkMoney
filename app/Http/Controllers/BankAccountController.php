<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::where('user_id', auth()->id())->paginate(10);
        return view('bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'currency_id' => 'required|exists:currencies,id',
            'balance' => 'required|numeric',
            'alias' => 'required|string|max:255',
        ]);

        $account = BankAccount::create(array_merge($validated, ['user_id' => auth()->id()]));

        return redirect()->route('asset.bank-accounts.index')->with('success', '帳戶已建立');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'currency_id' => 'required|exists:currencies,id',
            'balance' => 'required|numeric',
            'alias' => 'required|string|max:255',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('asset.bank-accounts.index')->with('success', '帳戶已更新');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('asset.bank-accounts.index')->with('success', '帳戶已刪除');
    }
}