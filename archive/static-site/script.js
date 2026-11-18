const navToggle = document.getElementById("navToggle");
const navMenu = document.getElementById("navMenu");
const body = document.body;
const year = document.getElementById("year");
const testimonials = Array.from(document.querySelectorAll(".testimonial"));
const prevButton = document.getElementById("prevTestimonial");
const nextButton = document.getElementById("nextTestimonial");
const revealItems = document.querySelectorAll(".reveal");
const page = document.body.dataset.page;
const filterButtons = document.getElementById("filterButtons");
const productGrid = document.getElementById("productGrid");
const homeProductPreview = document.getElementById("homeProductPreview");
const whatsappForm = document.getElementById("whatsappForm");
const whatsappNumber = "2348030613742";

const products = [
  {
    name: "Ankara Statement Gown",
    category: "Bespoke Fashion",
    type: "Price on Request",
    description: "A stunning bespoke gown featuring intricate Ankara patterns and elegant silhouette.",
    image: "Custom Statement Gown.png",
  },
  {
    name: "Ankara Luxury Bag",
    category: "Ankara Shoes & Bags",
    type: "Price on Request",
    description: "Handcrafted luxury bag with bold African print design and premium finish.",
    image: "Ankara bag.png",
  },
  {
    name: "Ankara Shoe Pair",
    category: "Ankara Shoes & Bags",
    type: "Price on Request",
    description: "Statement footwear featuring coordinated Ankara print for complete styling.",
    image: "Ankara shoe pair.png",
  },
  {
    name: "Modern Heritage Set",
    category: "Women's Ready-to-Wear",
    type: "Price on Request",
    description: "Contemporary ensemble blending modern design with African heritage aesthetics.",
    image: "wmremove-transformed (5).jpeg",
  },
  {
    name: "Custom Statement Gown",
    category: "Bespoke Fashion",
    type: "Price on Request",
    description: "Bespoke gown tailored to your specifications for special occasions.",
    image: "Custom Statement Gown.png",
  },
  {
    name: "Executive Ready Set",
    category: "Men's Ready-to-Wear",
    type: "Price on Request",
    description: "Sophisticated ready-to-wear ensemble for the modern gentleman.",
    image: "men ankara.png",
  },
];

if (year) {
  year.textContent = new Date().getFullYear();
}

if (navToggle && navMenu) {
  navToggle.addEventListener("click", () => {
    const isOpen = navMenu.classList.toggle("is-open");
    navToggle.setAttribute("aria-expanded", String(isOpen));
    body.classList.toggle("menu-open", isOpen);
  });

  navMenu.querySelectorAll("a").forEach((link) => {
    link.addEventListener("click", () => {
      navMenu.classList.remove("is-open");
      navToggle.setAttribute("aria-expanded", "false");
      body.classList.remove("menu-open");
    });
  });
}

document.querySelectorAll("[data-nav]").forEach((link) => {
  if (link.dataset.nav === page) {
    link.classList.add("is-active");
  }
});

let activeTestimonial = 0;

function showTestimonial(index) {
  if (!testimonials.length) return;

  activeTestimonial = (index + testimonials.length) % testimonials.length;
  testimonials.forEach((testimonial, itemIndex) => {
    testimonial.classList.toggle("is-active", itemIndex === activeTestimonial);
  });
}

if (prevButton && nextButton && testimonials.length) {
  prevButton.addEventListener("click", () => showTestimonial(activeTestimonial - 1));
  nextButton.addEventListener("click", () => showTestimonial(activeTestimonial + 1));

  setInterval(() => {
    showTestimonial(activeTestimonial + 1);
  }, 6000);
}

function buildWhatsAppUrl(message) {
  return `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(message)}`;
}

