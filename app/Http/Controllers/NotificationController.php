<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, string $account, string $role, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $url = data_get($notification->data, 'url', '#');
        
        // If it's a dummy/internal URL, we might need to reconstruct it 
        // but for now we assume it's a valid relative or absolute URL.
        if (str_contains($url, 'notifikasi')) {
             $url = route('complaints.show', [
                'account' => $account,
                'role' => $role,
                'complaint' => data_get($notification->data, 'complaint_id', 0) ?: basename($url)
             ]);
        }

        return redirect($url);
    }

    public function destroy(Request $request, string $account, string $role, string $id): RedirectResponse
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->delete();

        return back()->with('status', 'Notifikasi berhasil dihapus.');
    }

    public function clearAll(Request $request): RedirectResponse
    {
        $request->user()->notifications()->delete();
        return back()->with('status', 'Semua notifikasi berhasil dibersihkan.');
    }
}
