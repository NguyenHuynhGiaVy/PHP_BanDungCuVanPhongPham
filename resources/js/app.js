import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const productNameForSlug = document.querySelector('[data-auto-slug-name]');
const productSlug = document.querySelector('[data-auto-slug-output]');
const slugAutoFlag = document.querySelector('[data-slug-auto-flag]');

if (productNameForSlug instanceof HTMLInputElement && productSlug instanceof HTMLInputElement && slugAutoFlag instanceof HTMLInputElement) {
    let isSlugAutomatic = slugAutoFlag.value === '1';
    const slugify = (value) => value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/[đĐ]/g, 'd').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

    if (isSlugAutomatic && productSlug.value === '') productSlug.value = slugify(productNameForSlug.value);
    productNameForSlug.addEventListener('input', () => { if (isSlugAutomatic) productSlug.value = slugify(productNameForSlug.value); });
    productSlug.addEventListener('input', () => {
        if (productSlug.value.trim() === '') {
            isSlugAutomatic = true; slugAutoFlag.value = '1'; productSlug.value = slugify(productNameForSlug.value); return;
        }
        isSlugAutomatic = false; slugAutoFlag.value = '0';
    });
}

document.querySelectorAll('[data-product-filter-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[data-filter-submit]');
        const label = button?.querySelector('[data-filter-submit-label]');
        const status = form.querySelector('[data-filter-loading-status]');
        if (!(button instanceof HTMLButtonElement) || !label || !status) return;
        button.disabled = true; label.textContent = 'Đang tải...'; status.textContent = 'Đang áp dụng bộ lọc sản phẩm.';
        const spinner = document.createElement('span');
        spinner.classList.add('spinner-border', 'spinner-border-sm', 'me-2'); spinner.setAttribute('aria-hidden', 'true');
        button.prepend(spinner);
    });
});

document.querySelectorAll('[data-product-cart-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        const submitter = event.submitter;
        if (!(submitter instanceof HTMLButtonElement)) return;
        const label = submitter.querySelector('[data-cart-submit-label]');
        if (!label) return;
        form.querySelectorAll('button[type="submit"]').forEach((button) => { button.disabled = true; });
        const spinner = document.createElement('span');
        spinner.classList.add('spinner-border', 'spinner-border-sm', 'me-2'); spinner.setAttribute('aria-hidden', 'true');
        submitter.prepend(spinner); label.textContent = submitter.dataset.pendingLabel || 'Đang xử lý...';
    });
});

document.querySelectorAll('[data-cart-quantity-form]').forEach((form) => {
    const quantity = form.querySelector('[data-cart-quantity]');
    if (!(quantity instanceof HTMLInputElement)) return;
    const updateStepButtons = () => {
        const availableQuantity = quantity.dataset.available === '' ? Number(quantity.max || 99) : Number(quantity.dataset.available);
        const maximum = Math.max(1, Math.min(Number(quantity.max || 99), availableQuantity));
        const currentQuantity = Number(quantity.value);
        form.querySelectorAll('[data-quantity-step]').forEach((button) => {
            const step = Number(button.dataset.quantityStep);
            button.disabled = (step < 0 && currentQuantity <= Number(quantity.min || 1)) || (step > 0 && currentQuantity >= maximum);
        });
    };
    form.querySelectorAll('[data-quantity-step]').forEach((button) => {
        button.addEventListener('click', () => {
            const step = Number(button.dataset.quantityStep);
            const minimum = Number(quantity.min || 1);
            const availableQuantity = quantity.dataset.available === '' ? Number(quantity.max || 99) : Number(quantity.dataset.available);
            const maximum = Math.max(1, Math.min(Number(quantity.max || 99), availableQuantity));
            const currentQuantity = Number(quantity.value) || minimum;
            const newQuantity = Math.max(minimum, Math.min(maximum, currentQuantity + step));
            if (newQuantity !== currentQuantity) {
                quantity.value = String(newQuantity);
                updateStepButtons();
                form.requestSubmit();
            }
        });
    });
    quantity.addEventListener('input', updateStepButtons);
    updateStepButtons();
});

