<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintAction;
use App\Models\ComplaintAttachment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebugController extends Controller
{
    /**
     * Delete all complaints and related data.
     */
    public function deleteAllComplaints(Request $request): RedirectResponse
    {
        if (!config('app.debug')) {
            return back()->with('error', 'Fitur debug dinonaktifkan.');
        }

        if (!$request->user()->isAdmin()) {
            abort(403);
        }

        DB::transaction(function () {
            // Delete attachments (ideally we should also delete files from storage)
            ComplaintAttachment::query()->delete();
            
            // Delete actions
            ComplaintAction::query()->delete();
            
            // Delete complaints
            Complaint::query()->delete();
        });

        return back()->with('status', 'Seluruh data laporan berhasil dihapus.');
    }
}
