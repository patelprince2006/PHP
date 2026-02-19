// --- script.js (Updated for PHP/MySQL Backend) ---

const defaultConfig = {
    store_name: "Nutra_leaf",
    product1_name: "Herbal Tea Blend",
    product2_name: "Organic Green Powder",
    admin_password: "admin123",
    background_color: "#f3f4f6",
    card_color: "#ffffff",
    text_color: "#1f2937",
    primary_button_color: "#16a34a",
    secondary_button_color: "#059669",
    font_family: "system-ui",
    font_size: 16
};

const products = [
    {
        id: 1,
        name: "Herbal Tea Blend",
        price: 499,
        image: "🍵",
        description: "Premium blend of natural herbs for wellness and relaxation",
        rating: 4.5
    },
    {
        id: 2,
        name: "Organic Green Powder",
        price: 799,
        image: "🌿",
        description: "100% organic green superfood powder packed with nutrients",
        rating: 4.8
    }
];

let cart = [];
let generatedOTP = '';
let currentOrderData = null;
let currentUser = null;
let isAdmin = false;
let allData = [];

// --- PHP Backend API URL (CRUCIAL: VERIFY THIS PATH) ---
const API_URL = 'http://localhost/PHP/ecommers/api.php'; 

// --- API Wrapper Functions to replace dataSdk ---

/**
 * Handles all communication with the PHP API endpoint.
 * @param {object} data - The payload containing action and record data.
 */
async function callApi(data) {
    try {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            // Throw an error if the HTTP status is not 2xx
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (!result.isOk) {
            // Throw an error if the PHP script returned {isOk: false}
            throw new Error(result.error || 'API operation failed.');
        }
        return result;
    } catch (error) {
        console.error('API Error:', error);
        throw error; // Re-throw for handling in the calling function
    }
}

/** Fetches all users and orders from the database. */
async function fetchAllData() {
    try {
        const result = await callApi({ action: 'read_all' });
        // Simulate dataHandler.onDataChanged by passing data to it
        dataHandler.onDataChanged(result.data || []); 
    } catch (error) {
        // Since this runs on startup, use a gentle error message
        showToast('Failed to load application data from the server. Check API_URL and server status.', 'error');
    }
}

/** Creates a new user or order record. */
async function saveRecord(recordData) {
    const data = {
        action: 'create',
        ...recordData
    };
    return await callApi(data);
}

/** Updates an existing record (e.g., changing order status). */
async function updateRecord(recordData) {
    const data = {
        action: 'update',
        record_type: 'order', // Only used for order status update in this app
        ...recordData
    };
    return await callApi(data);
}
// ------------------------------------------------

function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}

function updateUIForUser() {
    if (currentUser) {
        document.getElementById('user-menu').style.display = 'block';
        document.getElementById('guest-menu').style.display = 'none';
        document.getElementById('user-name-display').textContent = `Hello, ${currentUser.name}`;
    } else if (isAdmin) {
        document.getElementById('user-menu').style.display = 'none';
        document.getElementById('guest-menu').style.display = 'none';
    } else {
        document.getElementById('user-menu').style.display = 'none';
        document.getElementById('guest-menu').style.display = 'block';
    }
}