const cartRemoveModal = document.querySelector('[data-cart-remove-modal]');
if (cartRemoveModal instanceof HTMLElement) {
    const removeProductName = cartRemoveModal.querySelector('[data-remove-product-name]');
    const confirmRemoveButton = cartRemoveModal.querySelector('[data-cart-remove-confirm]');
    let pendingRemoveForm = null;
    cartRemoveModal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        if (!(trigger instanceof HTMLButtonElement)) return;
        const form = document.getElementById(trigger.dataset.removeForm || '');
        pendingRemoveForm = form instanceof HTMLFormElement ? form : null;
        if (removeProductName) removeProductName.textContent = trigger.dataset.productName || '';
    });
    confirmRemoveButton?.addEventListener('click', () => { pendingRemoveForm?.requestSubmit(); });
}

document.querySelectorAll('[data-checkout-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[data-checkout-submit]');
        const label = form.querySelector('[data-checkout-submit-label]');
        const status = form.querySelector('[data-checkout-status]');
        if (!(button instanceof HTMLButtonElement) || !label || !status) return;
        button.disabled = true; label.textContent = 'Đang tạo đơn hàng...'; status.textContent = 'Đang xác nhận đơn hàng mô phỏng.';
        const spinner = document.createElement('span');
        spinner.classList.add('spinner-border', 'spinner-border-sm', 'me-2'); spinner.setAttribute('aria-hidden', 'true');
        button.prepend(spinner);
    });
});

document.querySelectorAll('[data-auth-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const button = form.querySelector('[data-auth-submit]');
        const label = form.querySelector('[data-auth-submit-label]');
        const status = form.querySelector('[data-auth-status]');
        if (!(button instanceof HTMLButtonElement) || !label || !status) return;
        button.disabled = true; label.textContent = 'Đang xử lý...'; status.textContent = 'Đang xử lý yêu cầu tài khoản.';
        const spinner = document.createElement('span');
        spinner.classList.add('spinner-border', 'spinner-border-sm', 'me-2'); spinner.setAttribute('aria-hidden', 'true');
        button.prepend(spinner);
    });
});

const adminConfirmModal = document.querySelector('#admin-confirm-modal');
if (adminConfirmModal instanceof HTMLElement) {
    let pendingAdminForm = null;
    const message = adminConfirmModal.querySelector('[data-admin-confirm-message]');
    adminConfirmModal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        if (!(trigger instanceof HTMLButtonElement)) return;
        const form = document.getElementById(trigger.dataset.adminForm || '');
        pendingAdminForm = form instanceof HTMLFormElement ? form : null;
        if (message) message.textContent = trigger.dataset.adminMessage || 'Bạn có chắc muốn xóa mục này?';
    });
    adminConfirmModal.querySelector('[data-admin-confirm-submit]')?.addEventListener('click', () => { pendingAdminForm?.requestSubmit(); });
}

const couponSendModal = document.querySelector('#coupon-send-modal');
if (couponSendModal instanceof HTMLElement) {
    const form = couponSendModal.querySelector('#coupon-send-form');
    const search = couponSendModal.querySelector('#coupon-recipient-search');
    const rows = Array.from(couponSendModal.querySelectorAll('.coupon-recipient-row'));
    const checkboxes = Array.from(couponSendModal.querySelectorAll('input[name="recipient_ids[]"]'));
    const submit = couponSendModal.querySelector('[data-coupon-send-submit]');
    const count = couponSendModal.querySelector('[data-coupon-recipient-count]');
    const selectAll = couponSendModal.querySelector('[data-coupon-select-all]');
    const title = couponSendModal.querySelector('#coupon-send-title');

    const updateSelection = () => {
        const selected = checkboxes.filter((checkbox) => checkbox.checked).length;
        if (count) count.textContent = `${selected} khách hàng được chọn`;
        if (submit instanceof HTMLButtonElement) submit.disabled = selected === 0;
    };

    document.querySelectorAll('[data-coupon-send-url]').forEach((button) => {
        button.addEventListener('click', () => {
            if (!(form instanceof HTMLFormElement)) return;
            form.action = button.dataset.couponSendUrl || '';
            if (title) title.textContent = `Gửi mã ${button.dataset.couponCode || ''}`;
            checkboxes.forEach((checkbox) => { checkbox.checked = false; });
            if (search instanceof HTMLInputElement) search.value = '';
            rows.forEach((row) => row.classList.remove('d-none'));
            updateSelection();
        });
    });

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', updateSelection));

    if (search instanceof HTMLInputElement) {
        search.addEventListener('input', () => {
            const term = search.value.trim().toLowerCase();
            rows.forEach((row) => {
                const searchable = row.dataset.customerSearch || '';
                row.classList.toggle('d-none', term !== '' && !searchable.includes(term));
            });
        });
    }

    selectAll?.addEventListener('click', () => {
        rows.forEach((row) => {
            if (row.classList.contains('d-none')) return;
            const checkbox = row.querySelector('input[name="recipient_ids[]"]');
            if (checkbox instanceof HTMLInputElement) checkbox.checked = true;
        });
        updateSelection();
    });

    updateSelection();
}

