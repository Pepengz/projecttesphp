document.addEventListener('DOMContentLoaded', function() {
    initPageTransitions();
    applyStaggeredAnimations();
    setupTextEffects();
    initGalleryModal();
    setupNavigation();
    setupContactInteractions();
    createScrollToTopButton();
    initScrollAnimations();
    setTimeout(initTypingAnimation, 50);
});

function initPageTransitions() {
    document.body.setAttribute('data-page', window.location.pathname.split('/').pop());
    
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.hostname === window.location.hostname) {
                const currentPage = window.location.pathname.split('/').pop();
                const targetPage = this.pathname.split('/').pop();
                if (currentPage === targetPage) return;
                e.preventDefault();
                document.body.classList.add('page-exit');
                
                setTimeout(() => window.location.href = this.href, 300);
            }
        });
    });
    
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.body.classList.remove('page-exit');
            applyStaggeredAnimations();
        }
    });
}

function applyStaggeredAnimations() {
    document.querySelectorAll('section').forEach((section, index) => {
        section.style.animationDelay = `${0.1 + (index * 0.08)}s`;
    });
    document.querySelectorAll('#gallery img').forEach((img, index) => {
        img.style.animationDelay = `${0.1 + (index * 0.03)}s`;
    });
    document.querySelectorAll('article').forEach((article, index) => {
        article.style.animationDelay = `${0.1 + (index * 0.1)}s`;
    });
}

function setupTextEffects() {
    document.querySelectorAll('h1').forEach(heading => {
        heading.classList.add('animated-text');
    });
}

function initGalleryModal() {
    const galleryImages = document.querySelectorAll('#gallery img');
    if (galleryImages.length === 0) return;
    const modal = document.createElement('div');
    modal.className = 'image-modal';
    modal.innerHTML = `
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <img class="modal-image">
            <div class="modal-navigation">
                <button class="prev-button">&lt;</button>
                <button class="next-button">&gt;</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
    
    const modalElements = {
        img: modal.querySelector('.modal-image'),
        close: modal.querySelector('.close-button'),
        prev: modal.querySelector('.prev-button'),
        next: modal.querySelector('.next-button')
    };
    
    let currentIndex = 0;
    galleryImages.forEach((img, index) => {
        img.addEventListener('click', () => {
            modal.style.display = 'flex';
            modalElements.img.src = img.src;
            currentIndex = index;
            checkNavButtons();
        });
    });
    
    modalElements.close.addEventListener('click', () => modal.style.display = 'none');
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.style.display = 'none';
    });
    modalElements.prev.addEventListener('click', () => {
        currentIndex = (currentIndex - 1 + galleryImages.length) % galleryImages.length;
        modalElements.img.src = galleryImages[currentIndex].src;
        checkNavButtons();
    });
    modalElements.next.addEventListener('click', () => {
        currentIndex = (currentIndex + 1) % galleryImages.length;
        modalElements.img.src = galleryImages[currentIndex].src;
        checkNavButtons();
    });
    function checkNavButtons() {
        if (galleryImages.length <= 1) {
            modalElements.prev.style.display = 'none';
            modalElements.next.style.display = 'none';
        }
    }
    document.addEventListener('keydown', (e) => {
        if (modal.style.display === 'flex') {
            if (e.key === 'Escape') modal.style.display = 'none';
            else if (e.key === 'ArrowLeft') modalElements.prev.click();
            else if (e.key === 'ArrowRight') modalElements.next.click();
        }
    });
}

function setupNavigation() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
    
    const navLinks = document.querySelectorAll('nav a');
    navLinks.forEach(link => {
        link.addEventListener('mouseover', function() {
            this.style.transform = 'translateY(-3px)';
            this.style.color = '#fff';
        });
        
        link.addEventListener('mouseout', function() {
            this.style.transform = 'translateY(0)';
        });
    });
    
    document.querySelectorAll('.social-icons img').forEach(icon => {
        icon.classList.add('icon-pulse');
    });
}

function setupContactInteractions() {
    const contactIcons = document.querySelectorAll('.contact-icon');
    
    contactIcons.forEach(icon => {
        if (!icon.dataset.text) return;
        
        icon.addEventListener('click', function() {
            this.style.animation = 'clickPulse 0.4s cubic-bezier(0.34, 1.56, 0.64, 1)';
            
            setTimeout(() => {
                this.style.animation = '';
            }, 400);
            
            const iconType = this.alt.toLowerCase();
            
            if (iconType === 'email') {
                window.open('mailto:' + this.dataset.text, '_blank');
            } 
            else if (iconType === 'instagram') {
                window.open('https://instagram.com/' + this.dataset.text.replace('@', ''), '_blank');
            }
            else if (iconType === 'telepon') {
                navigator.clipboard.writeText(this.dataset.text)
                    .then(() => showCopyNotification(this.dataset.text))
                    .catch(err => console.error('Failed to copy text:', err));
            }
        });
    });
}
function showCopyNotification(text) {
    const existingNotification = document.querySelector('.copy-notification');
    if (existingNotification) {
        existingNotification.remove();
    }  
    const notification = document.createElement('div');
    notification.className = 'copy-notification';
    notification.textContent = `${text} telah disalin!`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 2000);
}

function createScrollToTopButton() {
    const scrollTopBtn = document.createElement('button');
    scrollTopBtn.innerHTML = '&uarr;';
    scrollTopBtn.className = 'scroll-top-btn';
    scrollTopBtn.style.cssText = 'position:fixed; bottom:20px; right:20px; display:none; z-index:99; width:40px; height:40px; border-radius:50%; font-size:20px; line-height:1; padding:0;';
    document.body.appendChild(scrollTopBtn);
    window.addEventListener('scroll', function() {
        if (document.documentElement.scrollTop > 300) {
            scrollTopBtn.style.display = 'block';
            scrollTopBtn.style.animation = 'fadeIn 0.3s ease';
        } else {
            scrollTopBtn.style.display = 'none';
        }
    });
    
    scrollTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

function initScrollAnimations() {
    const sections = document.querySelectorAll('section');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1 });
    sections.forEach(section => {
        section.classList.add('fade-in');
        observer.observe(section);
    });
}

function initTypingAnimation() {
    const welcomeHeading = document.querySelector('.welcome-section h2');
    if (!welcomeHeading) return;
    if (!window.location.pathname.includes('index.html') && !window.location.pathname.endsWith('/')) return;
    const welcomeText = welcomeHeading.textContent;
    welcomeHeading.textContent = '';
    welcomeHeading.style.minHeight = '1.8em';
    let i = 0;
    function typeWriter() {
        if (i < welcomeText.length) {
            welcomeHeading.textContent += welcomeText.charAt(i);
            i++;
            setTimeout(typeWriter, 30);
        }
    }
    setTimeout(typeWriter, 400);
}