function createProductCard(product) {
  const article = document.createElement("article");
  article.className = "product-card reveal is-visible";
  
  const visualHtml = product.image 
    ? `<div class="product-visual product-visual--image"><img src="${product.image}" alt="${product.name}"></div>`
    : `<div class="product-visual" style="--visual-start: ${product.visualStart}; --visual-end: ${product.visualEnd};">
        <span class="product-visual__label">${product.name}</span>
       </div>`;
  
  const whatsappMessage = `Hello Delight Afriq, I am interested in the ${product.name}. Please send me the available sizes, price and ordering details.`;
  
  const sizes = product.sizes ? product.sizes.join(', ') : 'XS, S, M, L, XL, XXL';
  
  article.innerHTML = `
    ${visualHtml}
    <div class="product-card__body">
      <div class="product-badge">${product.category}</div>
      <h3>${product.name}</h3>
      <p>${product.description}</p>
      <div class="product-price">${product.type}</div>
      <div class="product-sizes">
        <strong>Available Sizes:</strong>
        <span>${sizes}</span>
      </div>
      <div class="product-card__links">
        <a class="product-link" href="${buildWhatsAppUrl(whatsappMessage)}" target="_blank" rel="noreferrer">Order on WhatsApp</a>
        <a class="product-link" href="contact.html">Request Custom Size</a>
      </div>
    </div>
  `;
  return article;
}

function renderHomePreview() {
  if (!homeProductPreview) return;
  homeProductPreview.innerHTML = "";

  products.slice(0, 3).forEach((product) => {
    const article = document.createElement("article");
    article.className = "product-card reveal is-visible";

    const visualHtml = product.image 
      ? `<div class="product-card__image product-card__image--custom"><img src="${product.image}" alt="${product.name}"></div>`
      : `<div class="product-card__image"></div>`;

    const whatsappMessage = `Hello Delight Afriq, I am interested in the ${product.name}. Please send me the available sizes, price and ordering details.`;

    article.innerHTML = `
      ${visualHtml}
      <div class="product-card__body">
        <div class="product-badge">${product.category}</div>
        <h3>${product.name}</h3>
        <p>${product.description}</p>
        <div class="product-price">${product.type}</div>
        <div class="product-card__links">
          <a class="product-link" href="${buildWhatsAppUrl(whatsappMessage)}" target="_blank" rel="noreferrer">Order on WhatsApp</a>
          <a class="product-link" href="collections.html">View Details</a>
        </div>
      </div>
    `;

    homeProductPreview.appendChild(article);
  });
}

function renderCollectionFilters() {
  if (!filterButtons) return;

  const categories = ["All", ...new Set(products.map((product) => product.category))];

  filterButtons.innerHTML = "";
  categories.forEach((category, index) => {
    const button = document.createElement("button");
    button.type = "button";
    button.className = `filter-chip${index === 0 ? " is-active" : ""}`;
    button.textContent = category;
    button.addEventListener("click", () => {
      document.querySelectorAll(".filter-chip").forEach((chip) => chip.classList.remove("is-active"));
      button.classList.add("is-active");
      renderCollectionProducts(category === "All" ? products : products.filter((product) => product.category === category));
    });
    filterButtons.appendChild(button);
  });
}

function renderCollectionProducts(items) {
  if (!productGrid) return;
  productGrid.innerHTML = "";
  items.forEach((product) => productGrid.appendChild(createProductCard(product)));
}

if (page === "collections") {
  renderCollectionFilters();
  renderCollectionProducts(products);
}

if (page === "home") {
  renderHomePreview();
}

if (whatsappForm) {
  whatsappForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const formData = new FormData(whatsappForm);
    const name = formData.get("name")?.toString().trim() || "";
    const phone = formData.get("phone")?.toString().trim() || "";
    const interest = formData.get("interest")?.toString().trim() || "";
    const message = formData.get("message")?.toString().trim() || "";

    const whatsappMessage = [
      "Hello Delight Afriq, I would like to make an inquiry.",
      `Name: ${name}`,
      phone ? `Phone: ${phone}` : null,
      `Interest: ${interest}`,
      `Message: ${message}`,
    ]
      .filter(Boolean)
      .join("\n");

    window.open(buildWhatsAppUrl(whatsappMessage), "_blank", "noopener,noreferrer");
  });
}

if ("IntersectionObserver" in window) {
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.14,
    }
  );

  revealItems.forEach((item) => observer.observe(item));
} else {
  revealItems.forEach((item) => item.classList.add("is-visible"));
}

showTestimonial(0);