document.addEventListener('error', (event) => {
    const image = event.target;
    if (!(image instanceof HTMLImageElement)) return;
    if (image.matches('[data-product-image]')) {
        image.hidden = true; image.nextElementSibling?.removeAttribute('hidden');
    }
    if (image.matches('[data-brand-image]')) {
        image.hidden = true; image.nextElementSibling?.removeAttribute('hidden');
    }
}, true);

document.querySelectorAll('[data-variant-selector]').forEach((selector) => {
    const price = document.querySelector('[data-variant-price]');
    const comparePrice = document.querySelector('[data-variant-compare]');
    const sku = document.querySelector('[data-variant-sku]');
    const stock = document.querySelector('[data-variant-stock]');
    const quantity = document.querySelector('[data-variant-quantity]');
    const submitButtons = selector.form?.querySelectorAll('[data-variant-submit]') ?? [];
    const updateVariant = () => {
        const option = selector.selectedOptions[0];
        const variantPrice = Number(option.dataset.price);
        const variantComparePrice = Number(option.dataset.comparePrice);
        const availableQuantity = option.dataset.available;
        if (price && Number.isFinite(variantPrice)) price.textContent = `${new Intl.NumberFormat('vi-VN').format(variantPrice)} ₫`;
        if (comparePrice) {
            const hasComparePrice = variantComparePrice > variantPrice;
            comparePrice.hidden = !hasComparePrice;
            comparePrice.textContent = hasComparePrice ? `${new Intl.NumberFormat('vi-VN').format(variantComparePrice)} ₫` : '';
        }
        if (sku) sku.textContent = option.dataset.sku || 'Đang cập nhật';
        if (stock) {
            const available = availableQuantity === '' ? null : Number(availableQuantity);
            const icon = document.createElement('i');
            icon.classList.add('bi', 'me-1');
            if (available === null) { icon.classList.add('bi-info-circle'); stock.replaceChildren(icon, document.createTextNode('Đang cập nhật tồn kho')); }
            else if (available > 0) { icon.classList.add('bi-check-circle'); stock.replaceChildren(icon, document.createTextNode(`Còn hàng (${available})`)); }
            else { icon.classList.add('bi-x-circle'); stock.replaceChildren(icon, document.createTextNode('Tạm hết hàng')); }
            submitButtons.forEach((button) => { button.disabled = available === null || available < 1; });
        }
        if (quantity) {
            if (availableQuantity === '') quantity.removeAttribute('max');
            else {
                quantity.max = String(Math.max(1, Number(availableQuantity)));
                if (Number(quantity.value) > Number(quantity.max)) quantity.value = quantity.max;
            }
        }
    };
    selector.addEventListener('change', updateVariant); updateVariant();
});

document.querySelectorAll('[data-gallery-thumbnail]').forEach((thumbnail) => {
    thumbnail.addEventListener('click', () => {
        const mainImage = document.querySelector('[data-gallery-main]');
        if (!mainImage) return;
        mainImage.src = thumbnail.dataset.imageSrc;
        mainImage.alt = thumbnail.dataset.imageAlt || '';
        mainImage.hidden = false; mainImage.nextElementSibling?.setAttribute('hidden', '');
        document.querySelectorAll('[data-gallery-thumbnail]').forEach((item) => {
            const isSelected = item === thumbnail;
            item.classList.toggle('active', isSelected); item.setAttribute('aria-pressed', String(isSelected));
        });
    });
});

