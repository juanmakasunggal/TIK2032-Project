// Wait for DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Set active navigation link based on current page
    setActiveNavLink();
    
    // Initialize dark mode toggle
    initDarkModeToggle();
    
    // Initialize page-specific functions (dengan perbaikan)
    try {
        initPageSpecificFunctions();
    } catch (error) {
        console.log('Error in page-specific functions:', error);
    }
    
    // Add fade-in animation to main container (dengan pengecekan)
    const container = document.querySelector('.container');
    if (container) {
        container.classList.add('fade-in');
    }
});

// Set active navigation link based on current URL
function setActiveNavLink() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('.nav-links a');
    
    navLinks.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref === currentPage) {
            link.classList.add('active');
        }
    });
}

// Initialize dark mode toggle
function initDarkModeToggle() {
    // Create toggle button if it doesn't exist
    if (!document.querySelector('.mode-toggle')) {
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mode-toggle';
        toggleBtn.innerHTML = '🌙';
        toggleBtn.title = 'Toggle Dark/Light Mode';
        document.body.appendChild(toggleBtn);
        
        // Check if dark mode is enabled in localStorage
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            toggleBtn.innerHTML = '☀️';
        }
        
        // Add click event listener to toggle button
        toggleBtn.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            toggleBtn.innerHTML = isDark ? '☀️' : '🌙';
        });
    }
}

function initDarkModeToggle() {
    console.log('Dark mode toggle initialization started');
    // Create toggle button if it doesn't exist
    if (!document.querySelector('.mode-toggle')) {
        console.log('Creating dark mode toggle button');
        const toggleBtn = document.createElement('button');
        toggleBtn.className = 'mode-toggle';
        toggleBtn.innerHTML = '🌙';
        toggleBtn.title = 'Toggle Dark/Light Mode';
        document.body.appendChild(toggleBtn);
        
        // Check if dark mode is enabled in localStorage
        const isDarkMode = localStorage.getItem('darkMode') === 'true';
        console.log('Is dark mode enabled in localStorage:', isDarkMode);
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
            toggleBtn.innerHTML = '☀️';
        }
        
        // Add click event listener to toggle button
        toggleBtn.addEventListener('click', function() {
            console.log('Dark mode toggle clicked');
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDark);
            toggleBtn.innerHTML = isDark ? '☀️' : '🌙';
        });
    }
}

// Initialize page-specific functions based on current page
function initPageSpecificFunctions() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    
    switch(currentPage) {
        case 'index.html':
            try { initHomePage(); } catch(e) { console.log('Error initializing home page:', e); }
            break;
        case 'gallery.html':
            try { initGalleryPage(); } catch(e) { console.log('Error initializing gallery page:', e); }
            break;
        case 'blog.html':
            try { initBlogPage(); } catch(e) { console.log('Error initializing blog page:', e); }
            break;
        case 'contact.html':
            try { initContactPage(); } catch(e) { console.log('Error initializing contact page:', e); }
            break;
    }
}

// Home page specific functions
function initHomePage() {
    // Add typing animation to welcome heading
    const welcomeHeading = document.querySelector('h1');
    if (welcomeHeading) {
        const text = welcomeHeading.textContent;
        welcomeHeading.textContent = '';
        let i = 0;
        
        function typeWriter() {
            if (i < text.length) {
                welcomeHeading.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            }
        }
        
        typeWriter();
    }
}

