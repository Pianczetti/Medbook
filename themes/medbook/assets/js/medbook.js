/**
 * MedBook - Platforma Medyczna
 * Front-office interactivity: slot picker, AJAX doctor search, booking flow
 */
(function () {
  'use strict';

  var MedBook = {
    init: function () {
      this.initNavToggle();
      this.initSlotPicker();
      this.initDoctorSearch();
      this.initBookingFlow();
      this.initDashboardTabs();
    },

    /* Mobile navigation toggle */
    initNavToggle: function () {
      var toggle = document.querySelector('.medbook-nav-toggle');
      var nav = document.querySelector('.medbook-nav');
      if (toggle && nav) {
        toggle.addEventListener('click', function () {
          nav.classList.toggle('medbook-nav--open');
        });
      }
    },

    /* Slot picker - calendar/time selection */
    initSlotPicker: function () {
      var picker = document.querySelector('.slot-picker');
      if (!picker) return;

      var days = picker.querySelectorAll('.slot-picker__day:not(.slot-picker__day--disabled)');
      var times = picker.querySelectorAll('.slot-picker__time');
      var dateInput = document.querySelector('input[name="booking_date"]');
      var timeInput = document.querySelector('input[name="time_start"]');

      days.forEach(function (day) {
        day.addEventListener('click', function () {
          days.forEach(function (d) { d.classList.remove('slot-picker__day--selected'); });
          this.classList.add('slot-picker__day--selected');
          if (dateInput) {
            dateInput.value = this.getAttribute('data-date');
          }
          MedBook.loadTimeSlots(picker, this.getAttribute('data-date'));
        });
      });

      times.forEach(function (time) {
        time.addEventListener('click', function () {
          times.forEach(function (t) { t.classList.remove('slot-picker__time--selected'); });
          this.classList.add('slot-picker__time--selected');
          if (timeInput) {
            timeInput.value = this.getAttribute('data-time');
          }
        });
      });
    },

    /* AJAX slot loading */
    loadTimeSlots: function (picker, date) {
      var resourceId = picker.getAttribute('data-resource-id');
      var slotsUrl = picker.getAttribute('data-slots-url');
      var timesContainer = picker.querySelector('.slot-picker__times');

      if (!resourceId || !slotsUrl || !timesContainer) return;

      timesContainer.innerHTML = '<div class="medbook-text-center medbook-text-muted">Ladowanie...</div>';

      var url = slotsUrl + '?resource_id=' + resourceId + '&date=' + date;

      fetch(url)
        .then(function (response) { return response.json(); })
        .then(function (data) {
          if (data.success && data.slots.length > 0) {
            var html = '';
            data.slots.forEach(function (slot) {
              html += '<div class="slot-picker__time" data-time="' + slot.time + '">';
              html += slot.time;
              if (slot.formatted_price) {
                html += '<br><small>' + slot.formatted_price + '</small>';
              }
              html += '</div>';
            });
            timesContainer.innerHTML = html;
            MedBook.initSlotPicker();
          } else {
            timesContainer.innerHTML = '<div class="medbook-text-center medbook-text-muted">Brak dostepnych terminow</div>';
          }
        })
        .catch(function () {
          timesContainer.innerHTML = '<div class="medbook-text-center medbook-text-muted">Blad ladowania terminow</div>';
        });
    },

    /* Doctor search with AJAX */
    initDoctorSearch: function () {
      var form = document.querySelector('.medbook-search-form');
      if (!form) return;

      form.addEventListener('submit', function (e) {
        var resultsContainer = document.querySelector('.doctor-search-results');
        if (!resultsContainer) return;

        // Allow normal form submission for server-side search
      });
    },

    /* Multi-step booking flow */
    initBookingFlow: function () {
      var steps = document.querySelectorAll('.booking-flow__step');
      if (steps.length === 0) return;

      var nextBtns = document.querySelectorAll('[data-booking-next]');
      var prevBtns = document.querySelectorAll('[data-booking-prev]');

      nextBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var current = document.querySelector('.booking-flow__step--active');
          var next = current ? current.nextElementSibling : null;
          if (current && next && next.classList.contains('booking-flow__step')) {
            current.classList.remove('booking-flow__step--active');
            next.classList.add('booking-flow__step--active');
            MedBook.updateStepIndicator(next.getAttribute('data-step'));
          }
        });
      });

      prevBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
          var current = document.querySelector('.booking-flow__step--active');
          var prev = current ? current.previousElementSibling : null;
          if (current && prev && prev.classList.contains('booking-flow__step')) {
            current.classList.remove('booking-flow__step--active');
            prev.classList.add('booking-flow__step--active');
            MedBook.updateStepIndicator(prev.getAttribute('data-step'));
          }
        });
      });
    },

    updateStepIndicator: function (stepNumber) {
      var indicators = document.querySelectorAll('.booking-steps__step');
      indicators.forEach(function (indicator, index) {
        indicator.classList.remove('booking-steps__step--active', 'booking-steps__step--completed');
        if (index + 1 < parseInt(stepNumber)) {
          indicator.classList.add('booking-steps__step--completed');
        } else if (index + 1 === parseInt(stepNumber)) {
          indicator.classList.add('booking-steps__step--active');
        }
      });
    },

    /* Dashboard tabs */
    initDashboardTabs: function () {
      var tabs = document.querySelectorAll('.dashboard-tabs__tab');
      if (tabs.length === 0) return;

      tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
          var target = this.getAttribute('data-tab');
          tabs.forEach(function (t) { t.classList.remove('dashboard-tabs__tab--active'); });
          this.classList.add('dashboard-tabs__tab--active');

          document.querySelectorAll('.dashboard-panel').forEach(function (panel) {
            panel.classList.add('medbook-hidden');
          });
          var targetPanel = document.querySelector('.dashboard-panel[data-panel="' + target + '"]');
          if (targetPanel) {
            targetPanel.classList.remove('medbook-hidden');
          }
        });
      });
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { MedBook.init(); });
  } else {
    MedBook.init();
  }
})();
