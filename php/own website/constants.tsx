
import { Product } from './types';

export const PRODUCTS: Product[] = [
  {
    id: 'p1',
    name: 'Nutra_leaf Ashwagandha Gold',
    price: 1299,
    description: 'Ultra-pure KSM-66 Ashwagandha for stress relief and cognitive performance. Harvested at peak potency for maximum bioavailability.',
    image: 'https://images.unsplash.com/photo-1611073100829-875883713028?auto=format&fit=crop&q=80&w=800',
    category: 'Stress Relief',
    benefits: ['Reduces Stress', 'Improves Focus', 'Boosts Immunity']
  },
  {
    id: 'p2',
    name: 'Nutra_leaf Moringa Power',
    price: 849,
    description: 'Cold-pressed organic Moringa powder rich in 92 nutrients. The ultimate superfood supplement for daily vitality.',
    image: 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&q=80&w=800',
    category: 'Superfoods',
    benefits: ['Full Spectrum Nutrition', 'Energy Boost', 'Detoxifying']
  }
];

export const MOCK_OTP = "123456";
