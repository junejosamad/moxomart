/**
 * SadaCart Main JavaScript
 * Vanilla JS for enhanced functionality
 * Compiled to bundle.min.js
 */

// Declare AOS variable
const AOS = window.AOS

// Initialize AOS (Animate On Scroll)
document.addEventListener("DOMContentLoaded", () => {
  if (typeof AOS !== "undefined") {
    AOS.init({
      duration: 800,
      easing: "ease-in-out",
      once: true,
      offset: 100,
    })
  }
})

// Declare gtag variable
const gtag = window.gtag

// Global App Object
const SadaCart = {
  // Configuration
  config: {
    apiUrl: "/api",
    cartUrl: "/cart",
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
  },

  // Initialize application
  init() {
    this.initEventListeners()
    this.initBackToTop()
    this.initMiniCart()
    this.initNewsletterForm()
    this.initSearchAutocomplete()
    this.updateCartCount()
  },

  // Event Listeners
  initEventListeners() {
    // Add to cart forms
    document.addEventListener("submit", (e) => {
      if (e.target.classList.contains("add-to-cart-form")) {
        e.preventDefault()
        this.addToCart(e.target)
      }
    })

    // Quick add to cart buttons
    document.addEventListener("click", (e) => {
      if (e.target.classList.contains("add-to-cart-btn") || e.target.closest(".add-to-cart-btn")) {
        e.preventDefault()
        const button = e.target.classList.contains("add-to-cart-btn") ? e.target : e.target.closest(".add-to-cart-btn")
        this.quickAddToCart(button)
      }
    })

    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector(".navbar-toggler")
    if (mobileMenuToggle) {
      mobileMenuToggle.addEventListener("click", this.toggleMobileMenu)
    }

    // Search form enhancements
    const searchForm = document.querySelector('form[action*="search"]')
    if (searchForm) {
      searchForm.addEventListener("submit", this.handleSearch)
    }
  },

  // Back to Top Button
  initBackToTop() {
    const backToTopBtn = document.getElementById("back-to-top")
    if (!backToTopBtn) return

    window.addEventListener("scroll", () => {
      if (window.pageYOffset > 300) {
        backToTopBtn.style.display = "block"
      } else {
        backToTopBtn.style.display = "none"
      }
    })

    backToTopBtn.addEventListener("click", () => {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      })
    })
  },

  // Mini Cart Functionality
  initMiniCart() {
    const cartDropdown = document.getElementById("cartDropdown")
    if (!cartDropdown) return

    cartDropdown.addEventListener("show.bs.dropdown", () => {
      this.loadMiniCart()
    })
  },

  // Load mini cart content
  loadMiniCart() {
    const miniCartContent = document.getElementById("mini-cart-content")
    if (!miniCartContent) return

    fetch("/cart/mini")
      .then((response) => response.text())
      .then((html) => {
        miniCartContent.innerHTML = html
      })
      .catch((error) => {
        console.error("Error loading mini cart:", error)
        miniCartContent.innerHTML = '<div class="p-3 text-center text-muted">Error loading cart</div>'
      })
  },

  // Add to Cart
  addToCart(form) {
    const formData = new FormData(form)
    const button = form.querySelector('button[type="submit"]')
    const originalText = button.innerHTML

    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...'
    button.disabled = true

    fetch("/cart/add", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          this.showNotification("success", "Product added to cart successfully!")
          this.updateCartCount()
          this.animateCartIcon()
        } else {
          this.showNotification("error", data.message || "Failed to add product to cart")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        this.showNotification("error", "An error occurred. Please try again.")
      })
      .finally(() => {
        button.innerHTML = originalText
        button.disabled = false
      })
  },

  // Quick Add to Cart
  quickAddToCart(button) {
    const productId = button.dataset.productId
    if (!productId) return

    const formData = new FormData()
    formData.append("product_id", productId)
    formData.append("quantity", "1")
    formData.append("csrf_token", this.config.csrfToken)

    const originalContent = button.innerHTML
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>'
    button.disabled = true

    fetch("/cart/add", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          this.showNotification("success", "Product added to cart!")
          this.updateCartCount()
          this.animateCartIcon()
        } else {
          this.showNotification("error", data.message || "Failed to add product")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        this.showNotification("error", "An error occurred. Please try again.")
      })
      .finally(() => {
        button.innerHTML = originalContent
        button.disabled = false
      })
  },

  // Update Cart Count
  updateCartCount() {
    fetch("/cart/count")
      .then((response) => response.json())
      .then((data) => {
        const cartBadge = document.querySelector(".navbar .badge")
        if (cartBadge) {
          cartBadge.textContent = data.count
          cartBadge.style.display = data.count > 0 ? "inline" : "none"
        }
      })
      .catch((error) => {
        console.error("Error updating cart count:", error)
      })
  },

  // Animate Cart Icon
  animateCartIcon() {
    const cartIcon = document.querySelector(".navbar .fa-shopping-cart")
    if (cartIcon) {
      cartIcon.style.animation = "pulse 0.6s ease-in-out"
      setTimeout(() => {
        cartIcon.style.animation = ""
      }, 600)
    }
  },

  // Newsletter Form
  initNewsletterForm() {
    const newsletterForm = document.getElementById("newsletter-form")
    if (!newsletterForm) return

    newsletterForm.addEventListener("submit", (e) => {
      e.preventDefault()

      const email = newsletterForm.querySelector('input[type="email"]').value
      const button = newsletterForm.querySelector('button[type="submit"]')
      const originalText = button.textContent

      button.textContent = "Subscribing..."
      button.disabled = true

      const formData = new FormData()
      formData.append("email", email)
      formData.append("csrf_token", this.config.csrfToken)

      fetch("/api/newsletter/subscribe", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            this.showNotification("success", "Successfully subscribed to newsletter!")
            newsletterForm.reset()
          } else {
            this.showNotification("error", data.message || "Subscription failed")
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          this.showNotification("error", "An error occurred. Please try again.")
        })
        .finally(() => {
          button.textContent = originalText
          button.disabled = false
        })
    })
  },

  // Search Autocomplete
  initSearchAutocomplete() {
    const searchInput = document.querySelector('input[name="q"]')
    if (!searchInput) return

    let searchTimeout

    searchInput.addEventListener("input", (e) => {
      clearTimeout(searchTimeout)
      const query = e.target.value.trim()

      if (query.length < 2) {
        this.hideSearchSuggestions()
        return
      }

      searchTimeout = setTimeout(() => {
        this.fetchSearchSuggestions(query)
      }, 300)
    })

    // Hide suggestions when clicking outside
    document.addEventListener("click", (e) => {
      if (!e.target.closest(".search-container")) {
        this.hideSearchSuggestions()
      }
    })
  },

  // Fetch Search Suggestions
  fetchSearchSuggestions(query) {
    fetch(`/api/products/search?q=${encodeURIComponent(query)}&limit=5`)
      .then((response) => response.json())
      .then((data) => {
        this.showSearchSuggestions(data.products || [])
      })
      .catch((error) => {
        console.error("Error fetching suggestions:", error)
      })
  },

  // Show Search Suggestions
  showSearchSuggestions(products) {
    let suggestionsContainer = document.getElementById("search-suggestions")

    if (!suggestionsContainer) {
      suggestionsContainer = document.createElement("div")
      suggestionsContainer.id = "search-suggestions"
      suggestionsContainer.className = "search-suggestions position-absolute bg-white border rounded shadow-sm"

      const searchForm = document.querySelector('form[action*="search"]')
      searchForm.style.position = "relative"
      searchForm.appendChild(suggestionsContainer)
    }

    if (products.length === 0) {
      suggestionsContainer.innerHTML = '<div class="p-3 text-muted">No products found</div>'
    } else {
      const html = products
        .map(
          (product) => `
        <a href="/products/${product.slug}" class="d-flex align-items-center p-2 text-decoration-none text-dark border-bottom">
          <img src="${product.image || "/assets/images/placeholder-product.jpg"}" 
               alt="${product.name}" 
               class="me-2 rounded" 
               style="width: 40px; height: 40px; object-fit: cover;">
          <div>
            <div class="fw-medium">${product.name}</div>
            <small class="text-muted">$${product.price}</small>
          </div>
        </a>
      `,
        )
        .join("")

      suggestionsContainer.innerHTML = html
    }

    suggestionsContainer.style.display = "block"
  },

  // Hide Search Suggestions
  hideSearchSuggestions() {
    const suggestionsContainer = document.getElementById("search-suggestions")
    if (suggestionsContainer) {
      suggestionsContainer.style.display = "none"
    }
  },

  // Handle Search
  handleSearch(e) {
    const query = e.target.querySelector('input[name="q"]').value.trim()

    if (!query) {
      e.preventDefault()
      SadaCart.showNotification("warning", "Please enter a search term")
      return
    }

    // Track search event
    if (typeof gtag !== "undefined") {
      gtag("event", "search", {
        search_term: query,
      })
    }
  },

  // Mobile Menu Toggle
  toggleMobileMenu() {
    const navbar = document.querySelector(".navbar-collapse")
    navbar.classList.toggle("show")
  },

  // Show Notification
  showNotification(type, message, duration = 5000) {
    const alertClass = type === "error" ? "danger" : type
    const alertId = "alert-" + Date.now()

    const alertHtml = `
      <div id="${alertId}" class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
           style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 400px;">
        <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    `

    document.body.insertAdjacentHTML("beforeend", alertHtml)

    // Auto dismiss
    setTimeout(() => {
      const alert = document.getElementById(alertId)
      if (alert) {
        alert.remove()
      }
    }, duration)
  },

  // Get Alert Icon
  getAlertIcon(type) {
    const icons = {
      success: "check-circle",
      error: "exclamation-circle",
      warning: "exclamation-triangle",
      info: "info-circle",
    }
    return icons[type] || "info-circle"
  },

  // Utility Functions
  utils: {
    // Format price
    formatPrice(price) {
      return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
      }).format(price)
    },

    // Debounce function
    debounce(func, wait) {
      let timeout
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout)
          func(...args)
        }
        clearTimeout(timeout)
        timeout = setTimeout(later, wait)
      }
    },

    // Throttle function
    throttle(func, limit) {
      let inThrottle
      return function () {
        const args = arguments
        
        if (!inThrottle) {
          func.apply(this, args)
          inThrottle = true
          setTimeout(() => (inThrottle = false), limit)
        }
      }
    },

    // Get cookie value
    getCookie(name) {
      const value = `; ${document.cookie}`
      const parts = value.split(`; ${name}=`)
      if (parts.length === 2) return parts.pop().split(";").shift()
    },

    // Set cookie
    setCookie(name, value, days = 7) {
      const expires = new Date()
      expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000)
      document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`
    },
  },
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  SadaCart.init()
})

// Lazy Loading Images
document.addEventListener("DOMContentLoaded", () => {
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target
          img.src = img.dataset.src
          img.classList.remove("lazy")
          imageObserver.unobserve(img)
        }
      })
    })

    document.querySelectorAll("img[data-src]").forEach((img) => {
      imageObserver.observe(img)
    })
  }
})

// Service Worker Registration (for PWA features)
if ("serviceWorker" in navigator) {
  window.addEventListener("load", () => {
    navigator.serviceWorker
      .register("/sw.js")
      .then((registration) => {
        console.log("SW registered: ", registration)
      })
      .catch((registrationError) => {
        console.log("SW registration failed: ", registrationError)
      })
  })
}

// Performance Monitoring
window.addEventListener("load", () => {
  // Log performance metrics
  if (window.performance && window.performance.timing) {
    const timing = window.performance.timing
    const loadTime = timing.loadEventEnd - timing.navigationStart

    console.log(`Page load time: ${loadTime}ms`)

    // Send to analytics if available
    if (typeof gtag !== "undefined") {
      gtag("event", "timing_complete", {
        name: "load",
        value: loadTime,
      })
    }
  }
})

// Export for global access
window.SadaCart = SadaCart
