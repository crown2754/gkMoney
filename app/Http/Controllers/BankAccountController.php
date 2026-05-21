<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\Bank;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bankAccounts = BankAccount::with(['bank', 'currency'])
            ->where('user_id', Auth::id())
            ->orderBy('is_active', 'desc')
            ->orderBy('bank_id')
            ->get();

        return view('bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::where('is_active', true)->orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('bank-accounts.create', compact('banks', 'currencies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'currency_id' => 'required|exists:currencies,id',
            'alias' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active', true);

        BankAccount::create($validated);

        return redirect()->route('asset.bank-accounts.index')
            ->with('success', '銀行帳戶已新增');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);

        $banks = Bank::where('is_active', true)->orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('bank-accounts.edit', compact('bankAccount', 'banks', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $this->authorize('update', $bankAccount);

        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'currency_id' => 'required|exists:currencies,id',
            'alias' => 'required|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'balance' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);

        $bankAccount->update($validated);

        return redirect()->route('asset.bank-accounts.index')
            ->with('success', '銀行帳戶已更新');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount)
    {
        $this->authorize('delete', $bankAccount);

        $bankAccount->delete();

        return redirect()->route('asset.bank-accounts.index')
            ->with('success', '銀行帳戶已刪除');
    }
}