
export interface Product {
  id: string;
  name: string;
  price: number;
  description: string;
  image: string;
  category: string;
  benefits: string[];
}

export interface CartItem extends Product {
  quantity: number;
}

export interface UserDetails {
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  pincode: string;
}

export interface Order extends UserDetails {
  id: string;
  date: string;
  items: CartItem[];
  total: number;
  status: 'Pending' | 'Verified' | 'Shipped';
}

export enum CheckoutStep {
  CART = 'CART',
  DETAILS = 'DETAILS',
  OTP = 'OTP',
  SUCCESS = 'SUCCESS'
}