// Perbaikan untuk gallery page specific functions
function initGalleryPage() {
    console.log("Initializing gallery page...");
    
    // Mencari container gambar
    const galleryContainer = document.querySelector('.gallery-container');
    if (!galleryContainer) {
        console.log("Gallery container not found, creating one...");
        
        // Cari section gallery
        const gallerySection = document.getElementById('gallery');
        if (!gallerySection) {
            console.log("Gallery section not found");
            return;
        }
        
        // Buat container baru jika belum ada
        const newGalleryContainer = document.createElement('div');
        newGalleryContainer.className = 'gallery-container';
        
        // Ambil semua gambar di dalam section
        const images = gallerySection.querySelectorAll('img');
        
        if (images.length === 0) {
            console.log("No images found in gallery section");
            return;
        }
        
        // Pindahkan gambar ke dalam gallery container dengan wrapper
        images.forEach(img => {
            // Hapus atribut width inline yang mungkin mengganggu styling
            img.removeAttribute('width');
            
            // Buat wrapper untuk gambar
            const galleryItem = document.createElement('div');
            galleryItem.className = 'gallery-item';
            
            // Clone gambar
            const imgClone = img.cloneNode(true);
            
            // Tambahkan ke gallery item
            galleryItem.appendChild(imgClone);
            
            // Tambahkan event listener untuk lightbox
            galleryItem.addEventListener('click', function() {
                openLightbox(imgClone.src, imgClone.alt);
            });
            
            // Tambahkan item ke container
            newGalleryContainer.appendChild(galleryItem);
        });
        
        // Hapus semua gambar asli
        images.forEach(img => img.remove());
        
        // Tambahkan container baru ke section
        gallerySection.appendChild(newGalleryContainer);
    } else {
        console.log("Gallery container found, updating items...");
        
        // Gambar sudah ada dalam container, tambahkan event listeners
        const galleryItems = galleryContainer.querySelectorAll('img');
        
        galleryItems.forEach(img => {
            // Hapus atribut width inline
            img.removeAttribute('width');
            
            // Buat wrapper jika belum ada
            if (img.parentNode.className !== 'gallery-item') {
                const galleryItem = document.createElement('div');
                galleryItem.className = 'gallery-item';
                
                // Clone gambar
                const imgClone = img.cloneNode(true);
                
                // Ganti gambar asli dengan wrapper
                galleryItem.appendChild(imgClone);
                img.parentNode.replaceChild(galleryItem, img);
                
                // Tambahkan event listener untuk lightbox
                galleryItem.addEventListener('click', function() {
                    openLightbox(imgClone.src, imgClone.alt);
                });
            } else {
                // Sudah dalam wrapper, tambahkan event listener saja
                img.parentNode.addEventListener('click', function() {
                    openLightbox(img.src, img.alt);
                });
            }
        });
    }
    
    // Tambahkan CSS untuk gallery items jika belum ada di styles.css
    addGalleryStyles();
}

// Function untuk menambahkan gallery styles jika belum ada
function addGalleryStyles() {
    // Cek apakah styles sudah ada
    const existingStyle = document.getElementById('dynamic-gallery-styles');
    if (existingStyle) return;
    
    const styleElement = document.createElement('style');
    styleElement.id = 'dynamic-gallery-styles';
    styleElement.textContent = `
        .gallery-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 20px auto;
        }
        
        .gallery-item {
            width: 30%;
            cursor: pointer;
            transition: transform 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .gallery-item:hover {
            transform: scale(1.05);
        }
        
        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .gallery-item {
                width: 45%;
            }
        }
        
        @media (max-width: 480px) {
            .gallery-item {
                width: 100%;
            }
        }
        
        /* Lightbox styles */
        .lightbox {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .lightbox.show {
            opacity: 1;
        }
        
        .lightbox img {
            max-width: 90%;
            max-height: 90%;
            border: 2px solid white;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }
        
        .lightbox-close {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 40px;
            color: white;
            cursor: pointer;
            z-index: 1001;
        }
    `;
    
    document.head.appendChild(styleElement);
}

// Improved lightbox function
function openLightbox(imgSrc, imgAlt) {
    console.log("Opening lightbox for:", imgSrc);
    
    // Remove existing lightbox if any
    const existingLightbox = document.querySelector('.lightbox');
    if (existingLightbox) {
        existingLightbox.remove();
    }
    
    // Create lightbox container
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    
    // Create image element
    const img = document.createElement('img');
    img.src = imgSrc;
    img.alt = imgAlt || 'Gallery Image';
    
    // Create close button
    const closeBtn = document.createElement('span');
    closeBtn.className = 'lightbox-close';
    closeBtn.innerHTML = '&times;';
    
    // Add elements to lightbox
    lightbox.appendChild(img);
    lightbox.appendChild(closeBtn);
    
    // Add lightbox to body
    document.body.appendChild(lightbox);
    
    // Add animation effect
    setTimeout(() => {
        lightbox.classList.add('show');
    }, 10);
    
    // Add event listeners for closing
    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Close on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });
    
    // Prevent scrolling on body
    document.body.style.overflow = 'hidden';
    
    // Function to close lightbox
    function closeLightbox() {
        lightbox.classList.remove('show');
        setTimeout(() => {
            lightbox.remove();
            document.body.style.overflow = '';
        }, 300);
    }
}

