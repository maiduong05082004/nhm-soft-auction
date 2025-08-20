
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("mobile-filter-toggle");
    const sidebar = document.getElementById("filter-sidebar");
    const chevron = document.getElementById("filter-chevron");
    console.log(toggleBtn, sidebar);

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener("click", function () {
            sidebar.classList.toggle("hidden");
            chevron.classList.toggle("rotate-180");
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const priceRangeBtns = document.querySelectorAll('.price-range-btn');
    const priceMinInput = document.querySelector('input[name="price_min"]');
    const priceMaxInput = document.querySelector('input[name="price_max"]');

    priceRangeBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const min = this.dataset.min;
            const max = this.dataset.max;

            priceMinInput.value = min || '';
            priceMaxInput.value = max || '';

            priceRangeBtns.forEach(b => b.classList.remove('bg-blue-100', 'border-blue-300',
                'text-blue-700'));
            this.classList.add('bg-blue-100', 'border-blue-300', 'text-blue-700');
        });
    });

    // const autoSubmitElements = document.querySelectorAll(
    //     'select[name="sort_by"], input[name="product_type"]');
    // autoSubmitElements.forEach(element => {
    //     element.addEventListener('change', function () {
    //         document.getElementById('filter-form').submit();
    //     });
    // });

    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function () {
            if (this.value < 0) this.value = 0;
        });
    });

    const categorySearch = document.getElementById('category-search');
    const categoryItems = document.querySelectorAll('.category-item');
    const selectedCategoryInput = document.getElementById('selected-category-id'); // hidden input (CSV)
    const selectedCategoryDisplay = document.getElementById('selected-category-display');
    const selectedCategoryList = document.getElementById('selected-category-list');
    const clearCategoryBtn = document.getElementById('clear-category');

    const selectedIds = new Set();

    function initFromInput() {
        const val = selectedCategoryInput.value ? String(selectedCategoryInput.value).trim() : '';
        if (!val) return;
        const parts = val.includes(',') ? val.split(',') : [val];
        parts.forEach(p => {
            const id = p.toString().trim();
            if (id) selectedIds.add(id);
        });
    }
    function renderSelected() {
        selectedCategoryList.innerHTML = '';
        if (selectedIds.size === 0) {
            selectedCategoryDisplay.classList.add('hidden');
            selectedCategoryInput.value = '';
            return;
        }

        selectedCategoryDisplay.classList.remove('hidden');

        selectedCategoryInput.value = Array.from(selectedIds).join(',');

        selectedIds.forEach(id => {
            const item = document.querySelector(`.category-item input[type="checkbox"][value="${id}"]`);
            let title = id;
            if (item) {
                const parentItem = item.closest('.category-item');
                if (parentItem) {
                    title = parentItem.dataset.fullPath || parentItem.querySelector('.category-name')?.textContent?.trim() || id;
                }
            }
            const tag = document.createElement('div');
            tag.className = 'inline-flex items-center gap-2 px-2 py-1 bg-blue-100 text-blue-800 rounded-md text-xs';
            tag.innerHTML = `
                <span class="truncate max-w-xs">${escapeHtml(title)}</span>
                <button type="button" class="remove-category inline-flex items-center" data-id="${id}" aria-label="Remove" title="Xóa">
                    <!-- small X icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.293 6.293a1 1 0 011.414 0L10 8.586l2.293-2.293a1 1 0 111.414 1.414L11.414 10l2.293 2.293a1 1 0 01-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 01-1.414-1.414L8.586 10 6.293 7.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                </button>
            `;
            selectedCategoryList.appendChild(tag);
        });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    initFromInput();
    selectedIds.forEach(id => {
        const checkbox = document.querySelector(`.category-item input[type="checkbox"][value="${id}"]`);
        if (checkbox) checkbox.checked = true;
    });

    renderSelected();
    if (categorySearch) {
        categorySearch.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            categoryItems.forEach(item => {
                const nameEl = item.querySelector('.category-name');
                const categoryName = nameEl ? nameEl.textContent.toLowerCase() : '';
                const shouldShow = categoryName.includes(searchTerm) || searchTerm === '';

                item.style.display = shouldShow ? 'block' : 'none';

                if (shouldShow && searchTerm !== '') {
                    let parent = item.closest('.category-children');
                    while (parent) {
                        const parentItem = parent.previousElementSibling;
                        if (parentItem && parentItem.classList.contains('category-item')) {
                            parentItem.style.display = 'block';
                        }
                        parent = parent.closest('.category-item')?.parentElement?.closest('.category-children');
                    }
                }
            });
        });
    }
    categoryItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                const id = String(this.value);
                if (this.checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
                renderSelected();
            });
        }
    });
    selectedCategoryList.addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-category');
        if (!btn) return;
        const id = btn.dataset.id;
        if (!id) return;
        selectedIds.delete(id);
        const checkbox = document.querySelector(`.category-item input[type="checkbox"][value="${id}"]`);
        if (checkbox) checkbox.checked = false;
        renderSelected();
    });

    if (clearCategoryBtn) {
        clearCategoryBtn.addEventListener('click', function () {
            selectedIds.clear();
            categoryItems.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
            });
            renderSelected();
        });
    }

    const toggleBtns = document.querySelectorAll('.category-toggle');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const children = this.parentElement.nextElementSibling;
            const icon = this.querySelector('svg');

            if (children && children.classList.contains('category-children')) {
                children.classList.toggle('hidden');
                if (icon) icon.classList.toggle('rotate-90');
            }
        });
    });
});