const initializeAttributeFields = (root) => {
    const groups = [];
    if (root instanceof Element && root.matches('[data-attribute-fields]')) groups.push(root);
    groups.push(...root.querySelectorAll('[data-attribute-fields]'));
    groups.forEach((fields) => {
        if (fields.dataset.initialized === 'true') return;
        fields.dataset.initialized = 'true';
        const list = fields.querySelector('[data-attribute-list]');
        const template = fields.querySelector('template[data-attribute-template]');
        fields.querySelector('[data-add-attribute]')?.addEventListener('click', () => {
            if (!(list instanceof HTMLElement) || !(template instanceof HTMLTemplateElement)) return;
            const index = Number(list.dataset.nextIndex || 0);
            list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__ATTRIBUTE_INDEX__', String(index)));
            list.dataset.nextIndex = String(index + 1);
        });
        fields.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof Element)) return;
            const removeButton = target.closest('[data-remove-attribute]');
            removeButton?.closest('[data-attribute-row]')?.remove();
        });
    });
};
initializeAttributeFields(document);

document.querySelectorAll('[data-variant-list]').forEach((list) => {
    const form = list.closest('form');
    const template = form?.querySelector('template[data-variant-template]');
    const addButton = form?.querySelector('[data-add-variant]');
    if (!(template instanceof HTMLTemplateElement) || !(addButton instanceof HTMLButtonElement)) return;
    let nextVariantIndex = Number(list.dataset.nextIndex || 0);
    const updateRemoveButtons = () => {
        const cards = list.querySelectorAll('[data-variant-card]');
        cards.forEach((card) => {
            const removeButton = card.querySelector('[data-remove-variant]');
            if (removeButton instanceof HTMLButtonElement) removeButton.disabled = cards.length === 1;
        });
    };
    addButton.addEventListener('click', () => {
        list.insertAdjacentHTML('beforeend', template.innerHTML.replaceAll('__VARIANT_INDEX__', String(nextVariantIndex)));
        initializeAttributeFields(list.lastElementChild);
        nextVariantIndex += 1; list.dataset.nextIndex = String(nextVariantIndex); updateRemoveButtons();
    });
    list.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof Element)) return;
        const removeButton = target.closest('[data-remove-variant]');
        if (removeButton && list.querySelectorAll('[data-variant-card]').length > 1) {
            removeButton.closest('[data-variant-card]')?.remove(); updateRemoveButtons();
        }
    });
    updateRemoveButtons();
});

document.querySelectorAll('[data-live-table-filter]').forEach((input) => {
    const targetSelector = input.dataset.liveTableFilter;
    const table = document.querySelector(targetSelector);
    if (!table) return;
    input.addEventListener('input', function () {
        const query = this.value.toLowerCase().trim();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach((row) => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
});

const counters = document.querySelectorAll('.counter-number');
if (counters.length > 0) {
    const runCounter = (el) => {
        const raw = el.textContent.replace(/\./g, '').replace(',', '.');
        const target = parseFloat(raw);
        if (isNaN(target)) return;
        const isDecimal = el.textContent.includes('.');
        let count = 0; const step = target / 40;
        const timer = setInterval(() => {
            count += step;
            if (count >= target) {
                el.textContent = isDecimal ? target.toFixed(1) : Math.round(target).toLocaleString('vi-VN');
                clearInterval(timer);
            } else {
                el.textContent = isDecimal ? count.toFixed(1) : Math.round(count).toLocaleString('vi-VN');
            }
        }, 25);
    };
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => { if (entry.isIntersecting) { runCounter(entry.target); observer.unobserve(entry.target); } });
        }, { threshold: 0.2 });
        counters.forEach((counter) => observer.observe(counter));
    } else { counters.forEach((counter) => runCounter(counter)); }
}

/* ============================================================
   FLASH SALE COUNTDOWN
   ============================================================ */
const flashSaleCountdown = document.querySelector('[data-flash-sale-countdown]');
if (flashSaleCountdown instanceof HTMLElement) {
    const endOfDay = new Date();
    endOfDay.setHours(23, 59, 59, 999);
    const pad = (n) => String(n).padStart(2, '0');
    const tick = () => {
        const diff = endOfDay.getTime() - Date.now();
        if (diff <= 0) {
            flashSaleCountdown.textContent = '00 : 00 : 00';
            clearInterval(flashSaleCountdown._interval);
            return;
        }
        const hours = Math.floor(diff / 3_600_000);
        const minutes = Math.floor((diff % 3_600_000) / 60_000);
        const seconds = Math.floor((diff % 60_000) / 1_000);
        flashSaleCountdown.textContent = `${pad(hours)} : ${pad(minutes)} : ${pad(seconds)}`;
    };
    tick();
    flashSaleCountdown._interval = setInterval(tick, 1000);
}