<?php

namespace App\Http\Controllers;

use App\Models\ComplaintAction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function indexForCitizen(Request $request): View
    {
        $actions = ComplaintAction::query()
            ->with('complaint')
            ->whereHas('complaint', fn ($query) => $query->where('reporter_id', $request->user()->id))
            ->where(function ($query) {
                $query->whereNotNull('notes')
                    ->orWhere('action_type', 'like', 'decision_%');
            })
            ->latest()
            ->paginate(12);

        return view('notifications.citizen', compact('actions'));
    }

    public function indexForUnit(Request $request): View
    {
        $actions = ComplaintAction::query()
            ->with('complaint')
            ->whereHas('complaint', fn ($query) => $query->where('assigned_unit_id', $request->user()->id))
            ->where(function ($query) {
                $query->where('action_type', 'decision_accepted')
                    ->orWhereIn('action_type', ['in_progress', 'resolved']);
            })
            ->latest()
            ->paginate(12);

        return view('notifications.unit', compact('actions'));
    }
}
