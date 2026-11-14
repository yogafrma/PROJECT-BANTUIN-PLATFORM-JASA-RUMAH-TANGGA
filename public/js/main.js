/**
 * Main JavaScript File
 * Project: Teknisi Rumah Tangga
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Smooth scrolling for anchor links
    const scrollLinks = document.querySelectorAll('a[href^="#"]');
    scrollLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId && targetId !== '#') {
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // 2. Quick Demo Credentials Auto-Fill on Login Page
    const demoButtons = document.querySelectorAll('.btn-demo-fill');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    if (demoButtons.length > 0 && emailInput && passwordInput) {
        demoButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const email = this.getAttribute('data-email');
                const pass = this.getAttribute('data-pass');

                if (email) emailInput.value = email;
                if (pass) passwordInput.value = pass;

                // Visual flash effect on inputs
                emailInput.focus();
                emailInput.classList.add('border-primary');
                passwordInput.classList.add('border-primary');
                setTimeout(() => {
                    emailInput.classList.remove('border-primary');
                    passwordInput.classList.remove('border-primary');
                }, 800);
            });
        });
    }

    // 3. Interactive Role Selector on Register Page
    const roleCards = document.querySelectorAll('.role-selector-card');
    roleCards.forEach(card => {
        card.addEventListener('click', function () {
            roleCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const radio = this.querySelector('.role-radio');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // 4. In-App Notification System (Bell Dropdown & Polling)
    const notifWrapper = document.getElementById('notificationDropdownWrapper');
    if (notifWrapper && window.CURRENT_USER) {
        const notifBadge = document.getElementById('notif-badge');
        const notifBellIcon = document.getElementById('notif-bell-icon');
        const notifHeaderCount = document.getElementById('notif-header-count');
        const notifListContainer = document.getElementById('notif-dropdown-list');
        const btnMarkAllRead = document.getElementById('btn-mark-all-read');
        const notifDropdownBtn = document.getElementById('notifDropdownBtn');

        const baseUrl = (window.BASE_URL || '').replace(/\/+$/, '');

        function getNotifIcon(type) {
            switch (type) {
                case 'new_post':
                    return '<span class="avatar-circle bg-primary-subtle text-primary p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-file-earmark-plus-fill fs-6"></i></span>';
                case 'new_response':
                    return '<span class="avatar-circle bg-info-subtle text-info p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-person-check-fill fs-6"></i></span>';
                case 'response_selected':
                    return '<span class="avatar-circle bg-success-subtle text-success p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-check-circle-fill fs-6"></i></span>';
                default:
                    return '<span class="avatar-circle bg-warning-subtle text-warning p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;"><i class="bi bi-bell-fill fs-6"></i></span>';
            }
        }

        async function fetchNotifications() {
            try {
                const response = await fetch(baseUrl + '/notification/list', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;

                const data = await response.json();
                if (!data.success) return;

                const unread = parseInt(data.unread || 0, 10);
                const items = data.items || [];

                // Update badge and icon
                if (unread > 0) {
                    notifBadge.textContent = unread > 99 ? '99+' : unread;
                    notifBadge.classList.remove('d-none');
                    if (notifBellIcon) {
                        notifBellIcon.classList.remove('text-secondary');
                        notifBellIcon.classList.add('text-primary');
                    }
                    if (notifHeaderCount) {
                        notifHeaderCount.textContent = unread + ' Baru';
                    }
                } else {
                    notifBadge.classList.add('d-none');
                    if (notifBellIcon) {
                        notifBellIcon.classList.remove('text-primary');
                        notifBellIcon.classList.add('text-secondary');
                    }
                    if (notifHeaderCount) {
                        notifHeaderCount.textContent = '0 Baru';
                    }
                }

                // Render items in dropdown list
                if (items.length === 0) {
                    notifListContainer.innerHTML = `
                        <div class="p-4 text-center text-muted small">
                            <i class="bi bi-bell-slash fs-3 d-block text-secondary opacity-50 mb-2"></i>
                            Belum ada notifikasi
                        </div>
                    `;
                    return;
                }

                let html = '';
                items.forEach(item => {
                    const unreadBg = !item.is_read ? 'background-color: #f0f7ff;' : '';
                    const unreadDot = !item.is_read ? '<span class="position-absolute top-50 end-0 translate-middle-y me-3 p-1 bg-primary rounded-circle" style="width: 8px; height: 8px;" title="Belum dibaca"></span>' : '';
                    const targetLink = item.link ? (baseUrl + '/' + item.link.replace(/^\/+/, '')) : '#';

                    html += `
                        <a href="${targetLink}" class="list-group-item list-group-item-action p-3 border-bottom position-relative notif-item ${!item.is_read ? 'fw-medium' : ''}" 
                           data-id="${item.id}" 
                           data-read="${item.is_read ? '1' : '0'}"
                           style="${unreadBg} transition: background-color 0.2s;">
                            <div class="d-flex align-items-start gap-3 pe-3">
                                <div class="flex-shrink-0 mt-1">
                                    ${getNotifIcon(item.type)}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="small fw-bold text-dark text-truncate">${item.title}</div>
                                    <div class="small text-muted text-break mt-1" style="font-size: 0.8rem; line-height: 1.35;">${item.message}</div>
                                    <div class="text-secondary extra-small mt-1" style="font-size: 0.72rem;">
                                        <i class="bi bi-clock me-1"></i>${item.time_ago || 'Baru saja'}
                                    </div>
                                </div>
                            </div>
                            ${unreadDot}
                        </a>
                    `;
                });

                notifListContainer.innerHTML = html;

                // Attach click handler to each item to mark as read
                notifListContainer.querySelectorAll('.notif-item').forEach(el => {
                    el.addEventListener('click', async function (e) {
                        const notifId = this.getAttribute('data-id');
                        const isRead = this.getAttribute('data-read') === '1';
                        if (!isRead && notifId) {
                            try {
                                await fetch(baseUrl + '/notification/read/' + notifId, {
                                    method: 'POST',
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                });
                            } catch (err) {}
                        }
                    });
                });

            } catch (err) {
                // Silently ignore network errors during background polling
            }
        }

        // Mark All As Read button click handler
        if (btnMarkAllRead) {
            btnMarkAllRead.addEventListener('click', async function (e) {
                e.preventDefault();
                try {
                    const res = await fetch(baseUrl + '/notification/readall', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (res.ok) {
                        fetchNotifications();
                    }
                } catch (err) {}
            });
        }

        // Re-fetch on dropdown open
        if (notifDropdownBtn) {
            notifDropdownBtn.addEventListener('show.bs.dropdown', () => {
                fetchNotifications();
            });
        }

        // Initial fetch & start 12s polling
        fetchNotifications();
        setInterval(fetchNotifications, 12000);
    }

    // 5. Modern Custom Confirmation & Alert Pop-Up System (SweetAlert2)
    // Upgrades all browser native "localhost says confirm(...)" into modern branded modals
    window.bantuinConfirm = function(options) {
        if (typeof options === 'string') {
            options = { text: options };
        }
        const {
            title = 'Konfirmasi Tindakan',
            text = '',
            html = '',
            icon = 'question',
            confirmBtnText = 'Ya, Setujui',
            cancelBtnText = 'Batal',
            isDanger = false,
            isSuccess = false
        } = options;

        let confirmBtnClass = 'btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm';
        if (isDanger) {
            confirmBtnClass = 'btn btn-danger rounded-pill px-4 py-2.5 fw-bold shadow-sm';
        } else if (isSuccess) {
            confirmBtnClass = 'btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm';
        }

        if (typeof Swal === 'undefined') {
            return Promise.resolve(window.confirm(text || title));
        }

        return Swal.fire({
            title: title,
            text: html ? undefined : text,
            html: html || undefined,
            icon: icon,
            showCancelButton: true,
            confirmButtonText: confirmBtnText,
            cancelButtonText: cancelBtnText,
            reverseButtons: true,
            focusConfirm: !isDanger,
            focusCancel: isDanger,
            backdrop: 'rgba(7, 30, 61, 0.45)',
            customClass: {
                popup: 'bantuin-swal-popup rounded-4 shadow-lg border-0 p-4',
                title: 'fw-bold text-dark fs-5 mt-2',
                htmlContainer: 'text-secondary small mt-2 mb-4 lh-base',
                actions: 'gap-2 mt-2',
                confirmButton: confirmBtnClass,
                cancelButton: 'btn btn-light border rounded-pill px-4 py-2.5 fw-semibold text-secondary'
            },
            buttonsStyling: false
        }).then(result => result.isConfirmed);
    };

    window.bantuinAlert = function(options) {
        if (typeof options === 'string') {
            options = { text: options };
        }
        const {
            title = 'Pemberitahuan',
            text = '',
            html = '',
            icon = 'info',
            btnText = 'Mengerti',
            isSuccess = false,
            isDanger = false
        } = options;

        let btnClass = 'btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm';
        if (isDanger) btnClass = 'btn btn-danger rounded-pill px-4 py-2.5 fw-bold shadow-sm';
        else if (isSuccess) btnClass = 'btn btn-success rounded-pill px-4 py-2.5 fw-bold shadow-sm';

        if (typeof Swal === 'undefined') {
            window.alert(text || title);
            return Promise.resolve();
        }

        return Swal.fire({
            title: title,
            text: html ? undefined : text,
            html: html || undefined,
            icon: icon,
            confirmButtonText: btnText,
            backdrop: 'rgba(7, 30, 61, 0.45)',
            customClass: {
                popup: 'bantuin-swal-popup rounded-4 shadow-lg border-0 p-4',
                title: 'fw-bold text-dark fs-5 mt-2',
                htmlContainer: 'text-secondary small mt-2 mb-4 lh-base',
                confirmButton: btnClass
            },
            buttonsStyling: false
        });
    };

    function initModernConfirmModals() {
        // Auto-convert any legacy inline onclick="return confirm('...')" elements
        document.querySelectorAll('[onclick*="confirm("]').forEach(el => {
            const onclickAttr = el.getAttribute('onclick');
            if (!onclickAttr) return;

            const match = onclickAttr.match(/confirm\(['"]([^'"]+)['"]\)/);
            if (match && match[1]) {
                el.removeAttribute('onclick');
                el.classList.add('btn-confirm-action');
                el.setAttribute('data-confirm-text', match[1]);

                const textLower = match[1].toLowerCase();
                if (textLower.includes('tawarkan') || textLower.includes('ajukan') || textLower.includes('bantuan')) {
                    el.setAttribute('data-confirm-title', 'Tawarkan Bantuan?');
                    el.setAttribute('data-confirm-btn', 'Ya, Saya Bisa Membantu');
                    el.setAttribute('data-confirm-icon', 'question');
                    el.setAttribute('data-confirm-color', '#2a9d8f');
                } else if (textLower.includes('pilih') || textLower.includes('konfirmasi')) {
                    el.setAttribute('data-confirm-title', 'Konfirmasi Pilihan Teknisi?');
                    el.setAttribute('data-confirm-btn', 'Ya, Pilih Teknisi Ini');
                    el.setAttribute('data-confirm-icon', 'question');
                    el.setAttribute('data-confirm-color', '#003049');
                } else if (textLower.includes('hapus') || textLower.includes('nonaktif') || textLower.includes('batal')) {
                    el.setAttribute('data-confirm-title', 'Konfirmasi Tindakan');
                    el.setAttribute('data-confirm-danger', 'true');
                    el.setAttribute('data-confirm-btn', 'Ya, Lanjutkan');
                    el.setAttribute('data-confirm-icon', 'warning');
                }
            }
        });

        // Global event delegation for .btn-confirm-action and [data-confirm]
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('.btn-confirm-action, [data-confirm]');
            if (!trigger) return;

            e.preventDefault();
            e.stopPropagation();

            const title = trigger.getAttribute('data-confirm-title') || 'Konfirmasi Tindakan';
            const text = trigger.getAttribute('data-confirm-text') || trigger.getAttribute('data-confirm') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const icon = trigger.getAttribute('data-confirm-icon') || 'question';
            const confirmBtnText = trigger.getAttribute('data-confirm-btn') || 'Ya, Lanjutkan';
            const cancelBtnText = trigger.getAttribute('data-cancel-btn') || 'Batal';
            const isDanger = trigger.classList.contains('btn-danger') || trigger.getAttribute('data-confirm-danger') === 'true';

            window.bantuinConfirm({
                title: title,
                text: text,
                icon: icon,
                confirmBtnText: `<i class="bi bi-check-circle-fill me-1"></i> ${confirmBtnText}`,
                cancelBtnText: `<i class="bi bi-x-circle me-1"></i> ${cancelBtnText}`,
                isDanger: isDanger
            }).then((confirmed) => {
                if (confirmed) {
                    const form = trigger.closest('form');
                    if (form) {
                        form.submit();
                    } else if (trigger.tagName === 'A' && trigger.href) {
                        window.location.href = trigger.href;
                    }
                }
            });
        }, true);
    }

    initModernConfirmModals();

    // Console information message
    console.info('Teknisi Rumah Tangga - Native PHP MVC & Auth initialized.');
});
