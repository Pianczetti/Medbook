/**
 * MedBook Booking - Front Office JavaScript
 * Vanilla JS, no jQuery, no framework. Uses fetch() for AJAX.
 * Progressive enhancement - form works without JS.
 */
(function () {
    'use strict';

    var calendarEl = document.getElementById('snbooking-calendar');
    if (!calendarEl) {
        return;
    }

    var form = document.getElementById('snbooking-form');
    var calGrid = document.getElementById('snbooking-cal-grid');
    var calLabel = document.getElementById('snbooking-cal-label');
    var calPrev = document.getElementById('snbooking-cal-prev');
    var calNext = document.getElementById('snbooking-cal-next');
    var slotsGrid = document.getElementById('snbooking-slots-grid');
    var dateInput = document.getElementById('snbooking-selected-date');
    var bookingDateInput = document.getElementById('snbooking-booking-date');
    var timeEndInput = document.getElementById('snbooking-time-end');

    var currentMonth = parseInt(calendarEl.getAttribute('data-month'), 10);
    var currentYear = parseInt(calendarEl.getAttribute('data-year'), 10);
    var today = calendarEl.getAttribute('data-today');
    var maxDays = parseInt(calendarEl.getAttribute('data-max-days'), 10) || 30;

    // Config from form data attributes
    var cartbookingUrl = form ? form.getAttribute('data-cartbooking-url') : '';
    var cartIntegrationActive = form ? form.getAttribute('data-cart-integration') === '1' : false;
    var showPrices = form ? form.getAttribute('data-show-prices') === '1' : false;
    var secureKey = form ? (form.getAttribute('data-secure-key') || '') : '';

    var slotsUrl = '';
    var slotsUrlEl = form ? form.querySelector('[name="resource_id"]') : null;

    // Get AJAX URL from a data attribute or compute from form action
    var pageUrl = form ? form.getAttribute('action') : '';
    if (pageUrl) {
        slotsUrl = pageUrl.replace(/controller=booking/i, 'controller=slots');
        if (slotsUrl === pageUrl) {
            var baseUrl = pageUrl.split('?')[0];
            slotsUrl = baseUrl.replace(/\/booking\/?$/, '/slots');
        }
    }

    if (typeof prestashop !== 'undefined' && prestashop.modules && prestashop.modules.medbook_booking) {
        slotsUrl = prestashop.modules.medbook_booking.slots_url || slotsUrl;
    }

    if (!slotsUrl || slotsUrl === pageUrl) {
        var urlBase = window.location.origin + window.location.pathname;
        slotsUrl = urlBase + '?fc=module&module=medbook_booking&controller=slots';
    }

    var selectedDate = dateInput ? dateInput.value : '';
    var selectedResourceId = getSelectedResource();
    var selectedSlotPrice = 0;
    var resourceDuration = 0;

    var monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];

    init();

    function init() {
        renderCalendar();
        bindResourceSelection();
        bindSlotSelection();
        bindFormValidation();
        bindAddonSelection();
        bindCartSubmission();

        // Set initial resource duration
        updateResourceDuration();

        if (calPrev) {
            calPrev.addEventListener('click', function () {
                currentMonth--;
                if (currentMonth < 1) {
                    currentMonth = 12;
                    currentYear--;
                }
                renderCalendar();
            });
        }

        if (calNext) {
            calNext.addEventListener('click', function () {
                currentMonth++;
                if (currentMonth > 12) {
                    currentMonth = 1;
                    currentYear++;
                }
                renderCalendar();
            });
        }
    }

    function renderCalendar() {
        if (!calGrid || !calLabel) {
            return;
        }

        calLabel.textContent = monthNames[currentMonth - 1] + ' ' + currentYear;

        var existing = calGrid.querySelectorAll('.snbooking-calendar__day');
        for (var i = 0; i < existing.length; i++) {
            existing[i].remove();
        }

        var firstDay = new Date(currentYear, currentMonth - 1, 1);
        var lastDay = new Date(currentYear, currentMonth, 0);
        var startDow = firstDay.getDay() || 7;
        var daysInMonth = lastDay.getDate();

        var todayDate = new Date(today);
        var maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() + maxDays);

        for (var e = 1; e < startDow; e++) {
            var emptyCell = document.createElement('div');
            emptyCell.className = 'snbooking-calendar__day snbooking-calendar__day--empty';
            calGrid.appendChild(emptyCell);
        }

        for (var d = 1; d <= daysInMonth; d++) {
            var cellDate = new Date(currentYear, currentMonth - 1, d);
            var dateStr = formatDate(cellDate);
            var cell = document.createElement('button');
            cell.type = 'button';
            cell.className = 'snbooking-calendar__day';
            cell.textContent = d;
            cell.setAttribute('data-date', dateStr);

            var isPast = cellDate < todayDate && dateStr !== today;
            var isTooFar = cellDate > maxDate;

            if (isPast || isTooFar) {
                cell.className += ' snbooking-calendar__day--disabled';
            } else {
                cell.addEventListener('click', handleDateClick);
            }

            if (dateStr === today) {
                cell.className += ' snbooking-calendar__day--today';
            }

            if (dateStr === selectedDate) {
                cell.className += ' snbooking-calendar__day--selected';
            }

            calGrid.appendChild(cell);
        }
    }

    function handleDateClick(e) {
        var btn = e.currentTarget;
        if (btn.classList.contains('snbooking-calendar__day--disabled')) {
            return;
        }

        var prev = calGrid.querySelector('.snbooking-calendar__day--selected');
        if (prev) {
            prev.classList.remove('snbooking-calendar__day--selected');
        }

        btn.classList.add('snbooking-calendar__day--selected');
        selectedDate = btn.getAttribute('data-date');

        if (dateInput) {
            dateInput.value = selectedDate;
        }
        if (bookingDateInput) {
            bookingDateInput.value = selectedDate;
        }

        fetchSlots();
    }

    function bindResourceSelection() {
        if (!form) {
            return;
        }

        var radios = form.querySelectorAll('input[name="resource_id"]');
        for (var i = 0; i < radios.length; i++) {
            radios[i].addEventListener('change', function () {
                selectedResourceId = this.value;

                var cards = form.querySelectorAll('.snbooking-resource-card');
                for (var j = 0; j < cards.length; j++) {
                    cards[j].classList.remove('snbooking-resource-card--selected');
                }
                this.closest('.snbooking-resource-card').classList.add('snbooking-resource-card--selected');

                updateResourceDuration();

                if (selectedDate) {
                    fetchSlots();
                }
            });
        }
    }

    function updateResourceDuration() {
        if (!form) {
            return;
        }
        var checked = form.querySelector('input[name="resource_id"]:checked');
        if (checked) {
            resourceDuration = parseInt(checked.getAttribute('data-duration'), 10) || 0;
        }
    }

    function bindSlotSelection() {
        if (!slotsGrid) {
            return;
        }

        slotsGrid.addEventListener('change', function (e) {
            if (e.target && e.target.name === 'time_start') {
                var labels = slotsGrid.querySelectorAll('.snbooking-slot');
                for (var i = 0; i < labels.length; i++) {
                    labels[i].classList.remove('snbooking-slot--selected');
                }
                e.target.closest('.snbooking-slot').classList.add('snbooking-slot--selected');

                // Update time_end
                var timeStart = e.target.value;
                if (timeEndInput && resourceDuration) {
                    timeEndInput.value = calculateTimeEnd(timeStart, resourceDuration);
                }

                // Update price base from slot data
                var slotPrice = parseFloat(e.target.getAttribute('data-price')) || 0;
                selectedSlotPrice = slotPrice;
                updatePriceDisplay();
            }
        });
    }

    function bindAddonSelection() {
        if (!form) {
            return;
        }

        var addonCheckboxes = form.querySelectorAll('.snbooking-addon-card__input');
        for (var i = 0; i < addonCheckboxes.length; i++) {
            addonCheckboxes[i].addEventListener('change', function () {
                updatePriceDisplay();
            });
        }
    }

    function bindCartSubmission() {
        if (!form || !cartIntegrationActive || !cartbookingUrl) {
            return;
        }

        form.addEventListener('submit', function (e) {
            var errors = validateForm();
            if (errors.length > 0) {
                e.preventDefault();
                showClientErrors(errors);
                return;
            }

            e.preventDefault();

            var resourceId = getSelectedResource();
            var timeStart = form.querySelector('input[name="time_start"]:checked');
            var timeEnd = timeEndInput ? timeEndInput.value : '';

            // Collect addon IDs
            var addonIds = [];
            var addonCheckboxes = form.querySelectorAll('.snbooking-addon-card__input:checked');
            for (var i = 0; i < addonCheckboxes.length; i++) {
                addonIds.push(parseInt(addonCheckboxes[i].value, 10));
            }

            var body = new FormData();
            body.append('resource_id', resourceId);
            body.append('date', selectedDate);
            body.append('time_start', timeStart.value);
            body.append('time_end', timeEnd);
            body.append('addon_ids', JSON.stringify(addonIds));
            body.append('token', secureKey);

            var submitBtn = form.querySelector('.snbooking-submit__btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
            }

            fetch(cartbookingUrl, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: body
            })
                .then(function (response) { return response.json(); })
                .then(function (data) {
                    if (data.success && data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.textContent = 'Add to Cart';
                        }
                        showClientErrors([data.error || 'An error occurred.']);
                    }
                })
                .catch(function () {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Add to Cart';
                    }
                    showClientErrors(['Unable to add to cart. Please try again.']);
                });
        });
    }

    function bindFormValidation() {
        if (!form || cartIntegrationActive) {
            return;
        }

        form.addEventListener('submit', function (e) {
            var errors = validateForm();

            if (errors.length > 0) {
                e.preventDefault();
                showClientErrors(errors);
            }
        });
    }

    function validateForm() {
        var errors = [];
        var resourceId = getSelectedResource();
        var timeStart = form.querySelector('input[name="time_start"]:checked');
        var name = form.querySelector('input[name="customer_name"]');
        var email = form.querySelector('input[name="customer_email"]');

        if (!resourceId) {
            errors.push('Please select a service.');
        }
        if (!selectedDate) {
            errors.push('Please select a date.');
        }
        if (!timeStart) {
            errors.push('Please select a time slot.');
        }
        if (!name || !name.value.trim()) {
            errors.push('Please enter your name.');
        }
        if (!email || !email.value.trim() || !isValidEmail(email.value)) {
            errors.push('Please enter a valid email address.');
        }

        return errors;
    }

    function fetchSlots() {
        if (!selectedResourceId || !selectedDate || !slotsGrid) {
            return;
        }

        slotsGrid.innerHTML = '<p class="snbooking-slots__loading">Loading...</p>';

        var url = slotsUrl + (slotsUrl.indexOf('?') !== -1 ? '&' : '?') +
            'resource_id=' + encodeURIComponent(selectedResourceId) +
            '&date=' + encodeURIComponent(selectedDate) +
            '&ajax=1';

        fetch(url, {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data.resource && data.resource.duration_minutes) {
                    resourceDuration = data.resource.duration_minutes;
                }
                renderSlots(data.slots || []);
            })
            .catch(function () {
                slotsGrid.innerHTML = '<p class="snbooking-slots__empty">Unable to load slots. Please try again.</p>';
            });
    }

    function renderSlots(slots) {
        if (!slotsGrid) {
            return;
        }

        if (!slots.length) {
            slotsGrid.innerHTML = '<p class="snbooking-slots__empty">No available slots for this date.</p>';
            return;
        }

        var html = '';
        for (var i = 0; i < slots.length; i++) {
            var slot = slots[i];
            var time = typeof slot === 'object' ? slot.time : slot;
            var price = typeof slot === 'object' && slot.price !== undefined ? slot.price : 0;
            var formattedPrice = typeof slot === 'object' && slot.formatted_price ? slot.formatted_price : '';

            html += '<label class="snbooking-slot snbooking-slot--available">' +
                '<input type="radio" name="time_start" value="' + escapeHtml(time) + '" class="snbooking-slot__input" data-price="' + price + '" />' +
                '<span class="snbooking-slot__time">' + escapeHtml(time) + '</span>';

            if (showPrices && formattedPrice) {
                html += '<span class="snbooking-slot__price">' + escapeHtml(formattedPrice) + '</span>';
            }

            html += '</label>';
        }
        slotsGrid.innerHTML = html;
    }

    function updatePriceDisplay() {
        if (!showPrices) {
            return;
        }

        var baseEl = document.getElementById('snbooking-price-base');
        var addonsEl = document.getElementById('snbooking-price-addons');
        var totalEl = document.getElementById('snbooking-price-total');
        var depositEl = document.getElementById('snbooking-price-deposit');

        var addonsTotal = calculateAddonsTotal();

        if (baseEl && selectedSlotPrice > 0) {
            baseEl.textContent = selectedSlotPrice.toFixed(2);
        }

        if (addonsEl) {
            addonsEl.textContent = addonsTotal.toFixed(2);
        }

        var total = selectedSlotPrice + addonsTotal;
        if (totalEl && selectedSlotPrice > 0) {
            totalEl.textContent = total.toFixed(2);
        }

        if (depositEl && total > 0) {
            // Deposit is calculated server-side, show estimate from rule if available
            depositEl.textContent = total.toFixed(2);
        }
    }

    function calculateAddonsTotal() {
        if (!form) {
            return 0;
        }

        var total = 0;
        var checked = form.querySelectorAll('.snbooking-addon-card__input:checked');
        for (var i = 0; i < checked.length; i++) {
            total += parseFloat(checked[i].getAttribute('data-price')) || 0;
        }

        return total;
    }

    function calculateTimeEnd(timeStart, durationMinutes) {
        var parts = timeStart.split(':');
        var totalMinutes = (parseInt(parts[0], 10) * 60) + parseInt(parts[1], 10) + durationMinutes;
        var hours = Math.floor(totalMinutes / 60);
        var mins = totalMinutes % 60;

        return (hours < 10 ? '0' : '') + hours + ':' + (mins < 10 ? '0' : '') + mins;
    }

    function getSelectedResource() {
        if (!form) {
            return '';
        }
        var checked = form.querySelector('input[name="resource_id"]:checked');
        return checked ? checked.value : '';
    }

    function showClientErrors(errors) {
        var container = form.querySelector('.snbooking-errors');
        if (!container) {
            container = document.createElement('div');
            container.className = 'snbooking-errors';
            form.insertBefore(container, form.firstChild);
        }
        var html = '<ul class="snbooking-errors__list">';
        for (var i = 0; i < errors.length; i++) {
            html += '<li class="snbooking-errors__item">' + escapeHtml(errors[i]) + '</li>';
        }
        html += '</ul>';
        container.innerHTML = html;
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    function formatDate(date) {
        var y = date.getFullYear();
        var m = ('0' + (date.getMonth() + 1)).slice(-2);
        var d = ('0' + date.getDate()).slice(-2);
        return y + '-' + m + '-' + d;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str)));
        return div.innerHTML;
    }
})();
