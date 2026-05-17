<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Notification $notification)
    {
        $service = new NotificationService();
        $service->markAsRead($notification, Auth::user());

        return back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    public function markAllAsRead()
    {
        $service = new NotificationService();
        $service->markAllAsRead(Auth::user());

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    public function unreadCount()
    {
        $service = new NotificationService();
        $count = $service->unreadCount(Auth::user());

        return response()->json(['count' => $count]);
    }

    public function destroy($id)
    {
        $notification = Notification::where('id', $id)->where('user_id', Auth::id())->first();

        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notifikasi berhasil dihapus.');
        }

        return back()->with('error', 'Notifikasi tidak ditemukan.');
    }

    // Method baru: Hapus semua notifikasi
    public function deleteAll()
    {
        Notification::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Semua notifikasi berhasil dihapus.');
    }
}