function renderProducts() {
    const grid = document.getElementById('products-grid');
    const config = window.elementSdk?.config || defaultConfig;
    
    products[0].name = config.product1_name || defaultConfig.product1_name;
    products[1].name = config.product2_name || defaultConfig.product2_name;
    
    grid.innerHTML = products.map(product => `
        <div class="product-card bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-8 text-center bg-gradient-to-br from-green-50 to-green-100">
                <div class="text-8xl mb-4">${product.image}</div>
                <div class="flex justify-center items-center space-x-1 mb-2">
                    ${Array(5).fill('⭐').map((star, i) => 
                        i < Math.floor(product.rating) ? star : '☆'
                    ).join('')}
                    <span class="text-sm text-gray-600 ml-2">(${product.rating})</span>
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-2">${product.name}</h3>
                <p class="text-gray-600 mb-4">${product.description}</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-green-600">₹${product.price}</span>
                    <button onclick="addToCart(${product.id})" class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 btn-primary">
                        Add to Cart
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function addToCart(productId) {
    if (!currentUser) {
        showToast('Please login to add items to cart', 'error');
        document.getElementById('login-modal').classList.add('active');
        return;
    }

    const product = products.find(p => p.id === productId);
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity++;
    } else {
        cart.push({ ...product, quantity: 1 });
    }
    
    updateCartUI();
    showToast('Product added to cart!');
}

function updateCartUI() {
    const cartCount = document.getElementById('cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;
    
    if (totalItems > 0) {
        cartCount.classList.add('cart-badge');
        setTimeout(() => cartCount.classList.remove('cart-badge'), 500);
    }
}

function renderCart() {
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    
    if (cart.length === 0) {
        cartItems.innerHTML = '<p class="text-center text-gray-500 py-8">Your cart is empty</p>';
        cartTotal.textContent = '₹0';
        return;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    
    cartItems.innerHTML = cart.map(item => `
        <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
            <div class="flex items-center space-x-4 flex-1">
                <div class="text-4xl">${item.image}</div>
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">${item.name}</h4>
                    <p class="text-green-600 font-bold">₹${item.price}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="updateQuantity(${item.id}, -1)" class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 font-bold">-</button>
                <span class="font-semibold w-8 text-center">${item.quantity}</span>
                <button onclick="updateQuantity(${item.id}, 1)" class="bg-gray-200 w-8 h-8 rounded-full hover:bg-gray-300 font-bold">+</button>
                <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 ml-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
    
    cartTotal.textContent = `₹${total}`;
}

function updateQuantity(productId, change) {
    const item = cart.find(i => i.id === productId);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(productId);
        } else {
            updateCartUI();
            renderCart();
        }
    }
}

function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    updateCartUI();
    renderCart();
}

function generateOTP() {
    return Math.floor(100000 + Math.random() * 900000).toString();
}