// Add this to your existing DOMContentLoaded event
document.addEventListener('DOMContentLoaded', function() {
    // Check if this is the gallery page
    const isGalleryPage = window.location.pathname.includes('gallery.html') || 
                         document.title.includes('Gallery');
    
    if (isGalleryPage) {
        console.log("Gallery page detected, initializing gallery...");
        initGalleryPage();
    }
} );

// Blog page specific functions
function initBlogPage() {
    // Add read more functionality to blog posts
    const blogPosts = document.querySelectorAll('h3 + p');
    if (!blogPosts || blogPosts.length === 0) return; // Hentikan jika tidak ada postingan blog
    
    blogPosts.forEach(post => {
        // Pastikan post dan elemen sekitarnya ada
        if (!post || !post.previousElementSibling) return;
        
        // Wrap each blog post in a container
        const postContainer = document.createElement('div');
        postContainer.className = 'blog-post';
        
        // Get the title (h3 element)
        const title = post.previousElementSibling;
        
        // Create blog meta element
        const blogMeta = document.createElement('div');
        blogMeta.className = 'blog-meta';
        blogMeta.textContent = `Posted on ${getRandomDate()} · ${Math.floor(Math.random() * 10) + 1} min read`;
        
        // Limit the content to 150 characters
        const fullContent = post.innerHTML;
        const limitedContent = fullContent.substring(0, 150).trim() + '...';
        post.innerHTML = limitedContent;
        post.className = 'blog-content';
        
        // Create read more link
        const readMoreLink = document.createElement('a');
        readMoreLink.href = 'javascript:void(0)';
        readMoreLink.className = 'read-more';
        readMoreLink.textContent = 'Read More';
        
        let isExpanded = false;
        readMoreLink.addEventListener('click', function() {
            if (isExpanded) {
                post.innerHTML = limitedContent;
                readMoreLink.textContent = 'Read More';
            } else {
                post.innerHTML = fullContent;
                readMoreLink.textContent = 'Show Less';
            }
            isExpanded = !isExpanded;
        });
        
        // Rearrange elements
        if (post.parentNode) {
            post.parentNode.insertBefore(postContainer, title);
            postContainer.appendChild(title);
            postContainer.appendChild(blogMeta);
            postContainer.appendChild(post);
            postContainer.appendChild(readMoreLink);
        }
    });
}

// Contact page specific functions
function initContactPage() {
    // Mencari container konten
    const contactHeading = document.querySelector('h1');
    if (!contactHeading || !contactHeading.textContent.includes("Contact")) {
        console.log("Contact heading not found, skipping contact page initialization");
        return;
    }
    
    const contactSection = contactHeading.parentNode;
    if (!contactSection) {
        console.log("Contact section not found, skipping contact page initialization");
        return;
    }
    
    // Dapatkan semua elemen yang ada di halaman kontak
    const existingElements = contactSection.querySelectorAll('*');
    console.log("Existing elements in contact page:", existingElements.length);
    
    // Cek apakah info kontak sudah ada dalam format yang diharapkan
    const existingContactInfo = contactSection.querySelector('.contact-info');
    if (!existingContactInfo) {
        // Buat contact info container
        const contactInfo = document.createElement('div');
        contactInfo.className = 'contact-info';
        
        // Sisipkan info kontak setelah heading
        contactHeading.after(contactInfo);
    }
    
    // Form kontak dihapus sesuai permintaan (bagian yang ada di gambar)
    // Kode pembuatan form kontak sengaja dihilangkan di sini
}

