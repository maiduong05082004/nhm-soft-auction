
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
    const selectedCategoryId = document.getElementById('selected-category-id');
    const selectedCategoryDisplay = document.getElementById('selected-category-display');
    const selectedCategoryName = document.getElementById('selected-category-name');
    const clearCategoryBtn = document.getElementById('clear-category');

    // Search functionality
    if (categorySearch) {
        categorySearch.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();

            categoryItems.forEach(item => {
                const categoryName = item.querySelector('.category-name').textContent.toLowerCase();
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

    // Category selection
    categoryItems.forEach(item => {
        const checkbox = item.querySelector('input[type="checkbox"]');
        if (checkbox) {
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    categoryItems.forEach(otherItem => {
                        const otherCheckbox = otherItem.querySelector('input[type="checkbox"]');
                        if (otherCheckbox && otherCheckbox !== this) {
                            otherCheckbox.checked = false;
                        }
                    });

                    selectedCategoryId.value = this.value;
                    selectedCategoryName.textContent = this.dataset.fullPath;
                    selectedCategoryDisplay.classList.remove('hidden');
                } else {
                    selectedCategoryId.value = '';
                    selectedCategoryDisplay.classList.add('hidden');
                }
            });
        }
    });

    // Clear category selection
    if (clearCategoryBtn) {
        clearCategoryBtn.addEventListener('click', function () {
            selectedCategoryId.value = '';
            selectedCategoryDisplay.classList.add('hidden');
            categoryItems.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                if (checkbox) {
                    checkbox.checked = false;
                }
            });
        });
    }

    // Toggle category children
    const toggleBtns = document.querySelectorAll('.category-toggle');
    toggleBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const children = this.parentElement.nextElementSibling;
            const icon = this.querySelector('svg');

            if (children && children.classList.contains('category-children')) {
                children.classList.toggle('hidden');
                icon.classList.toggle('rotate-90');
            }
        });
    });

});