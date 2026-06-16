// ===== Page Transition — sync (runs before paint) =====
(function(){
    var e = document.getElementById('pageTransition');
    if (!e) return;
    if (sessionStorage.getItem('sirs_ts') === '1') {
        sessionStorage.removeItem('sirs_ts');
        window.__sirs = 1;
        e.style.transform = 'translateX(0)';
        e.style.transition = 'none';
    } else {
        e.style.transform = 'translateX(100%)';
        e.style.transition = 'none';
    }
})();

// ===== Landing Page — Mobile Menu & Smooth Scroll =====
(function(){
    var btn = document.getElementById('mobileMenuBtn');
    if (btn) {
        btn.addEventListener('click', function(){
            document.querySelector('.landing-nav-links').classList.toggle('open');
            var icon = this.querySelector('i');
            if (icon) { icon.classList.toggle('fa-bars'); icon.classList.toggle('fa-times'); }
        });
    }
    var scrollBtn = document.querySelector('.landing-scroll');
    if (scrollBtn) {
        scrollBtn.addEventListener('click', function(){
            var target = document.getElementById('features');
            if (target) target.scrollIntoView({behavior:'smooth'});
        });
    }
})();

document.addEventListener('DOMContentLoaded', function () {

    // ===== Slideshow Page Transition =====
    var overlay = document.getElementById('pageTransition');
    if (overlay) {
        // If sync script flagged a transition, play slide-in
        if (window.__sirs) {
            delete window.__sirs;
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    overlay.style.transition = 'transform 0.4s cubic-bezier(0.4,0,0.2,1)';
                    overlay.style.transform = 'translateX(-100%)';
                });
            });
            setTimeout(function () {
                overlay.style.transition = 'none';
                overlay.style.transform = 'translateX(100%)';
            }, 450);
        }

        // Handle browser back/forward navigation
        window.addEventListener('pageshow', function (e) {
            if (e.persisted) {
                overlay.style.transition = 'none';
                overlay.style.transform = 'translateX(0)';
                requestAnimationFrame(function () {
                    requestAnimationFrame(function () {
                        overlay.style.transition = 'transform 0.4s cubic-bezier(0.4,0,0.2,1)';
                        overlay.style.transform = 'translateX(-100%)';
                    });
                });
                setTimeout(function () {
                    overlay.style.transition = 'none';
                    overlay.style.transform = 'translateX(100%)';
                }, 450);
            }
        });

        // Intercept internal links
        document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href*="logout"]):not(.pw-toggle)').forEach(function (link) {
            link.addEventListener('click', function (e) {
                var href = this.getAttribute('href');
                if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('javascript:')) {
                    e.preventDefault();
                    sessionStorage.setItem('sirs_ts', '1');
                    overlay.style.transition = 'none';
                    overlay.style.transform = 'translateX(100%)';
                    void overlay.offsetHeight;
                    overlay.style.transition = 'transform 0.35s cubic-bezier(0.4,0,0.2,1)';
                    overlay.style.transform = 'translateX(0)';
                    setTimeout(function () { window.location.href = href; }, 350);
                }
            });
        });
    }

    // ===== Dark Mode =====
    const themeToggle = document.getElementById('themeToggle');
    if (localStorage.getItem('theme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }
    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const html = document.documentElement;
            const isDark = html.getAttribute('data-theme') === 'dark';
            if (isDark) {
                html.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                showToast('Light mode activated', 'blue');
            } else {
                html.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                showToast('Dark mode activated', 'blue');
            }
        });
    }

    // ===== Toast =====
    window.showToast = function (message, type) {
        const container = document.getElementById('toast-wrap');
        if (!container) return;
        const icons = { green: '&#10003;', red: '&#9888;', blue: '&#8505;' };
        const toast = document.createElement('div');
        toast.className = 'toast toast-' + type;
        toast.innerHTML = '<span>' + (icons[type] || '') + '</span><span>' + message + '</span><button onclick="this.parentElement.classList.add(\'out\');setTimeout(()=>this.parentElement.remove(),300)">&times;</button>';
        container.appendChild(toast);
        setTimeout(function () {
            if (toast.parentElement) {
                toast.classList.add('out');
                setTimeout(function () { if (toast.parentElement) toast.remove(); }, 300);
            }
        }, 4000);
    };



    // ===== Stat Counter Animation =====
    document.querySelectorAll('.stat-num').forEach(function (el) {
        var target = parseInt(el.getAttribute('data-count'));
        var duration = 600;
        var start = performance.now();
        function animate(now) {
            var elapsed = now - start;
            var progress = Math.min(elapsed / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3);
            el.textContent = Math.floor(eased * target);
            if (progress < 1) requestAnimationFrame(animate);
        }
        requestAnimationFrame(animate);
    });

    // ===== Password Toggle =====
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = this.parentElement.querySelector('input');
            var icon = this.querySelector('i');
            if (!input || !icon) return;
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'far fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'far fa-eye';
            }
        });
    });

    // ===== Password Strength =====
    var pwInput = document.getElementById('regPassword') || document.getElementById('resetPassword');
    var pwBar = document.getElementById('pwStrengthBar');
    var pwHint = document.getElementById('pwHint');
    if (pwInput && pwBar) {
        pwInput.addEventListener('input', function () {
            var val = this.value;
            var score = 0;
            if (val.length >= 6) score += 25;
            if (val.length >= 10) score += 15;
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) score += 20;
            if (/\d/.test(val)) score += 20;
            if (/[^a-zA-Z0-9]/.test(val)) score += 20;
            score = Math.min(score, 100);
            pwBar.style.width = score + '%';
            if (score < 25) {
                pwBar.style.background = '#ef4444';
                if (pwHint) pwHint.textContent = 'Too weak — add more characters';
            } else if (score < 50) {
                pwBar.style.background = '#f59e0b';
                if (pwHint) pwHint.textContent = 'Could be stronger — mix upper, lower & numbers';
            } else if (score < 75) {
                pwBar.style.background = '#10b981';
                if (pwHint) pwHint.textContent = 'Good password!';
            } else {
                pwBar.style.background = '#059669';
                if (pwHint) pwHint.textContent = 'Strong password!';
            }
            if (pwHint) pwHint.classList.add('show');
            if (val.length === 0) {
                pwBar.style.width = '0%';
                if (pwHint) pwHint.classList.remove('show');
            }
        });
    }

    // ===== Password Rules =====
    var rulesList = document.getElementById('pwRules');
    var regPwInput = document.getElementById('regPassword') || document.getElementById('resetPassword');
    if (rulesList && regPwInput) {
        regPwInput.addEventListener('focus', function () {
            rulesList.classList.add('show');
        });
        regPwInput.addEventListener('blur', function () {
            if (this.value.length === 0) rulesList.classList.remove('show');
        });
        regPwInput.addEventListener('input', function () {
            var val = this.value;
            rulesList.querySelectorAll('li').forEach(function (li) {
                var rule = li.getAttribute('data-rule');
                var pass = false;
                if (rule === 'length') pass = val.length >= 6;
                else if (rule === 'lower') pass = /[a-z]/.test(val);
                else if (rule === 'upper') pass = /[A-Z]/.test(val);
                else if (rule === 'number') pass = /\d/.test(val);
                else if (rule === 'special') pass = /[^a-zA-Z0-9]/.test(val);
                li.className = pass ? 'met' : 'unmet';
            });
            if (val.length > 0) rulesList.classList.add('show');
        });
    }

    // ===== Confirm Password Match =====
    var pwField = document.getElementById('regPassword') || document.getElementById('resetPassword');
    var confirmPw = document.getElementById('regConfirmPw') || document.getElementById('resetConfirmPw');
    if (pwField && confirmPw) {
        confirmPw.addEventListener('input', function () {
            if (this.value.length === 0) {
                this.style.borderColor = '';
                return;
            }
            if (this.value === pwField.value) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });
        pwField.addEventListener('input', function () {
            if (confirmPw.value.length > 0) {
                if (confirmPw.value === this.value) {
                    confirmPw.style.borderColor = '#10b981';
                } else {
                    confirmPw.style.borderColor = '#ef4444';
                }
            }
        });
    }

    // ===== Button Loading State =====
    var authForm = document.getElementById('authForm');
    if (authForm) {
        authForm.addEventListener('submit', function () {
            var btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner"></span> ' + (btn.textContent.trim() || 'Please wait...');
            }
        });
    }

    // ===== Shake Cleanup =====
    var shakeCard = document.querySelector('.auth-card.shake');
    if (shakeCard) {
        shakeCard.addEventListener('animationend', function () {
            this.classList.remove('shake');
        });
    }

    // ===== Email Validation (Register) =====
    var emailInput = document.querySelector('input[name="email"]');
    if (emailInput) {
        emailInput.addEventListener('blur', function () {
            var val = this.value.trim();
            if (val.length === 0) {
                this.style.borderColor = '';
                return;
            }
            if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
                this.style.borderColor = '#10b981';
            } else {
                this.style.borderColor = '#ef4444';
            }
        });
        emailInput.addEventListener('input', function () {
            if (this.value.trim().length === 0) {
                this.style.borderColor = '';
            }
        });
    }

});
