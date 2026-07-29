<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Outlet;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::today()->endOfDay();
        $outletId = $request->outlet_id;

        $outletsQuery = Outlet::query();
        if ($user->role === 'karyawan') {
            $outletsQuery->where('id', $user->outlet_id);
        } elseif ($user->role === 'manager') {
            $outletsQuery->where('owner_id', $user->id);
            if ($outletId) $outletsQuery->where('id', $outletId);
        } else {
            if ($outletId) $outletsQuery->where('id', $outletId);
        }
        $allowedOutletIds = $outletsQuery->pluck('id');

        $expenses = Expense::with(['user:id,name', 'outlet:id,name'])
            ->whereIn('outlet_id', $allowedOutletIds)
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at')
            ->get();

        $summary = [
            'total_amount' => $expenses->sum('amount')
        ];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $expenses
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $validated['user_id'] = auth()->id();

        $expense = Expense::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dicatat',
            'data' => $expense
        ]);
    }

    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus'
        ]);
    }
}
