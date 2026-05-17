<div style="display:inline-block; position:relative">
    <a href="{{ route('notifications.index') }}">
        🔔
        <span id="notif-count"
            style="
            display:none;
            position:absolute;
            top:-6px; right:-6px;
            background:red;
            color:white;
            font-size:11px;
            padding:2px 5px;
            border-radius:10px;
        ">0</span>
    </a>
</div>

<script>
    function loadNotifCount() {
        fetch("{{ route('notifications.unread-count') }}")
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('notif-count');
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline';
                } else {
                    badge.style.display = 'none';
                }
            });
    }

    loadNotifCount();
    setInterval(loadNotifCount, 30000);
</script>