// Helper function to get random date for blog posts
function getRandomDate() {
    const start = new Date(2025, 0, 1);
    const end = new Date();
    const randomDate = new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
    return randomDate.toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' });
}
// Fungsi tambahan untuk penyesuaian mobile
document.addEventListener('DOMContentLoaded', function() {
    // Fungsi yang sudah ada ...
    
    // Deteksi apakah perangkat mobile
    const isMobile = window.innerWidth <= 768;
    
    // Penyesuaian animasi untuk mobile
    if(isMobile) {
        // Kurangi delay animasi typing untuk mobile
        const welcomeHeading = document.querySelector('.welcome-heading');
        if(welcomeHeading) {
            const text = welcomeHeading.textContent;
            welcomeHeading.textContent = '';
            let i = 0;
            
            // Typing lebih cepat untuk mobile
            function typeWriter() {
                if (i < text.length) {
                    welcomeHeading.textContent += text.charAt(i);
                    i++;
                    setTimeout(typeWriter, 30); // Lebih cepat
                }
            }
            
            // Delay memulai typing
            setTimeout(typeWriter, 500);
        }
        
        // Perbaiki ukuran lightbox untuk mobile
        const lightboxOpenFn = openLightbox;
        window.openLightbox = function(imgSrc, imgAlt) {
            // Panggil fungsi asli
            lightboxOpenFn(imgSrc, imgAlt);
            
            // Tambahkan pengaturan lebih untuk mobile
            const lightbox = document.querySelector('.lightbox');
            if(lightbox) {
                const img = lightbox.querySelector('img');
                if(img) {
                    img.style.maxWidth = '95%';
                    img.style.maxHeight = '80%';
                }
                
                // Tombol close lebih besar
                const closeBtn = lightbox.querySelector('span');
                if(closeBtn) {
                    closeBtn.style.fontSize = '50px';
                    closeBtn.style.top = '15px';
                    closeBtn.style.right = '15px';
                }
            }
        };
        
        // Mengatasi masalah tap focus pada mobile
        document.querySelectorAll('a, button').forEach(el => {
            el.addEventListener('touchstart', function(){
                // Hanya untuk efek visual pada touch
                this.classList.add('touch-active');
            });
            
            el.addEventListener('touchend', function(){
                this.classList.remove('touch-active');
            });
        });
    }
    
    // Tambahkan kelas khusus pada body untuk deteksi mobile via CSS
    if(isMobile) {
        document.body.classList.add('is-mobile');
    }
    
    // Perbaiki penanganan form contact di mobile
    const contactForm = document.getElementById('contactForm') || document.getElementById('contact-form');
    if(contactForm) {
        const inputs = contactForm.querySelectorAll('input, textarea');
        
        // Tambahkan handling untuk input focus di mobile
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                // Scroll ke elemen ketika difokuskan
                if(isMobile) {
                    setTimeout(() => {
                        this.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 300);
                }
            });
        });
    }
    
    // Cek ukuran image dan lakukan lazy loading untuk mobile
    const images = document.querySelectorAll('img');
    if(isMobile && images.length > 0) {
        images.forEach(img => {
            // Tambahkan lazy loading untuk performa
            img.loading = 'lazy';
            
            // Pastikan gambar memiliki alt text
            if(!img.alt) img.alt = 'Image';
        });
    }
});

// Tambahkan event resize untuk menangani perubahan orientasi layar
window.addEventListener('resize', function() {
    const isMobile = window.innerWidth <= 768;
    
    if(isMobile) {
        document.body.classList.add('is-mobile');
    } else {
        document.body.classList.remove('is-mobile');
    }
    
    // Recalculate gallery layout
    const galleryContainer = document.querySelector('.gallery-container');
    if(galleryContainer) {
        const items = galleryContainer.querySelectorAll('.gallery-item');
        if(items.length > 0) {
            // Force reflow untuk layout yang lebih baik
            galleryContainer.style.display = 'none';
            setTimeout(() => {
                galleryContainer.style.display = '';
            }, 10);
        }
    }
});

// Perbaikan untuk scroll dan interaksi sentuh di mobile
if('ontouchstart' in window) {
    document.documentElement.classList.add('touchscreen');
    
    // Perbaiki scrolling untuk iOS
    document.addEventListener('touchmove', function(e) {
        // Biarkan browser menangani semua touch event secara default
    }, { passive: true });
}
// Tambahkan fungsi ini ke bagian bawah scripts.js untuk memperbaiki animasi typing di mobile

