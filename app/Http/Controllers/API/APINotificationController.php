<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use function Laravel\Prompts\error;

class APINotificationController extends Controller
{
    
    public function getAll() {
        $user = Auth::user();
        $notifications = Notification::select(['id', 'subject', 'title', 'created_at', 'is_read'])
            ->where('receiver_id', $user->id);
        $has_any_read = $notifications->where('is_read', true)->first() != null;
        $notifications = $notifications
            ->orderByDesc('created_at')
            ->get();
        return response()->json([
            'has_any_read' => $has_any_read,
            'notifications' => $notifications
        ]);
    }

    public function get($id) {
        $notification = Notification::find(Crypt::decryptString($id));
        if (!$notification) return error('notifikasi tidak ditemukan');
        return response()->json($notification);
    }

    public function send(Request $request) {
        $user = Auth::user();
        $request->validate([
            'receiver_id' => 'required|numeric',
            'subject' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'links' => 'array',
            'links.*.name' => 'required|string',
            'links.*.url' => 'required|string'
        ], [
            'receiver_id.required' => 'penerima tidak boleh kosong',
            'subject.required' => 'subjek tidak boleh kosong',
            'title.required' => 'judul tidak boleh kosong',
            'message.required' => 'pesan tidak boleh kosong',
            'links.*.name.required' => 'nama tautan tidak boleh kosong',
            'links.*.url.required' => 'alamat tautan tidak boleh kosong',
        ]);
        $notification = new Notification();
        $notification->sender_id = $user->id;
        $notification->receiver_id = $request->receiver_id;
        $notification->subject = $request->subject;
        $notification->title = $request->title;
        $notification->message = $request->message;
        $notification->links()->attach($request->links);
        $notification->save();
        if (!$notification->id) return error('Internal server error');
        return response()->json($notification);
    }

    public function read($id) {
        $notification = Notification::find(Crypt::decryptString($id));
        if (!$notification) return error('notifikasi tidak ditemukan');
        $notification->update(['is_read' => true]);
        return response()->json(['message' => 'notifikasi telah dibaca']);
    }

    public function delete($id) {
        $notification = Notification::find(Crypt::decryptString($id));
        if (!$notification) return error('notifikasi tidak ditemukan');
        $notification->update(['is_deleted' => true]);
        return response()->json(['message' => 'notifikasi berhasil dihapus']);
    }

    public function deleteAllRead() {
        $user = Auth::user();
        Notification::where('receiver_id', $user->id)
            ->where('is_read', true)
            ->update(['is_deleted' => true]);
        return response()->json(['message' => 'notifikasi terbaca berhasil dihapus']);
    }

}
