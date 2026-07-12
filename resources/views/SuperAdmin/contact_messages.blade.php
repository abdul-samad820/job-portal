@extends('layouts.superadmin')
@section('title', 'Contact Messages')

@section('content')

<div class="container-fluid py-4">

    @include('partials.superadmin-page-header', [
        'icon' => 'fa-envelope',
        'title' => 'Contact Messages',
        'subtitle' => 'Messages submitted through the public "Get In Touch" form.',
        'badge' => $unreadCount > 0 ? ['text' => $unreadCount.' new', 'class' => 'badge-danger'] : null,
    ])

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>From</th>
                        <th>Subject</th>
                        <th>Received</th>
                        <th style="width:70px" class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Same 4-color palette already used elsewhere on the SuperAdmin
                        // side (Reports doughnut / stat chips) — picked per-contact from
                        // their name so the same person always gets the same color.
                        $sa_avatarPalette = ['blue', 'green', 'amber', 'red'];
                    @endphp
                    @forelse($messages as $msg)
                    @php
                        $sa_initials = collect(preg_split('/\s+/', trim($msg->name)))
                            ->filter()
                            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->take(2)
                            ->implode('');
                        $sa_color = $sa_avatarPalette[ord(mb_strtoupper(mb_substr($msg->name, 0, 1))) % count($sa_avatarPalette)];
                    @endphp
                    <tr class="{{ $msg->read_at ? '' : 'font-weight-bold' }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="sa-contact-avatar sa-contact-avatar-{{ $sa_color }} mr-3">
                                    {{ $sa_initials ?: '?' }}
                                    @if(!$msg->read_at)
                                        <span class="sa-contact-avatar-dot"></span>
                                    @endif
                                </span>
                                <div>
                                    {{ $msg->name }}
                                    <br><small class="text-muted font-weight-normal">{{ $msg->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <a href="#" class="view-message" data-id="{{ $msg->id }}" data-toggle="modal" data-target="#msgModal">
                                {{ $msg->subject }}
                            </a>
                        </td>
                        <td>{{ $msg->created_at->diffForHumans() }}</td>
                        <td class="text-right">
                            <div class="sa-row-actions justify-content-end">
                                <form action="{{ route('superadmin.contact.destroy', $msg->id) }}" method="POST" class="d-inline-flex"
                                      onsubmit="return confirm('Delete this message?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger sa-icon-action" title="Delete"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="p-0">
                        <div class="sa-empty-state">
                            <i class="fas fa-envelope-open"></i>
                            <p>No messages yet.</p>
                        </div>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </div>

    <div class="mt-3">{{ $messages->links() }}</div>

</div>

<!-- Message detail modal -->
<div class="modal fade" id="msgModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="msgSubject"></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>From:</strong> <span id="msgFrom"></span> (<span id="msgEmail"></span>)</p>
                <p id="msgBody" style="white-space: pre-wrap;"></p>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.view-message').forEach(function (link) {
    link.addEventListener('click', function (e) {
        e.preventDefault();
        const id = this.dataset.id;
        fetch('/superadmin/contact-messages/' + id)
            .then(res => res.json())
            .then(data => {
                document.getElementById('msgSubject').textContent = data.subject;
                document.getElementById('msgFrom').textContent = data.name;
                document.getElementById('msgEmail').textContent = data.email;
                document.getElementById('msgBody').textContent = data.message;
            });
    });
});
</script>
@endpush