// Fungsi perbaikan animasi typing pada perangkat mobile
function fixMobileTypingAnimation() {
    // Cek apakah ini halaman home
    const welcomeHeading = document.querySelector('.welcome-heading');
    if (!welcomeHeading) return;
    
    // Cek apakah tampilan mobile
    const isMobile = window.innerWidth <= 768;
    
    if (isMobile) {
        // Override fungsi typing yang mungkin sudah ada
        // Dapatkan teks asli
        const originalText = 'Selamat Datang di Personal Homepage Jennifer Manoppo';
        welcomeHeading.textContent = '';
        
        // Fungsi typing yang lebih cepat dan aman untuk mobile
        let i = 0;
        function mobileTypeWriter() {
            if (i < originalText.length) {
                welcomeHeading.textContent += originalText.charAt(i);
                i++;
                // Lebih cepat di mobile (20ms vs 50ms di desktop)
                setTimeout(mobileTypeWriter, 20);
            }
        }
        
        // Mulai animasi typing dengan sedikit delay
        setTimeout(() => {
            mobileTypeWriter();
        }, 500);
    }
}

// Tambahkan ke event DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    // Panggil fungsi lain yang sudah ada...
    
    // Tambahkan perbaikan animasi typing untuk mobile
    fixMobileTypingAnimation();
});

// Juga tangani event resize untuk kasus rotasi layar
window.addEventListener('resize', function() {
    // Cek jika ukuran layar berubah dari desktop ke mobile atau sebaliknya
    const wasMobile = window.wasMobile;
    const isMobile = window.innerWidth <= 768;
    window.wasMobile = isMobile;
    
    // Jika berubah dari desktop ke mobile atau sebaliknya, refresh halaman
    if (wasMobile !== undefined && wasMobile !== isMobile) {
        // Hanya refresh jika ini adalah halaman home
        if (document.querySelector('.welcome-heading')) {
            location.reload();
        }
    }
});

// Standalone Dark Mode implementation
document.addEventListener('DOMContentLoaded', function() {
    // Create dark mode toggle button if it doesn't exist already
    if (!document.querySelector('.mode-toggle')) {
        console.log('Adding standalone dark mode toggle');
        
        // Create button
        const darkModeToggle = document.createElement('button');
        darkModeToggle.className = 'mode-toggle';
        darkModeToggle.innerHTML = localStorage.getItem('darkMode') === 'true' ? '☀️' : '🌙';
        darkModeToggle.title = 'Toggle Dark/Light Mode';
        
        // Add styles directly to ensure visibility
        darkModeToggle.style.position = 'fixed';
        darkModeToggle.style.bottom = '20px';
        darkModeToggle.style.right = '20px';
        darkModeToggle.style.width = '40px';
        darkModeToggle.style.height = '40px';
        darkModeToggle.style.borderRadius = '50%';
        darkModeToggle.style.fontSize = '20px';
        darkModeToggle.style.background = '#fff';
        darkModeToggle.style.border = '2px solid #ccc';
        darkModeToggle.style.cursor = 'pointer';
        darkModeToggle.style.zIndex = '1000';
        darkModeToggle.style.display = 'flex';
        darkModeToggle.style.alignItems = 'center';
        darkModeToggle.style.justifyContent = 'center';
        
        // Add to document
        document.body.appendChild(darkModeToggle);
        
        // Check localStorage and apply dark mode if needed
        if (localStorage.getItem('darkMode') === 'true') {
            document.body.classList.add('dark-mode');
            
            // Add inline styles for immediate effect
            document.body.style.backgroundColor = '#121212';
            document.body.style.color = '#e0e0e0';
        }
        
        // Add click event
        darkModeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            
            // Apply/remove inline styles for immediate effect
            if (isDarkMode) {
                document.body.style.backgroundColor = '#121212';
                document.body.style.color = '#e0e0e0';
                this.innerHTML = '☀️';
            } else {
                document.body.style.backgroundColor = '';
                document.body.style.color = '';
                this.innerHTML = '🌙';
            }
            
            // Save preference
            localStorage.setItem('darkMode', isDarkMode);
        });
    }
});