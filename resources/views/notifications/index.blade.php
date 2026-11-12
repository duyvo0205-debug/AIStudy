
@extends('layouts.app')

@section('title', 'Thông báo - AIStudy')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">Thông báo</h3>
            <p class="text-muted mb-0">
                Cập nhật hoạt động và đề xuất học tập của bạn.
            </p>
        </div>

        @if($unreadCount > 0)
            <form action="{{ route('notifications.read-all') }}" method="POST">
                @csrf
                @method('PATCH')

                <button type="submit" class="btn btn-outline-primary">
                    Đánh dấu tất cả đã đọc
                </button>
            </form>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            @forelse($notifications as $notification)
                <div class="d-flex align-items-start justify-content-between gap-3 p-3 border-bottom
                    {{ $notification->read_at ? 'bg-white' : 'bg-light' }}">

                    <div class="d-flex gap-3">
                        <div class="rounded-circle bg-primary-subtle text-primary
                                    d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;flex-shrink:0;">
                            <i class="bi bi-bell fs-5"></i>
                        </div>

                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <strong>
                                    {{ $notification->data['title'] ?? 'Thông báo mới' }}
                                </strong>

                                @if(!$notification->read_at)
                                    <span class="badge bg-primary">Mới</span>
                                @endif
                            </div>

                            <p class="text-muted mb-1 mt-1">
                                {{ $notification->data['message'] ?? '' }}
                            </p>

                            <small class="text-muted">
                                {{ $notification->created_at->format('H:i - d/m/Y') }}
                            </small>

                            @if(!empty($notification->data['url']))
                                <div class="mt-2">
                                    <a href="{{ $notification->data['url'] }}"
                                       class="text-decoration-none">
                                        Xem chi tiết
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="d-flex gap-2 flex-shrink-0">
                        @if(!$notification->read_at)
                            <form action="{{ route('notifications.read', $notification->id) }}"
                                  method="POST">
                                @csrf
                                @method('PATCH')

                                <button type="submit"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Đánh dấu đã đọc">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        @endif

                        <form action="{{ route('notifications.destroy', $notification->id) }}"
                              method="POST"
                              onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Xóa thông báo">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>

                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted" style="font-size:42px;"></i>
                    <h5 class="mt-3">Chưa có thông báo</h5>
                    <p class="text-muted mb-0">
                        Những cập nhật học tập của bạn sẽ xuất hiện tại đây.
                    </p>
                </div>
            @endforelse

        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>
@endsection
