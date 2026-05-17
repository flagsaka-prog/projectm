<div id="online-users" style="border:1px solid #ddd; border-radius:6px; padding:12px; max-width:300px;">
    <h3 style="margin:0 0 10px 0">🟢 User Online</h3>
    <ul id="online-list" style="list-style:none; padding:0; margin:0">
        <li>Memuat...</li>
    </ul>
</div>

<script>
    function loadOnlineUsers() {
        fetch("{{ route('presence.online') }}")
            .then(res => res.json())
            .then(users => {
                const list = document.getElementById('online-list');

                if (users.length === 0) {
                    list.innerHTML = '<li style="color:#999">Tidak ada user online.</li>';
                    return;
                }

                list.innerHTML = users.map(user => `
                <li style="padding:6px 0; border-bottom:1px solid #eee">
                    <span style="color:green">●</span>
                    <strong>${user.name}</strong>
                    <small style="color:#666">(${user.role})</small>
                    <br>
                    <small style="color:#999">${user.last_seen}</small>
                </li>
            `).join('');
            })
            .catch(() => {
                document.getElementById('online-list').innerHTML = '<li style="color:red">Gagal memuat.</li>';
            });
    }

    // Load pertama kali
    loadOnlineUsers();

    // Refresh setiap 30 detik
    setInterval(loadOnlineUsers, 30000);
</script>
