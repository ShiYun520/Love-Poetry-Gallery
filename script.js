document.addEventListener('DOMContentLoaded', () => {
    // --- 图片懒加载 (Lazy Loading) ---
    initLazyLoading();
    
    // --- Lightbox 模态框 (Modal) ---
    initGalleryItemClick();

    // --- Navbar Transparency on Scroll ---
    const navbar = document.querySelector('.navbar');
    const scrollThreshold = 50; 
    let ticking = false; 

    function handleScroll() {
        if (!ticking) {
            window.requestAnimationFrame(() => {
                if (window.scrollY > scrollThreshold) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
                ticking = false;
            });
            ticking = true;
        }
    }

    window.addEventListener('scroll', handleScroll);
    handleScroll();

    // --- Wallpaper Card Text Fade Out Effect ---
    const textContentElement = document.querySelector('.wallpaper-card .text-content');
    
    if (textContentElement) {
        setTimeout(() => {
            textContentElement.classList.add('fading-out');
            textContentElement.addEventListener('transitionend', function handler(event) {
                if (event.propertyName === 'opacity') {
                    textContentElement.style.display = 'none';
                    textContentElement.removeEventListener('transitionend', handler);
                }
            }, { once: true });
        }, 2000);
    }
    
    // --- 刷新壁纸功能 ---
    const refreshDesktopBtn = document.getElementById('refresh-desktop');
    if (refreshDesktopBtn) {
        refreshDesktopBtn.addEventListener('click', function() {
            refreshWallpapers('desktop', 8);
        });
    }
    
    const refreshMobileBtn = document.getElementById('refresh-mobile');
    if (refreshMobileBtn) {
        refreshMobileBtn.addEventListener('click', function() {
            refreshWallpapers('mobile', 10);
        });
    }
});

// 初始化懒加载
function initLazyLoading() {
    const lazyImages = document.querySelectorAll('.lazy-image');

    const lazyLoad = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                const src = img.getAttribute('data-src');
                if (src) {
                    const tempImg = new Image();
                    tempImg.src = src;
                    tempImg.onload = () => {
                        img.src = src;
                        img.classList.add('loaded');
                    };
                    observer.unobserve(img);
                }
            }
        });
    };

    const observer = new IntersectionObserver(lazyLoad, {
        rootMargin: '0px 0px 200px 0px',
        threshold: 0.01
    });

    lazyImages.forEach(img => {
        observer.observe(img);
    });
}

// 初始化画廊项点击事件
function initGalleryItemClick() {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('img01');
    const modalTitle = document.getElementById('modalTitle');
    const modalDescription = document.getElementById('modalDescription');
    const closeButton = document.querySelector('.close-button');
    const imageCards = document.querySelectorAll('.gallery-item');

    imageCards.forEach(card => {
        card.addEventListener('click', () => {
            const imgSrc = card.querySelector('.lazy-image').getAttribute('data-src');
            const title = card.getAttribute('data-title');
            const description = card.getAttribute('data-desc');

            modal.classList.add('show');
            modal.style.display = 'block';
            modalImg.src = imgSrc;
            modalImg.alt = title;
            modalTitle.textContent = title;
            modalDescription.textContent = description;
            document.body.style.overflow = 'hidden';
        });
    });

    const closeModal = () => {
        modal.classList.remove('show');
        modal.addEventListener('transitionend', function handler() {
            if (!modal.classList.contains('show')) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
            modal.removeEventListener('transitionend', handler);
        }, { once: true });
    };

    closeButton.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.style.display === 'block') {
            closeModal();
        }
    });
}

// 刷新壁纸函数 - 使用front-api.php而非原来的刷新接口
function refreshWallpapers(type, limit) {
    fetch(`front-api.php?action=refresh&type=${type}&limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const gridSelector = type === 'mobile' ? '.mobile-gallery-grid' : '.gallery-grid:not(.mobile-gallery-grid)';
                const grid = document.querySelector(gridSelector);
                
                if (grid) {
                    // 清空现有内容
                    grid.innerHTML = '';
                    
                    // 添加新壁纸
                    data.data.forEach(wallpaper => {
                        const galleryItem = document.createElement('div');
                        galleryItem.className = `gallery-item ${type === 'mobile' ? 'mobile-gallery-item' : ''}`;
                        galleryItem.dataset.title = wallpaper.title;
                        galleryItem.dataset.desc = wallpaper.description;
                        
                        galleryItem.innerHTML = `
                            <div class="image-container">
                                <img class="lazy-image" 
                                     src="http://fz.torgw.cc/bz/uploads/683ec0b07f8ad.png" 
                                     data-src="${wallpaper.image_url}" 
                                     alt="${wallpaper.title}">
                            </div>
                            <div class="image-overlay">
                                <h3 class="image-title">${wallpaper.title}</h3>
                                <p class="image-desc">${wallpaper.description}</p>
                            </div>
                        `;
                        
                        grid.appendChild(galleryItem);
                    });
                    
                    // 重新初始化懒加载和点击事件
                    initLazyLoading();
                    initGalleryItemClick();
                }
            } else {
                console.error('刷新壁纸失败:', data.message);
            }
        })
        .catch(error => {
            console.error('请求失败:', error);
        });
}
