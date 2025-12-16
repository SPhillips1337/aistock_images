// Cookie Consent Management
class CookieConsent {
    constructor() {
        this.consentCookieName = 'cookie_consent';
        this.preferencesCookieName = 'cookie_preferences';
        this.init();
    }

    init() {
        this.bindEvents();
        
        // Show banner if no consent has been given
        if (!this.hasConsent()) {
            this.showBanner();
        } else {
            // Apply saved preferences
            const preferences = this.getPreferences();
            if (preferences) {
                this.applyPreferences(preferences);
            }
        }
    }

    bindEvents() {
        // Accept all cookies button
        document.getElementById('acceptAllCookies')?.addEventListener('click', () => {
            this.acceptAll();
        });

        // Accept essential only button
        document.getElementById('acceptEssentialCookies')?.addEventListener('click', () => {
            this.acceptEssential();
        });

        // Settings button
        document.getElementById('cookieSettings')?.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('cookieSettingsModal'));
            modal.show();
        });

        // Save preferences button
        document.getElementById('saveCookieSettings')?.addEventListener('click', () => {
            this.savePreferences();
        });
    }

    showBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.classList.add('show');
        }
    }

    hideBanner() {
        const banner = document.getElementById('cookieConsent');
        if (banner) {
            banner.classList.remove('show');
        }
    }

    hasConsent() {
        return this.getCookie(this.consentCookieName) !== null;
    }

    getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }

    setCookie(name, value, days = 365) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = `expires=${date.toUTCString()}`;
        document.cookie = `${name}=${value};${expires};path=/;SameSite=Lax`;
    }

    acceptAll() {
        const preferences = {
            essential: true,
            analytics: true,
            marketing: true
        };

        this.setCookie(this.consentCookieName, 'accepted', 365);
        this.setCookie(this.preferencesCookieName, JSON.stringify(preferences), 365);
        
        this.hideBanner();
        this.applyPreferences(preferences);
        this.loadOptionalScripts(preferences);
    }

    acceptEssential() {
        const preferences = {
            essential: true,
            analytics: false,
            marketing: false
        };

        this.setCookie(this.consentCookieName, 'essential', 365);
        this.setCookie(this.preferencesCookieName, JSON.stringify(preferences), 365);
        
        this.hideBanner();
        this.applyPreferences(preferences);
    }

    savePreferences() {
        const preferences = {
            essential: true,
            analytics: document.getElementById('analyticsCookies').checked,
            marketing: document.getElementById('marketingCookies').checked
        };

        this.setCookie(this.consentCookieName, 'custom', 365);
        this.setCookie(this.preferencesCookieName, JSON.stringify(preferences), 365);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('cookieSettingsModal'));
        modal.hide();
        
        // Hide banner if still visible
        this.hideBanner();
        
        this.applyPreferences(preferences);
        this.loadOptionalScripts(preferences);
    }

    getPreferences() {
        const preferences = this.getCookie(this.preferencesCookieName);
        return preferences ? JSON.parse(preferences) : null;
    }

    applyPreferences(preferences) {
        // Update checkboxes in settings modal
        if (document.getElementById('analyticsCookies')) {
            document.getElementById('analyticsCookies').checked = preferences.analytics;
        }
        if (document.getElementById('marketingCookies')) {
            document.getElementById('marketingCookies').checked = preferences.marketing;
        }
    }

    loadOptionalScripts(preferences) {
        // Load analytics scripts if consented
        if (preferences.analytics) {
            // Example: Google Analytics (replace with your tracking ID)
            // this.loadGoogleAnalytics();
        }

        // Load marketing scripts if consented
        if (preferences.marketing) {
            // Example: Facebook Pixel, Google Ads, etc.
            // this.loadMarketingScripts();
        }
    }

    loadGoogleAnalytics() {
        // Example implementation
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
        
        // Replace with your actual tracking ID
        // ga('create', 'UA-XXXXX-Y', 'auto');
        // ga('send', 'pageview');
    }

    loadMarketingScripts() {
        // Add marketing/advertising scripts here
        console.log('Marketing scripts would be loaded here');
    }

    // Method to check if user has consented to specific cookie type
    hasConsentFor(type) {
        const preferences = this.getPreferences();
        return preferences && preferences[type] === true;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new CookieConsent();
});

// Export for potential use in other scripts
window.CookieConsent = CookieConsent;