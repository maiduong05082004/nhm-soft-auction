
$(document).ready(function () {
    let mobileMenuOpen = false;
    let searchOpen = false;
    let searchQuery = '';

    function openMobileMenu() {
        mobileMenuOpen = true;
        $('#mobile-overlay').removeClass('hidden').css('opacity', '0').animate({ opacity: 1 }, 300);
        $('#mobile-menu').removeClass('translate-x-full').addClass('translate-x-0');
    }

    function closeMobileMenu() {
        mobileMenuOpen = false;
        $('#mobile-overlay').animate({ opacity: 0 }, 300, function () {
            $(this).addClass('hidden');
        });
        $('#mobile-menu').removeClass('translate-x-0').addClass('translate-x-full');
    }

    function openSearch() {
        console.log('open search');
        $('#search-overlay').removeClass('hidden');
        $('#search-overlay').addClass('flex opacity-100');
        $('#search-modal').removeClass('hidden').css('opacity', '100');
        $('#search-modal').addClass('flex opacity-100');
        setTimeout(() => {
            $('#search-overlay').css('opacity', '1');
            $('#search-modal .bg-white').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100');
            $('#search-input').focus();
        }, 10);
    }

    function closeSearch() {
        searchOpen = false;
        $('body').removeClass('overflow-hidden');
        searchQuery = '';
        $('#search-input, #mobile-search-input').val('');

        $('#search-modal .bg-white').removeClass('scale-100 opacity-100').addClass('scale-95 opacity-0');

        setTimeout(() => {
            $('#search-overlay').css('opacity', '0');
            setTimeout(() => {
                $('#search-modal, #search-overlay').addClass('hidden');
            }, 300);
        }, 300);
    }

    $('#mobile-menu-btn').click(openMobileMenu);
    $('#close-mobile-menu, #mobile-overlay').click(closeMobileMenu);
    $('#search-btn').click(openSearch);
    $('#close-search, #cancel-search').click(closeSearch);

    // Close search when clicking overlay
    $('#search-overlay').click(function (e) {
        if (e.target === this) {
            closeSearch();
        }
    });

    // Handle escape key
    $(document).keydown(function (e) {
        if (e.key === 'Escape') {
            if (mobileMenuOpen) {
                closeMobileMenu();
            }
            if (searchOpen) {
                closeSearch();
            }
        }
    });

    $('#search-input, #mobile-search-input').on('input', function () {
        searchQuery = $(this).val();
        $('#search-input, #mobile-search-input').val(searchQuery);
    });

    $('#search-form, #mobile-search-form').submit(function (e) {
        if (searchOpen) {
            closeSearch();
        }
        if (mobileMenuOpen) {
            closeMobileMenu();
        }
    });

});