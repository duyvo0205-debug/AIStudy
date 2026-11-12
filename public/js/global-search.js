
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('globalSearch');
    const searchBox = document.querySelector('.search-box');

    if (!searchInput || !searchBox) {
        return;
    }

    // Tạo vùng hiển thị kết quả
    let resultBox = document.getElementById('globalSearchResults');

    if (!resultBox) {
        resultBox = document.createElement('div');
        resultBox.id = 'globalSearchResults';
        resultBox.className = 'global-search-results';
        resultBox.hidden = true;

        searchBox.appendChild(resultBox);
    }

    const searchUrl = searchInput.dataset.searchUrl;

    let debounceTimer = null;
    let currentRequest = null;

    // Hiển thị trạng thái
    function showMessage(message) {
        resultBox.replaceChildren();

        const item = document.createElement('div');
        item.className = 'search-message';
        item.textContent = message;

        resultBox.appendChild(item);
        resultBox.hidden = false;
    }

    // Tạo kết quả tìm kiếm an toàn
    function createResult(result) {
        const link = document.createElement('a');

        link.className = 'search-result-item';
        link.href = result.url;

        const icon = document.createElement('div');
        icon.className = 'search-result-icon';
        icon.textContent = getIcon(result.icon);

        const content = document.createElement('div');
        content.className = 'search-result-content';

        const title = document.createElement('strong');
        title.textContent = result.title;

        const description = document.createElement('span');
        description.textContent = result.description || result.type;

        const type = document.createElement('small');
        type.textContent = result.type;

        content.appendChild(title);
        content.appendChild(description);

        link.appendChild(icon);
        link.appendChild(content);
        link.appendChild(type);

        return link;
    }

    // Biểu tượng kết quả
    function getIcon(icon) {
        const icons = {
            'book-open': '📖',
            'clipboard-list': '📝',
            'target': '🎯',
            'calendar': '📅',
            'calendar-days': '🗓️',
            'house': '⌂',
            'sparkles': '✦',
            'route': '◈',
            'lightbulb': '💡',
            'chart-no-axes-combined': '📊',
            'user': '👤'
        };

        return icons[icon] || '🔎';
    }

    // Đóng kết quả
    function closeResults() {
        resultBox.hidden = true;
    }

    // Gửi yêu cầu tìm kiếm
    async function performSearch(keyword) {
        if (!keyword.trim()) {
            closeResults();
            return;
        }

        if (!searchUrl) {
            showMessage('Không tìm thấy địa chỉ tìm kiếm.');
            return;
        }

        if (currentRequest) {
            currentRequest.abort();
        }

        currentRequest = new AbortController();

        showMessage('Đang tìm kiếm...');

        try {
            const url = new URL(searchUrl, window.location.origin);

            url.searchParams.set('q', keyword);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: currentRequest.signal
            });

            if (!response.ok) {
                throw new Error('Không thể tìm kiếm dữ liệu.');
            }

            const data = await response.json();

            // Không hiển thị kết quả của từ khóa cũ
            if (searchInput.value.trim() !== keyword) {
                return;
            }

            resultBox.replaceChildren();

            if (!data.results || data.results.length === 0) {
                showMessage('Không tìm thấy kết quả phù hợp.');
                return;
            }

            // Tiêu đề kết quả
            const heading = document.createElement('div');
            heading.className = 'search-results-heading';
            heading.textContent = `Tìm thấy ${data.total} kết quả`;

            resultBox.appendChild(heading);

            // Danh sách kết quả
            data.results.forEach(function (result) {
                resultBox.appendChild(createResult(result));
            });

            resultBox.hidden = false;

        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error('Search error:', error);

            showMessage('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại.');
        }
    }

    // Tìm kiếm khi nhập
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimer);

        const keyword = this.value.trim();

        if (!keyword) {
            closeResults();

            if (currentRequest) {
                currentRequest.abort();
            }

            return;
        }

        debounceTimer = setTimeout(function () {
            performSearch(keyword);
        }, 300);
    });

    // Hiển thị lại kết quả khi focus
    searchInput.addEventListener('focus', function () {
        if (this.value.trim()) {
            performSearch(this.value.trim());
        }
    });

    // Ctrl + K để mở tìm kiếm
    document.addEventListener('keydown', function (event) {
        if (
            (event.ctrlKey || event.metaKey) &&
            event.key.toLowerCase() === 'k'
        ) {
            event.preventDefault();

            searchInput.focus();
            searchInput.select();
        }

        if (event.key === 'Escape') {
            closeResults();
            searchInput.blur();
        }
    });

    // Click bên ngoài để đóng
    document.addEventListener('click', function (event) {
        if (!searchBox.contains(event.target)) {
            closeResults();
        }
    });

});
