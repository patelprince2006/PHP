// products.js
let products = [];
async function loadProducts() {
  const res = await fetch('data/products.json');
  const data = await res.json();
  products = data.products;
  renderProducts(products);
}
function renderProducts(list) {
  const container = document.getElementById('product-grid');
  if (!container) return;
  container.innerHTML = '';
  list.forEach(p => {
    const card = document.createElement('div');
    card.className = 'product-card';
    card.innerHTML = `
      <img src="${p.image}" alt="${p.name}">
      <h3>${p.name}</h3>
      <p class="price">₹${p.price}</p>
      <p class="rating">★ ${p.rating}</p>
      <button class="btn" data-id="${p.id}">Add to Cart</button>
    `;
    container.appendChild(card);
  });
}
function filterAndSort() {
  const cat = document.getElementById('categoryFilter')?.value;
  const sort = document.getElementById('sort')?.value;
  let list = [...products];
  if (cat && cat !== 'All') list = list.filter(p => p.category === cat);
  if (sort === 'low') list.sort((a,b) => a.price - b.price);
  if (sort === 'high') list.sort((a,b) => b.price - a.price);
  renderProducts(list);
}
document.addEventListener('click', (e) => {
  if (e.target.matches('.product-card .btn') || e.target.closest('.product-card .btn')) {
    const id = e.target.closest('.btn').dataset.id;
    const product = products.find(p => p.id === id);
    if (product) {
      addToCart(product, 1);
    }
  }
});
document.addEventListener('search', (e) => {
  const query = e.detail;
  const filtered = products.filter(p => p.name.toLowerCase().includes(query) || p.description.toLowerCase().includes(query));
  renderProducts(filtered);
});
document.addEventListener('DOMContentLoaded', loadProducts);