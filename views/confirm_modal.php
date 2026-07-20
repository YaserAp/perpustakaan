<!-- REUSABLE CUSTOM CONFIRMATION MODAL -->
<div id="modalConfirm" class="modal-overlay">
    <div class="modal-content" style="max-width: 420px; text-align: center; padding: 28px 24px;">
        <div id="modalConfirmIconWrapper" style="width: 58px; height: 58px; border-radius: 50%; background: var(--primary-light); color: var(--primary); display: inline-flex; align-items: center; justify-content: center; font-size: 26px; margin: 0 auto 16px auto; box-shadow: var(--shadow-sm);">
            <i id="modalConfirmIcon" class="fa-solid fa-circle-question"></i>
        </div>
        
        <h3 id="modalConfirmTitle" style="font-size: 17px; font-weight: 600; margin-bottom: 8px; color: var(--text-main);">Konfirmasi Tindakan</h3>
        <p id="modalConfirmMessage" style="font-size: 13.5px; color: var(--text-muted); margin-bottom: 24px; line-height: 1.5;"></p>
        
        <div style="display: flex; gap: 12px;">
            <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="closeModal('modalConfirm')">
                <i class="fa-solid fa-xmark"></i> Batal
            </button>
            <a id="modalConfirmBtn" href="#" class="btn btn-primary" style="flex: 1.2; justify-content: center; text-decoration: none;">
                <i class="fa-solid fa-check"></i> Ya, Lanjutkan
            </a>
        </div>
    </div>
</div>

<script>
function bukaKonfirmasi(url, pesan, judul = 'Konfirmasi Tindakan', type = 'primary', iconClass = '') {
    const titleEl = document.getElementById('modalConfirmTitle');
    const msgEl = document.getElementById('modalConfirmMessage');
    const btnEl = document.getElementById('modalConfirmBtn');
    const iconEl = document.getElementById('modalConfirmIcon');
    const iconWrap = document.getElementById('modalConfirmIconWrapper');

    if (titleEl) titleEl.innerText = judul;
    if (msgEl) msgEl.innerText = pesan;
    
    if (btnEl) {
        btnEl.href = url;
        btnEl.className = 'btn btn-' + type;
    }

    if (iconEl && iconWrap) {
        if (iconClass) {
            iconEl.className = iconClass;
        } else if (type === 'danger') {
            iconWrap.style.background = 'var(--danger-bg)';
            iconWrap.style.color = 'var(--danger)';
            iconEl.className = 'fa-solid fa-triangle-exclamation';
        } else if (type === 'success') {
            iconWrap.style.background = 'var(--success-bg)';
            iconWrap.style.color = 'var(--success)';
            iconEl.className = 'fa-solid fa-rotate-left';
        } else {
            iconWrap.style.background = 'var(--primary-light)';
            iconWrap.style.color = 'var(--primary)';
            iconEl.className = 'fa-solid fa-circle-question';
        }
    }

    openModal('modalConfirm');
    return false;
}
</script>
