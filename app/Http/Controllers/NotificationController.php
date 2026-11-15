<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(15);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notif = Notification::findOrFail($id);
        $notif->update(['is_read' => true, 'read_at' => now()]);

        if ($notif->link) {
            return redirect($notif->link);
        }

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Notification::where('is_read', false)->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
