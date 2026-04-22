// Vehicle Service Booking System — main.js
// Author: Ananya | Enrollment: 02214803123

function showAlert(id, type, message) {
    const el = document.getElementById(id);
    if (!el) return;
    el.className = 'alert alert-' + type;
    el.textContent = message;
    el.style.display = 'block';
    setTimeout(() => { el.style.display = 'none'; }, 4000);
}

function ajaxPost(url, data, callback) {
    const form = new FormData();
    for (const key in data) form.append(key, data[key]);
    fetch(url, { method: 'POST', body: form })
        .then(res => res.json())
        .then(callback)
        .catch(() => callback({ status: 'error', message: 'Network error.' }));
}

// ── Register ──
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = {
            name: this.name.value,
            email: this.email.value,
            password: this.password.value,
            phone: this.phone.value
        };
        ajaxPost('php/register.php', data, function (res) {
            showAlert('regAlert', res.status === 'success' ? 'success' : 'error', res.message);
            if (res.status === 'success') setTimeout(() => window.location.href = 'index.php', 1500);
        });
    });
}

// ── Login ──
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
        e.preventDefault();
        ajaxPost('php/login.php', { email: this.email.value, password: this.password.value }, function (res) {
            if (res.status === 'success') {
                window.location.href = res.redirect;
            } else {
                showAlert('loginAlert', 'error', res.message);
            }
        });
    });
}

// ── Add Vehicle ──
const vehicleForm = document.getElementById('vehicleForm');
if (vehicleForm) {
    vehicleForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = {
            vehicle_name: this.vehicle_name.value,
            vehicle_number: this.vehicle_number.value,
            vehicle_type: this.vehicle_type.value,
            brand: this.brand.value,
            model: this.model.value,
            year: this.year.value
        };
        ajaxPost('php/add_vehicle.php', data, function (res) {
            showAlert('vehicleAlert', res.status, res.message);
            if (res.status === 'success') { vehicleForm.reset(); setTimeout(() => location.reload(), 1500); }
        });
    });
}

// ── Book Appointment ──
const bookForm = document.getElementById('bookForm');
if (bookForm) {
    // Load available slots
    fetch('php/get_slots.php')
        .then(r => r.json())
        .then(slots => {
            const sel = document.getElementById('slot_id');
            if (!sel) return;
            slots.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.id;
                opt.textContent = s.slot_date + ' at ' + s.slot_time.slice(0, 5);
                sel.appendChild(opt);
            });
        });

    bookForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const data = {
            vehicle_id: this.vehicle_id.value,
            slot_id: this.slot_id.value,
            service_type: this.service_type.value,
            description: this.description.value
        };
        ajaxPost('php/book_appointment.php', data, function (res) {
            showAlert('bookAlert', res.status === 'success' ? 'success' : 'error', res.message);
            if (res.status === 'success') setTimeout(() => location.reload(), 1500);
        });
    });
}

// ── Cancel Appointment ──
function cancelAppointment(id) {
    if (!confirm('Cancel this appointment? The slot will be released.')) return;
    ajaxPost('php/cancel_appointment.php', { appointment_id: id }, function (res) {
        alert(res.message);
        if (res.status === 'success') location.reload();
    });
}

// ── Admin: Update Status ──
function updateStatus(id) {
    const status  = document.getElementById('status_' + id).value;
    const cost    = document.getElementById('cost_' + id) ? document.getElementById('cost_' + id).value : '0';
    const remarks = document.getElementById('remarks_' + id) ? document.getElementById('remarks_' + id).value : '';
    ajaxPost('php/update_status.php', { appointment_id: id, status, cost, remarks }, function (res) {
        alert(res.message);
        if (res.status === 'success') location.reload();
    });
}
