// checkout.js
function calculateTotal() {
  return state.cart.reduce((sum, it) => sum + it.qty * it.price, 0);
}
function placeOrder() {
  const address = document.getElementById('address').value;
  const payment = document.querySelector('input[name="payment"]:checked')?.value;
  const total = calculateTotal();
  // Simple mock; in real app you'd call a backend
  alert(`Order placed!\nAddress: ${address}\nPayment: ${payment}\nTotal: ₹${total.toFixed(2)}`);
  state.cart = [];
  saveState();
  window.location.href = 'index.html';
}
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('place-order');
  if (btn) btn.addEventListener('click', placeOrder);
});