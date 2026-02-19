
import React, { useState, useEffect, useCallback } from 'react';
import { ShoppingCart, Package, Info, CheckCircle2, Phone, User, MapPin, Download, MessageSquare, ChevronRight, X } from 'lucide-react';
import { Product, CartItem, UserDetails, Order, CheckoutStep } from './types';
import { PRODUCTS, MOCK_OTP } from './constants';
import { getProductAdvice } from './services/geminiService';

// Utility to handle Excel export (using script loaded in index.html)
declare const XLSX: any;

const App: React.FC = () => {
  const [cart, setCart] = useState<CartItem[]>([]);
  const [view, setView] = useState<'home' | 'checkout' | 'admin'>('home');
  const [checkoutStep, setCheckoutStep] = useState<CheckoutStep>(CheckoutStep.CART);
  const [userDetails, setUserDetails] = useState<UserDetails>({
    name: '', email: '', phone: '', address: '', city: '', pincode: ''
  });
  const [otp, setOtp] = useState('');
  const [otpSent, setOtpSent] = useState(false);
  const [orders, setOrders] = useState<Order[]>([]);
  const [isAiOpen, setIsAiOpen] = useState(false);
  const [aiQuery, setAiQuery] = useState('');
  const [aiResponse, setAiResponse] = useState('');
  const [isLoadingAi, setIsLoadingAi] = useState(false);

  // Load orders from "database" (localStorage)
  useEffect(() => {
    const savedOrders = localStorage.getItem('nutra_leaf_orders');
    if (savedOrders) {
      setOrders(JSON.parse(savedOrders));
    }
  }, []);

  const addToCart = (product: Product) => {
    setCart(prev => {
      const existing = prev.find(item => item.id === product.id);
      if (existing) {
        return prev.map(item => item.id === product.id ? { ...item, quantity: item.quantity + 1 } : item);
      }
      return [...prev, { ...product, quantity: 1 }];
    });
  };

  const removeFromCart = (id: string) => {
    setCart(prev => prev.filter(item => item.id !== id));
  };

  const totalAmount = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  const handleAiConsult = async () => {
    if (!aiQuery.trim()) return;
    setIsLoadingAi(true);
    const advice = await getProductAdvice(aiQuery);
    setAiResponse(advice || "");
    setIsLoadingAi(false);
  };

  const handleSendOtp = () => {
    if (userDetails.phone.length !== 10) {
      alert("Please enter a valid 10-digit mobile number");
      return;
    }
    setOtpSent(true);
    setCheckoutStep(CheckoutStep.OTP);
    console.log(`SIMULATED OTP SENT TO ${userDetails.phone}: ${MOCK_OTP}`);
    alert(`[SIMULATION] OTP sent to ${userDetails.phone}. Check console or use: ${MOCK_OTP}`);
  };

  const handleVerifyOtp = () => {
    if (otp === MOCK_OTP) {
      const newOrder: Order = {
        ...userDetails,
        id: 'ORD-' + Math.random().toString(36).substr(2, 9).toUpperCase(),
        date: new Date().toLocaleString(),
        items: [...cart],
        total: totalAmount,
        status: 'Verified'
      };
      
      const updatedOrders = [newOrder, ...orders];
      setOrders(updatedOrders);
      localStorage.setItem('nutra_leaf_orders', JSON.stringify(updatedOrders));
      
      setCart([]);
      setCheckoutStep(CheckoutStep.SUCCESS);
    } else {
      alert("Invalid OTP. Try again.");
    }
  };

  const exportToExcel = () => {
    if (orders.length === 0) {
      alert("No orders to export.");
      return;
    }
    const data = orders.map(o => ({
      ID: o.id,
      Date: o.date,
      Customer: o.name,
      Phone: o.phone,
      Address: `${o.address}, ${o.city} - ${o.pincode}`,
      Total: `₹${o.total}`,
      Items: o.items.map(i => `${i.name} (x${i.quantity})`).join(', '),
      Status: o.status
    }));

    const ws = XLSX.utils.json_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Orders");
    XLSX.writeFile(wb, "NutraLeaf_Orders.xlsx");
  };

  return (
    <div className="min-h-screen flex flex-col font-sans">
      {/* Navigation Header */}
      <header className="sticky top-0 z-50 bg-white shadow-sm border-b border-green-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
          <div className="flex items-center space-x-2 cursor-pointer" onClick={() => setView('home')}>
            <div className="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
              <Package className="text-white" size={24} />
            </div>
            <h1 className="text-2xl font-bold text-green-800 tracking-tight">Nutra_leaf</h1>
          </div>
          
          <div className="hidden md:flex space-x-8">
            <button onClick={() => setView('home')} className="text-gray-600 hover:text-green-600 font-medium">Home</button>
            <button className="text-gray-600 hover:text-green-600 font-medium">Best Sellers</button>
            <button onClick={() => setView('admin')} className="text-gray-600 hover:text-green-600 font-medium flex items-center gap-1">
               Admin <ChevronRight size={14} />
            </button>
          </div>

          <div className="flex items-center space-x-4">
            <button 
              onClick={() => { setView('checkout'); setCheckoutStep(CheckoutStep.CART); }}
              className="relative p-2 text-gray-600 hover:bg-green-50 rounded-full transition-colors"
            >
              <ShoppingCart size={24} />
              {cart.length > 0 && (
                <span className="absolute -top-1 -right-1 bg-green-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full border-2 border-white">
                  {cart.length}
                </span>
              )}
            </button>
          </div>
        </div>
      </header>

      {/* Main Content Area */}
      <main className="flex-grow">
        {view === 'home' && (
          <div className="pb-16">
            {/* Hero Section */}
            <section className="relative h-[400px] flex items-center bg-gradient-to-br from-green-800 to-green-900 text-white overflow-hidden">
              <div className="absolute inset-0 opacity-20">
                <img src="https://images.unsplash.com/photo-1512036630240-199126773e73?auto=format&fit=crop&q=80&w=2000" alt="background" className="w-full h-full object-cover" />
              </div>
              <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="max-w-2xl">
                  <span className="inline-block px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full mb-4">PREMIUM WELLNESS</span>
                  <h2 className="text-5xl font-extrabold leading-tight mb-6">Elevate Your Daily Health with Nature's Wisdom.</h2>
                  <p className="text-lg text-green-100 mb-8">Pure, potent, and ethically sourced. We bring the best of nature to your doorstep.</p>
                  <button onClick={() => window.scrollTo({ top: 400, behavior: 'smooth' })} className="bg-white text-green-800 px-8 py-3 rounded-full font-bold shadow-lg hover:bg-green-50 transition-colors">
                    Shop Collection
                  </button>
                </div>
              </div>
            </section>

            {/* Products Grid */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
              <h3 className="text-3xl font-bold text-gray-900 mb-10 text-center">Our Signature Formulas</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                {PRODUCTS.map(product => (
                  <div key={product.id} className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl transition-all group">
                    <div className="relative h-72 overflow-hidden">
                      <img src={product.image} alt={product.name} className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" />
                      <div className="absolute top-4 left-4">
                        <span className="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-green-800 shadow-sm">
                          {product.category}
                        </span>
                      </div>
                    </div>
                    <div className="p-8">
                      <div className="flex justify-between items-start mb-4">
                        <div>
                          <h4 className="text-2xl font-bold text-gray-900">{product.name}</h4>
                          <div className="flex items-center gap-1 mt-1">
                            {[1, 2, 3, 4, 5].map(i => (
                              <span key={i} className="text-yellow-400 text-sm">★</span>
                            ))}
                            <span className="text-gray-400 text-xs ml-2">(120+ Reviews)</span>
                          </div>
                        </div>
                        <span className="text-2xl font-bold text-green-700">₹{product.price}</span>
                      </div>
                      <p className="text-gray-600 mb-6 line-clamp-2">{product.description}</p>
                      <div className="flex flex-wrap gap-2 mb-8">
                        {product.benefits.map(benefit => (
                          <span key={benefit} className="flex items-center gap-1 bg-green-50 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-md">
                            <CheckCircle2 size={12} /> {benefit}
                          </span>
                        ))}
                      </div>
                      <button 
                        onClick={() => addToCart(product)}
                        className="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition-all active:scale-95 flex items-center justify-center gap-2"
                      >
                        <ShoppingCart size={20} /> Add to Cart
                      </button>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {view === 'checkout' && (
          <div className="max-w-4xl mx-auto px-4 py-12">
            {/* Stepper */}
            <div className="flex items-center justify-between mb-12">
              {[
                { step: CheckoutStep.CART, label: 'Cart' },
                { step: CheckoutStep.DETAILS, label: 'Shipping' },
                { step: CheckoutStep.OTP, label: 'Verification' },
                { step: CheckoutStep.SUCCESS, label: 'Success' }
              ].map((s, idx) => (
                <React.Fragment key={s.step}>
                  <div className={`flex flex-col items-center ${checkoutStep === s.step ? 'text-green-600' : 'text-gray-400'}`}>
                    <div className={`w-10 h-10 rounded-full flex items-center justify-center font-bold mb-2 border-2 ${
                      checkoutStep === s.step ? 'border-green-600 bg-green-50' : 'border-gray-200'
                    }`}>
                      {idx + 1}
                    </div>
                    <span className="text-xs font-bold uppercase tracking-widest">{s.label}</span>
                  </div>
                  {idx < 3 && <div className="flex-1 h-0.5 bg-gray-200 mx-4 mt-5" />}
                </React.Fragment>
              ))}
            </div>

            {/* Step Content */}
            {checkoutStep === CheckoutStep.CART && (
              <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div className="p-8">
                  <h3 className="text-2xl font-bold mb-8">Your Shopping Bag</h3>
                  {cart.length === 0 ? (
                    <div className="text-center py-12">
                      <div className="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <ShoppingCart className="text-gray-300" size={32} />
                      </div>
                      <p className="text-gray-500 mb-6">Your cart is empty.</p>
                      <button onClick={() => setView('home')} className="text-green-600 font-bold hover:underline">Start Shopping</button>
                    </div>
                  ) : (
                    <div className="space-y-6">
                      {cart.map(item => (
                        <div key={item.id} className="flex items-center justify-between py-4 border-b border-gray-100 last:border-0">
                          <div className="flex items-center gap-4">
                            <img src={item.image} alt={item.name} className="w-20 h-20 object-cover rounded-lg" />
                            <div>
                              <h4 className="font-bold text-gray-900">{item.name}</h4>
                              <p className="text-green-600 font-bold">₹{item.price}</p>
                            </div>
                          </div>
                          <div className="flex items-center gap-6">
                            <span className="text-gray-500">Qty: {item.quantity}</span>
                            <button onClick={() => removeFromCart(item.id)} className="text-red-500 hover:bg-red-50 p-2 rounded-lg">
                              <X size={20} />
                            </button>
                          </div>
                        </div>
                      ))}
                      <div className="pt-6 border-t border-gray-200">
                        <div className="flex justify-between items-center text-2xl font-bold">
                          <span>Grand Total</span>
                          <span className="text-green-700">₹{totalAmount}</span>
                        </div>
                        <button 
                          onClick={() => setCheckoutStep(CheckoutStep.DETAILS)}
                          className="w-full mt-8 bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition-all"
                        >
                          Checkout Now
                        </button>
                      </div>
                    </div>
                  )}
                </div>
              </div>
            )}

            {checkoutStep === CheckoutStep.DETAILS && (
              <div className="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 className="text-2xl font-bold mb-8">Shipping Information</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <div className="relative">
                      <User className="absolute left-3 top-3.5 text-gray-400" size={18} />
                      <input 
                        type="text" 
                        value={userDetails.name} 
                        onChange={e => setUserDetails({...userDetails, name: e.target.value})}
                        className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none"
                        placeholder="John Doe"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input 
                      type="email" 
                      value={userDetails.email} 
                      onChange={e => setUserDetails({...userDetails, email: e.target.value})}
                      className="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none"
                      placeholder="john@example.com"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Mobile Number (Verification Required)</label>
                    <div className="relative">
                      <Phone className="absolute left-3 top-3.5 text-gray-400" size={18} />
                      <input 
                        type="tel" 
                        maxLength={10}
                        value={userDetails.phone} 
                        onChange={e => setUserDetails({...userDetails, phone: e.target.value.replace(/\D/g, '')})}
                        className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none font-mono"
                        placeholder="9876543210"
                      />
                    </div>
                  </div>
                  <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Pincode</label>
                    <div className="relative">
                      <MapPin className="absolute left-3 top-3.5 text-gray-400" size={18} />
                      <input 
                        type="text" 
                        value={userDetails.pincode} 
                        onChange={e => setUserDetails({...userDetails, pincode: e.target.value})}
                        className="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none"
                        placeholder="400001"
                      />
                    </div>
                  </div>
                  <div className="md:col-span-2">
                    <label className="block text-sm font-bold text-gray-700 mb-2">Detailed Address</label>
                    <textarea 
                      value={userDetails.address} 
                      onChange={e => setUserDetails({...userDetails, address: e.target.value})}
                      rows={3}
                      className="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none resize-none"
                      placeholder="Flat/House No, Building Name, Street"
                    />
                  </div>
                </div>
                <div className="mt-10 flex gap-4">
                  <button onClick={() => setCheckoutStep(CheckoutStep.CART)} className="flex-1 px-8 py-4 border border-gray-200 rounded-xl font-bold hover:bg-gray-50">Back</button>
                  <button 
                    onClick={handleSendOtp}
                    disabled={!userDetails.name || !userDetails.phone || !userDetails.address}
                    className="flex-2 bg-green-600 text-white px-12 py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Verify & Continue
                  </button>
                </div>
              </div>
            )}

            {checkoutStep === CheckoutStep.OTP && (
              <div className="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                <div className="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
                  <Phone className="text-green-600" size={32} />
                </div>
                <h3 className="text-2xl font-bold mb-2">Verify Phone Number</h3>
                <p className="text-gray-500 mb-8">We've sent a 6-digit code to <span className="text-gray-900 font-semibold">+91 {userDetails.phone}</span></p>
                <div className="space-y-6">
                  <input 
                    type="text" 
                    maxLength={6}
                    value={otp}
                    onChange={e => setOtp(e.target.value)}
                    className="w-full text-center text-3xl font-bold tracking-[1em] py-4 bg-gray-50 border-2 border-gray-200 rounded-xl focus:border-green-500 focus:ring-0 outline-none"
                    placeholder="000000"
                  />
                  <button 
                    onClick={handleVerifyOtp}
                    className="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700 transition-all"
                  >
                    Confirm Order
                  </button>
                  <button onClick={() => setCheckoutStep(CheckoutStep.DETAILS)} className="text-sm text-gray-500 hover:text-green-600">
                    Resend Code or Change Number
                  </button>
                </div>
              </div>
            )}

            {checkoutStep === CheckoutStep.SUCCESS && (
              <div className="max-w-lg mx-auto bg-white rounded-2xl shadow-xl p-12 text-center">
                <div className="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-8 animate-bounce">
                  <CheckCircle2 className="text-green-600" size={48} />
                </div>
                <h3 className="text-3xl font-bold text-gray-900 mb-4">Order Placed!</h3>
                <p className="text-gray-600 mb-8">Thank you for choosing Nutra_leaf. Your wellness journey starts today. You'll receive a confirmation SMS shortly.</p>
                <div className="bg-gray-50 rounded-xl p-6 mb-8 text-left border border-dashed border-gray-300">
                  <h4 className="font-bold text-sm uppercase text-gray-400 mb-4">Order Summary</h4>
                  <div className="space-y-2">
                    <p className="flex justify-between"><span>Status:</span> <span className="font-bold text-green-600">Verified</span></p>
                    <p className="flex justify-between"><span>Delivery To:</span> <span className="font-bold">{userDetails.name}</span></p>
                    <p className="flex justify-between"><span>City:</span> <span className="font-bold">{userDetails.city}</span></p>
                  </div>
                </div>
                <button 
                  onClick={() => setView('home')}
                  className="w-full bg-green-600 text-white py-4 rounded-xl font-bold shadow-lg hover:bg-green-700"
                >
                  Return to Store
                </button>
              </div>
            )}
          </div>
        )}

        {view === 'admin' && (
          <div className="max-w-7xl mx-auto px-4 py-12">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
              <div>
                <h3 className="text-3xl font-bold text-gray-900">Order Management</h3>
                <p className="text-gray-500">Track and export all customer purchase history.</p>
              </div>
              <button 
                onClick={exportToExcel}
                className="flex items-center justify-center gap-2 bg-green-800 text-white px-8 py-3 rounded-xl font-bold hover:bg-green-900 transition-all shadow-md active:scale-95"
              >
                <Download size={20} /> Export to Excel
              </button>
            </div>

            <div className="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead>
                    <tr className="bg-gray-50 border-b border-gray-100">
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Order ID</th>
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Customer</th>
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Phone</th>
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Amount</th>
                      <th className="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                    </tr>
                  </thead>
                  <tbody className="divide-y divide-gray-50">
                    {orders.length === 0 ? (
                      <tr>
                        <td colSpan={6} className="px-6 py-12 text-center text-gray-400 italic">No orders found in the database.</td>
                      </tr>
                    ) : (
                      orders.map(order => (
                        <tr key={order.id} className="hover:bg-green-50/30 transition-colors">
                          <td className="px-6 py-4 font-mono text-sm font-bold text-green-700">{order.id}</td>
                          <td className="px-6 py-4 text-sm text-gray-600">{order.date}</td>
                          <td className="px-6 py-4">
                            <div className="font-bold text-gray-900">{order.name}</div>
                            <div className="text-xs text-gray-400">{order.city}, {order.pincode}</div>
                          </td>
                          <td className="px-6 py-4 text-sm font-semibold">{order.phone}</td>
                          <td className="px-6 py-4 font-bold">₹{order.total}</td>
                          <td className="px-6 py-4">
                            <span className="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">
                              {order.status}
                            </span>
                          </td>
                        </tr>
                      ))
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        )}
      </main>

      {/* Footer */}
      <footer className="bg-gray-900 text-gray-400 py-12">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12 border-b border-gray-800 pb-12">
            <div className="col-span-1 md:col-span-2">
              <div className="flex items-center space-x-2 mb-6">
                <div className="w-8 h-8 bg-green-600 rounded flex items-center justify-center">
                  <Package className="text-white" size={16} />
                </div>
                <h1 className="text-xl font-bold text-white tracking-tight">Nutra_leaf</h1>
              </div>
              <p className="max-w-sm">Premium plant-based supplements engineered for modern health challenges. Science meets nature for your well-being.</p>
            </div>
            <div>
              <h5 className="text-white font-bold mb-6">Quick Links</h5>
              <ul className="space-y-4 text-sm">
                <li><button onClick={() => setView('home')} className="hover:text-green-500">Home</button></li>
                <li><button className="hover:text-green-500">Privacy Policy</button></li>
                <li><button className="hover:text-green-500">Terms of Service</button></li>
                <li><button onClick={() => setView('admin')} className="hover:text-green-500">Admin Panel</button></li>
              </ul>
            </div>
            <div>
              <h5 className="text-white font-bold mb-6">Connect</h5>
              <p className="text-sm mb-4">support@nutraleaf.com</p>
              <div className="flex space-x-4">
                <div className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-green-600 transition-colors cursor-pointer">
                  <Info size={18} />
                </div>
              </div>
            </div>
          </div>
          <p className="text-center text-xs">© {new Date().getFullYear()} Nutra_leaf Wellness Pvt Ltd. All rights reserved.</p>
        </div>
      </footer>

      {/* AI Assistant Floating Button & Modal */}
      <div className="fixed bottom-8 right-8 z-50">
        {!isAiOpen ? (
          <button 
            onClick={() => setIsAiOpen(true)}
            className="w-16 h-16 bg-green-600 text-white rounded-full shadow-2xl flex items-center justify-center hover:bg-green-700 hover:scale-110 transition-all group"
          >
            <MessageSquare size={28} />
            <span className="absolute right-full mr-4 bg-white text-green-800 px-3 py-1 rounded-lg shadow-lg text-sm font-bold whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity">
              Ask Health Assistant
            </span>
          </button>
        ) : (
          <div className="bg-white w-80 md:w-96 rounded-2xl shadow-2xl border border-gray-100 flex flex-col overflow-hidden animate-in slide-in-from-bottom-4 duration-300">
            <div className="bg-green-600 p-4 text-white flex justify-between items-center">
              <div className="flex items-center gap-2">
                <div className="w-2 h-2 bg-green-300 rounded-full animate-pulse" />
                <span className="font-bold">Nutra_leaf AI Guide</span>
              </div>
              <button onClick={() => setIsAiOpen(false)} className="hover:bg-green-500 p-1 rounded-full">
                <X size={20} />
              </button>
            </div>
            <div className="h-64 overflow-y-auto p-4 bg-gray-50 flex flex-col gap-4">
              <div className="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm text-sm text-gray-700 self-start border border-gray-100">
                Hi! I'm your Nutra_leaf assistant. Need help choosing between Ashwagandha or Moringa? Ask me anything!
              </div>
              {aiResponse && (
                <div className="bg-green-50 p-3 rounded-2xl rounded-tr-none shadow-sm text-sm text-green-900 self-end border border-green-100">
                  {aiResponse}
                </div>
              )}
              {isLoadingAi && (
                <div className="self-end text-xs text-gray-400 italic">Nutra_leaf is thinking...</div>
              )}
            </div>
            <div className="p-4 border-t border-gray-100 flex gap-2 bg-white">
              <input 
                type="text" 
                value={aiQuery}
                onChange={e => setAiQuery(e.target.value)}
                onKeyDown={e => e.key === 'Enter' && handleAiConsult()}
                placeholder="Ex: Which one helps with sleep?"
                className="flex-1 text-sm bg-gray-100 border-none rounded-xl px-4 py-2 focus:ring-2 focus:ring-green-500 outline-none"
              />
              <button 
                onClick={handleAiConsult}
                disabled={isLoadingAi}
                className="bg-green-600 text-white p-2 rounded-xl hover:bg-green-700 disabled:opacity-50"
              >
                <ChevronRight size={20} />
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

export default App;
