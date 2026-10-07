// ISCOM Mentoring Booking System - Interactive UI Helper

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Menu Toggle
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileNav = document.getElementById('mobileNav');
  if (mobileMenuBtn && mobileNav) {
    mobileMenuBtn.addEventListener('click', function () {
      mobileNav.classList.toggle('open');
    });
  }

  // 2. Interactive Schedule Card Selection (Step 2)
  const scheduleItems = document.querySelectorAll('.schedule-select-item');
  const scheduleInput = document.getElementById('selected_schedule_id');
  const btnNextSchedule = document.getElementById('btnNextSchedule');

  if (scheduleItems.length > 0 && scheduleInput) {
    scheduleItems.forEach(item => {
      item.addEventListener('click', function () {
        const scheduleId = this.getAttribute('data-schedule-id');
        const remaining = parseInt(this.getAttribute('data-remaining') || '0');

        if (remaining <= 0) return; // Cannot select full schedule

        scheduleItems.forEach(el => {
          el.classList.remove('selected');
          const btn = el.querySelector('.action-select-btn');
          if (btn) {
            btn.innerHTML = '<span>Pilih Jadwal</span>';
            btn.className = 'btn btn-outline btn-sm action-select-btn';
          }
        });

        this.classList.add('selected');
        scheduleInput.value = scheduleId;

        const currentBtn = this.querySelector('.action-select-btn');
        if (currentBtn) {
          currentBtn.innerHTML = '<span class="material-symbols-outlined text-[16px]">check</span><span>Terpilih</span>';
          currentBtn.className = 'btn btn-blue btn-sm action-select-btn';
        }

        if (btnNextSchedule) {
          btnNextSchedule.removeAttribute('disabled');
        }
      });
    });
  }

  // 3. Status Re-check Animation
  const btnRecheck = document.getElementById('btn-recheck');
  const refreshIcon = document.getElementById('refresh-icon');
  if (btnRecheck && refreshIcon) {
    btnRecheck.addEventListener('click', function () {
      refreshIcon.style.animation = 'spin 0.8s linear infinite';
      btnRecheck.setAttribute('disabled', 'true');
      btnRecheck.style.opacity = '0.7';

      setTimeout(function () {
        window.location.reload();
      }, 800);
    });
  }
});
