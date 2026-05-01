<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;
        $statusFilter = $request->string('status')->toString();

        $stats = [
            'total' => Complaint::query()
                ->when($role === User::ROLE_MASYARAKAT, fn($q) => $q->where('reporter_id', $user->id))
                ->when($role === User::ROLE_INSTANSI, fn($q) => $q->where('assigned_unit_id', $user->id))
                ->count(),
            'submitted' => Complaint::query()
                ->where('status', Complaint::STATUS_SUBMITTED)
                ->when($role === User::ROLE_MASYARAKAT, fn($q) => $q->where('reporter_id', $user->id))
                ->when($role === User::ROLE_INSTANSI, fn($q) => $q->where('assigned_unit_id', $user->id))
                ->count(),
            'in_progress' => Complaint::query()
                ->where('status', Complaint::STATUS_IN_PROGRESS)
                ->when($role === User::ROLE_MASYARAKAT, fn($q) => $q->where('reporter_id', $user->id))
                ->when($role === User::ROLE_INSTANSI, fn($q) => $q->where('assigned_unit_id', $user->id))
                ->count(),
            'resolved' => Complaint::query()
                ->where('status', Complaint::STATUS_RESOLVED)
                ->when($role === User::ROLE_MASYARAKAT, fn($q) => $q->where('reporter_id', $user->id))
                ->when($role === User::ROLE_INSTANSI, fn($q) => $q->where('assigned_unit_id', $user->id))
                ->count(),
        ];

        $recentComplaints = Complaint::query()
            ->when($role === User::ROLE_MASYARAKAT, fn($q) => $q->where('reporter_id', $user->id))
            ->when($role === User::ROLE_INSTANSI, fn($q) => $q->where('assigned_unit_id', $user->id))
            ->when($statusFilter !== '', fn($q) => $q->where('status', $statusFilter))
            ->latest()
            ->limit(2)
            ->get();

        return view('dashboard', compact('stats', 'recentComplaints', 'role', 'statusFilter'));
    }
}