function downloadExcel() {
    const orders = allData.filter(d => d.record_type === 'order');
    
    if (orders.length === 0) {
        showToast('No orders to export', 'error');
        return;
    }

    let csv = 'Order ID,Customer Name,Email,Phone,Address,City,State,Pincode,Product,Quantity,Amount,Status,Date\n';
    
    orders.forEach(order => {
        const row = [
            order.order_id,
            order.customer_name,
            order.customer_email,
            order.customer_phone,
            `"${order.address}"`,
            order.city,
            order.state,
            order.pincode,
            order.product_name,
            order.quantity,
            order.total_amount,
            order.status,
            new Date(order.order_date).toLocaleString()
        ].join(',');
        csv += row + '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `orders_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('Excel file downloaded successfully!');
}

function renderAdminDashboard() {
    const orders = allData.filter(d => d.record_type === 'order');
    const pendingOrders = orders.filter(o => o.status === 'Confirmed' || o.status === 'Pending');
    const totalRevenue = orders.reduce((sum, o) => sum + (parseFloat(o.total_amount) || 0), 0);

    document.getElementById('total-orders').textContent = orders.length;
    document.getElementById('pending-orders').textContent = pendingOrders.length;
    document.getElementById('total-revenue').textContent = `₹${totalRevenue}`;

    const tbody = document.getElementById('orders-table-body');
    
    if (orders.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">No orders yet</td></tr>';
        return;
    }

    tbody.innerHTML = orders.map(order => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 border text-sm">${order.order_id}</td>
            <td class="px-4 py-3 border text-sm">
                <div class="font-semibold">${order.customer_name}</div>
                <div class="text-xs text-gray-500">${order.customer_email}</div>
                <div class="text-xs text-gray-500">${order.customer_phone}</div>
            </td>
            <td class="px-4 py-3 border text-sm">${order.product_name}</td>
            <td class="px-4 py-3 border text-sm">${order.quantity}</td>
            <td class="px-4 py-3 border text-sm font-semibold">₹${parseFloat(order.total_amount).toFixed(2)}</td>
            <td class="px-4 py-3 border text-sm">
                <span class="px-2 py-1 rounded-full text-xs font-semibold ${
                    order.status === 'Confirmed' ? 'bg-yellow-100 text-yellow-800' :
                    order.status === 'Delivered' ? 'bg-green-100 text-green-800' :
                    'bg-blue-100 text-blue-800'
                }">${order.status}</span>
            </td>
            <td class="px-4 py-3 border text-sm text-gray-600">
                ${new Date(order.order_date).toLocaleDateString()}
            </td>
            <td class="px-4 py-3 border text-sm">
                <button onclick="updateOrderStatus(${order.__backendId}, '${order.status}')" 
                    class="text-blue-600 hover:text-blue-800 font-semibold text-xs">
                    ${order.status === 'Confirmed' ? 'Mark Delivered' : 'Update'}
                </button>
            </td>
        </tr>
    `).join('');
}

async function updateOrderStatus(backendId, currentStatus) {
    const order = allData.find(d => d.__backendId === backendId);
    if (!order) return;

    // Toggle status between Confirmed and Delivered
    const newStatus = currentStatus === 'Confirmed' ? 'Delivered' : 'Confirmed';
    
    try {
        await updateRecord({
            __backendId: backendId,
            status: newStatus
        });
        showToast(`Order status updated to ${newStatus}`);
        // Re-fetch all data to refresh the dashboard
        await fetchAllData(); 
    } catch (error) {
        showToast('Failed to update order status', 'error');
    }
}

// Event Listeners
document.getElementById('register-btn').addEventListener('click', () => {
    document.getElementById('register-modal').classList.add('active');
});

document.getElementById('close-register').addEventListener('click', () => {
    document.getElementById('register-modal').classList.remove('active');
});

document.getElementById('register-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const btn = e.target.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<div class="flex items-center justify-center"><div class="loading-spinner"></div><span class="ml-2">Registering...</span></div>';

    const email = document.getElementById('reg-email').value;
    
    const userData = {
        record_type: 'user',
        user_id: `USER${Date.now()}`,
        name: document.getElementById('reg-name').value,
        email: email,
        phone: document.getElementById('reg-phone').value,
        password: document.getElementById('reg-password').value,
        created_at: new Date().toISOString()
    };

    try {
        // API call to create the user
        await saveRecord(userData); 
        showToast('Registration successful! Please login.');
        document.getElementById('register-modal').classList.remove('active');
        document.getElementById('register-form').reset();
        document.getElementById('login-modal').classList.add('active');
        await fetchAllData(); // Refresh data to include new user (important for client-side login fallback)
    } catch (error) {
        // Display specific error from PHP (e.g., 'Email already registered.')
        showToast(error.message || 'Registration failed. Please check API connection and try again.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = 'Register';
    }
});

document.getElementById('login-btn').addEventListener('click', () => {
    document.getElementById('login-modal').classList.add('active');
});

document.getElementById('close-login').addEventListener('click', () => {
    document.getElementById('login-modal').classList.remove('active');
});

document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const email = document.getElementById('login-email').value;
    const password = document.getElementById('login-password').value;

    try {
        // Attempt dedicated API Login
        const result = await callApi({ action: 'login', email: email, password: password });
        
        currentUser = {
            ...result.data,
            // Ensure record_type is set for compatibility with frontend structure
            record_type: 'user' 
        };
        updateUIForUser();
        document.getElementById('login-modal').classList.remove('active');
        document.getElementById('login-form').reset();
        showToast(`Welcome back, ${currentUser.name}!`);

    } catch (apiError) {
        // Fallback to original client-side filter if API login fails (e.g., if API is down, or for consistency)
        console.warn("API Login failed, falling back to client-side data check.", apiError);
        
        const user = allData.find(d => 
            d.record_type === 'user' && 
            d.email === email && 
            d.password === password
        );
    
        if (user) {
            currentUser = user;
            updateUIForUser();
            document.getElementById('login-modal').classList.remove('active');
            document.getElementById('login-form').reset();
            showToast(`Welcome back, ${user.name}!`);
        } else {
            showToast('Invalid email or password', 'error');
        }
    }
});

document.getElementById('logout-btn').addEventListener('click', () => {
    currentUser = null;
    cart = [];
    updateCartUI();
    updateUIForUser();
    showToast('Logged out successfully');
});

document.getElementById('admin-btn').addEventListener('click', () => {
    document.getElementById('admin-login-modal').classList.add('active');
});

document.getElementById('close-admin-login').addEventListener('click', () => {
    document.getElementById('admin-login-modal').classList.remove('active');
});

document.getElementById('admin-login-form').addEventListener('submit', (e) => {
    e.preventDefault();

    const password = document.getElementById('admin-password').value;
    const config = window.elementSdk?.config || defaultConfig;
    const correctPassword = config.admin_password || defaultConfig.admin_password;

    if (password === correctPassword) {
        isAdmin = true;
        currentUser = null;
        updateUIForUser();
        document.getElementById('admin-login-modal').classList.remove('active');
        document.getElementById('admin-dashboard').classList.add('active');
        document.getElementById('admin-login-form').reset();
        renderAdminDashboard();
        showToast('Admin login successful');
    } else {
        showToast('Invalid admin password', 'error');
    }
});

document.getElementById('close-admin-dashboard').addEventListener('click', () => {
    document.getElementById('admin-dashboard').classList.remove('active');
    isAdmin = false;
    updateUIForUser();
});

document.getElementById('export-excel-btn').addEventListener('click', downloadExcel);

document.getElementById('cart-btn').addEventListener('click', () => {
    document.getElementById('cart-modal').classList.add('active');
    renderCart();
});

document.getElementById('close-cart').addEventListener('click', () => {
    document.getElementById('cart-modal').classList.remove('active');
});

document.getElementById('checkout-btn').addEventListener('click', () => {
    if (cart.length === 0) {
        showToast('Your cart is empty!', 'error');
        return;
    }
    document.getElementById('cart-modal').classList.remove('active');
    document.getElementById('checkout-modal').classList.add('active');
});

document.getElementById('close-checkout').addEventListener('click', () => {
    document.getElementById('checkout-modal').classList.remove('active');
});

document.getElementById('checkout-form').addEventListener('submit', (e) => {
    e.preventDefault();

    currentOrderData = {
        address: document.getElementById('checkout-address').value,
        city: document.getElementById('checkout-city').value,
        state: document.getElementById('checkout-state').value,
        pincode: document.getElementById('checkout-pincode').value
    };

    generatedOTP = generateOTP();
    // In a real app, this should trigger an SMS/Email service via the backend
    console.log(`OTP for ${currentUser.phone || 'customer'}: ${generatedOTP}`);

    document.getElementById('otp-phone').textContent = currentUser.phone || 'your phone';
    document.getElementById('checkout-modal').classList.remove('active');
    document.getElementById('otp-modal').classList.add('active');

    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs[0].focus();

    showToast('OTP simulated and logged to console!');
});

document.querySelectorAll('.otp-input').forEach((input, index, inputs) => {
    input.addEventListener('input', (e) => {
        if (e.target.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
            inputs[index - 1].focus();
        }
    });
});

document.getElementById('close-otp').addEventListener('click', () => {
    document.getElementById('otp-modal').classList.remove('active');
});

document.getElementById('resend-otp-btn').addEventListener('click', () => {
    generatedOTP = generateOTP();
    console.log(`New OTP: ${generatedOTP}`);
    showToast('New OTP simulated and logged to console!');
});

document.getElementById('verify-otp-btn').addEventListener('click', async () => {
    const otpInputs = document.querySelectorAll('.otp-input');
    const enteredOTP = Array.from(otpInputs).map(input => input.value).join('');

    const btn = document.getElementById('verify-otp-btn');

    if (enteredOTP.length !== 6) {
        showToast('Please enter complete OTP', 'error');
        return;
    }

    if (enteredOTP === generatedOTP) {
        btn.disabled = true;
        btn.innerHTML = '<div class="flex items-center justify-center"><div class="loading-spinner"></div><span class="ml-2">Processing Order...</span></div>';

        try {
            // Process each item in the cart as a separate order record
            for (const item of cart) {
                const orderData = {
                    record_type: 'order',
                    order_id: `ORD${Date.now()}${Math.floor(Math.random() * 1000)}`,
                    customer_name: currentUser.name,
                    customer_email: currentUser.email,
                    customer_phone: currentUser.phone,
                    address: currentOrderData.address,
                    city: currentOrderData.city,
                    state: currentOrderData.state,
                    pincode: currentOrderData.pincode,
                    product_name: item.name,
                    quantity: item.quantity,
                    total_amount: item.price * item.quantity,
                    order_date: new Date().toISOString(),
                    status: "Confirmed"
                };

                // API call to save the order record
                await saveRecord(orderData); 
            }

            document.getElementById('otp-modal').classList.remove('active');
            showToast('Order placed successfully! 🎉');

            cart = [];
            updateCartUI();
            await fetchAllData(); // Refresh data for admin dashboard

            document.getElementById('checkout-form').reset();
            otpInputs.forEach(input => input.value = '');

        } catch (error) {
            showToast(error.message || 'Failed to place order. Please try again.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = 'Verify & Place Order';
        }
    } else {
        showToast('Invalid OTP. Please try again.', 'error');
        otpInputs.forEach(input => input.value = '');
        otpInputs[0].focus();
    }
});

// The dataHandler now receives data directly from fetchAllData()
const dataHandler = {
    onDataChanged(data) {
        // This function is triggered with all data fetched from the backend.
        allData = data;
        if (isAdmin) {
            renderAdminDashboard();
        }
    }
};

async function onConfigChange(config) {
    document.getElementById('store-name').textContent = config.store_name || defaultConfig.store_name;

    const customFont = config.font_family || defaultConfig.font_family;
    const baseFontStack = 'system-ui, -apple-system, sans-serif';
    const baseSize = config.font_size || defaultConfig.font_size;

    document.body.style.fontFamily = `${customFont}, ${baseFontStack}`;
    document.body.style.fontSize = `${baseSize}px`;

    const app = document.getElementById('app');
    app.style.backgroundColor = config.background_color || defaultConfig.background_color;

    const header = document.querySelector('header');
    header.style.background = `linear-gradient(to right, ${config.primary_button_color || defaultConfig.primary_button_color}, ${config.secondary_button_color || defaultConfig.secondary_button_color})`;

    document.querySelectorAll('.product-card').forEach(card => {
        card.style.backgroundColor = config.card_color || defaultConfig.card_color;
    });

    document.querySelectorAll('h1, h2, h3, h4').forEach(heading => {
        heading.style.color = config.text_color || defaultConfig.text_color;
        heading.style.fontFamily = `${customFont}, ${baseFontStack}`;
    });

    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.style.backgroundColor = config.primary_button_color || defaultConfig.primary_button_color;
    });

    renderProducts();
}

const element = {
    defaultConfig,
    onConfigChange,
    mapToCapabilities: (config) => ({
        recolorables: [
            {
                get: () => config.background_color || defaultConfig.background_color,
                set: (value) => {
                    config.background_color = value;
                    // window.elementSdk.setConfig({ background_color: value });
                }
            },
            {
                get: () => config.card_color || defaultConfig.card_color,
                set: (value) => {
                    config.card_color = value;
                    // window.elementSdk.setConfig({ card_color: value });
                }
            },
            {
                get: () => config.text_color || defaultConfig.text_color,
                set: (value) => {
                    config.text_color = value;
                    // window.elementSdk.setConfig({ text_color: value });
                }
            },
            {
                get: () => config.primary_button_color || defaultConfig.primary_button_color,
                set: (value) => {
                    config.primary_button_color = value;
                    // window.elementSdk.setConfig({ primary_button_color: value });
                }
            },
            {
                get: () => config.secondary_button_color || defaultConfig.secondary_button_color,
                set: (value) => {
                    config.secondary_button_color = value;
                    // window.elementSdk.setConfig({ secondary_button_color: value });
                }
            }
        ],
        borderables: [],
        fontEditable: {
            get: () => config.font_family || defaultConfig.font_family,
            set: (value) => {
                config.font_family = value;
                // window.elementSdk.setConfig({ font_family: value });
            }
        },
        fontSizeable: {
            get: () => config.font_size || defaultConfig.font_size,
            set: (value) => {
                config.font_size = value;
                // window.elementSdk.setConfig({ font_size: value });
            }
        }
    }),
    mapToEditPanelValues: (config) => new Map([
        ["store_name", config.store_name || defaultConfig.store_name],
        ["product1_name", config.product1_name || defaultConfig.product1_name],
        ["product2_name", config.product2_name || defaultConfig.product2_name],
        ["admin_password", config.admin_password || defaultConfig.admin_password]
    ])
};

(async () => {
    // 1. Fetch data on startup to populate allData array
    await fetchAllData(); 
    
    // 2. Initialize Element SDK if available
    if (window.elementSdk) {
        // window.elementSdk.init(element); // Original line in snippet, commented out as per modern practice
        await onConfigChange(window.elementSdk.config);
    } else {
        onConfigChange(defaultConfig);
    }

    updateUIForUser();
})();