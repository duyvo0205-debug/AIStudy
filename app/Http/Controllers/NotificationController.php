<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Hiển thị danh sách thông báo.
     */
    public function index()
    {
        $user = auth()->user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);

        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact(
            'notifications',
            'unreadCount'
        ));
    }

    /**
     * Đánh dấu một thông báo đã đọc.
     */
    public function markAsRead(string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->back()->with(
            'success',
            'Đã đánh dấu thông báo là đã đọc.'
        );
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc.
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);

        return redirect()->back()->with(
            'success',
            'Đã đánh dấu tất cả thông báo là đã đọc.'
        );
    }

    /**
     * Xóa một thông báo.
     */
    public function destroy(string $id)
    {
        auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail()
            ->delete();

        return redirect()->back()->with(
            'success',
            'Đã xóa thông báo.'
        );
    }
}
