
document.addEventListener('DOMContentLoaded', function () {
    const themeButton = document.getElementById('themeButton');
    const html = document.documentElement;

    if (!themeButton) return;

    function applyTheme(theme) {
        html.setAttribute('data-theme', theme);

        try {
            localStorage.setItem('aistudy-theme', theme);
        } catch (error) {
            console.warn('Không thể lưu giao diện:', error);
        }

        if (theme === 'dark') {
            themeButton.textContent = '☀';
            themeButton.title = 'Chuyển sang giao diện sáng';
        } else {
            themeButton.textContent = '☾';
            themeButton.title = 'Chuyển sang giao diện tối';
        }
    }

    let savedTheme = 'light';

    try {
        savedTheme = localStorage.getItem('aistudy-theme') || 'light';
    } catch (error) {
        console.warn('Không thể đọc giao diện đã lưu:', error);
    }

    applyTheme(savedTheme);

    themeButton.addEventListener('click', function () {
        const currentTheme = html.getAttribute('data-theme');

        applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
    });
});
