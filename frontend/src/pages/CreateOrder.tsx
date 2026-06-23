import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../api/axios';
import { ShoppingCart, User, Plus, Trash2 } from 'lucide-react';

const CreateOrder = () => {
  const navigate = useNavigate();
  const [customerName, setCustomerName] = useState('');
  const [customerEmail, setCustomerEmail] = useState('');
  const [items, setItems] = useState([{ product_id: 1, quantity: 1 }]);
  const [error, setError] = useState<string | null>(null);

  const handleAddItem = () => {
    setItems([...items, { product_id: items.length + 1, quantity: 1 }]);
  };

  const handleRemoveItem = (index: number) => {
    const newItems = [...items];
    newItems.splice(index, 1);
    setItems(newItems);
  };

  const handleItemChange = (index: number, field: string, value: string) => {
    const newItems = [...items];
    newItems[index] = { ...newItems[index], [field]: Number(value) };
    setItems(newItems);
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    try {
      const payload = {
        customer_name: customerName,
        customer_email: customerEmail,
        items: items
      };
      // Use Idempotency Key to prevent double submission
      const idempotencyKey = crypto.randomUUID();
      const res = await api.post('/orders', payload, {
        headers: {
          'Idempotency-Key': idempotencyKey
        }
      });
      navigate(`/orders/${res.data.data.id}`);
    } catch (err: any) {
      setError(err.response?.data?.message || 'An error occurred while creating the order.');
    }
  };

  return (
    <div style={{ maxWidth: '800px', margin: '0 auto' }}>
      <div className="mb-8">
        <h1 style={{ fontSize: '2rem', marginBottom: '0.25rem' }}>Create New Order</h1>
        <p style={{ color: 'var(--text-muted)' }}>Enter customer details and add products to the order.</p>
      </div>

      {error && (
        <div style={{ background: 'rgba(239, 68, 68, 0.15)', color: '#f87171', padding: '1rem', borderRadius: 'var(--radius-md)', marginBottom: '1.5rem', border: '1px solid rgba(239, 68, 68, 0.3)' }}>
          {error}
        </div>
      )}

      <form onSubmit={handleSubmit} className="glass-panel p-6 stagger-1">
        <div className="grid" style={{ gridTemplateColumns: '1fr 1fr', gap: '1.5rem' }}>
          <div className="form-group">
            <label className="form-label"><User size={14} style={{ display: 'inline', marginRight: '4px' }} /> Customer Name</label>
            <input 
              type="text" 
              className="form-control" 
              required
              placeholder="e.g. John Doe"
              value={customerName}
              onChange={e => setCustomerName(e.target.value)}
            />
          </div>
          <div className="form-group">
            <label className="form-label">Customer Email</label>
            <input 
              type="email" 
              className="form-control" 
              required
              placeholder="e.g. john@example.com"
              value={customerEmail}
              onChange={e => setCustomerEmail(e.target.value)}
            />
          </div>
        </div>

        <div className="mt-8 mb-4 flex items-center justify-between">
          <h2 style={{ fontSize: '1.25rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
            <ShoppingCart size={20} color="var(--primary-color)" /> Order Items
          </h2>
          <button type="button" onClick={handleAddItem} className="btn btn-outline" style={{ padding: '0.25rem 0.75rem', fontSize: '0.875rem' }}>
            <Plus size={14} /> Add Item
          </button>
        </div>

        <div className="grid gap-4 mb-8">
          {items.map((item, idx) => (
            <div key={idx} className="flex items-center gap-4" style={{ background: 'rgba(15, 23, 42, 0.4)', padding: '1rem', borderRadius: 'var(--radius-md)', border: '1px solid var(--surface-border)' }}>
              <div className="flex-col" style={{ flex: 1 }}>
                <label className="form-label">Product ID</label>
                <input 
                  type="number" 
                  min="1"
                  className="form-control" 
                  value={item.product_id}
                  onChange={e => handleItemChange(idx, 'product_id', e.target.value)}
                />
              </div>
              <div className="flex-col" style={{ flex: 1 }}>
                <label className="form-label">Quantity</label>
                <input 
                  type="number" 
                  min="1"
                  className="form-control" 
                  value={item.quantity}
                  onChange={e => handleItemChange(idx, 'quantity', e.target.value)}
                />
              </div>
              <div className="flex-col" style={{ marginTop: '1.5rem' }}>
                <button type="button" onClick={() => handleRemoveItem(idx)} className="btn" style={{ background: 'rgba(239, 68, 68, 0.1)', color: '#f87171', border: '1px solid rgba(239, 68, 68, 0.2)' }}>
                  <Trash2 size={18} />
                </button>
              </div>
            </div>
          ))}
        </div>

        <div className="flex items-center justify-between" style={{ borderTop: '1px solid var(--surface-border)', paddingTop: '1.5rem' }}>
          <div style={{ color: 'var(--text-muted)' }}>
            Total Items: <strong style={{ color: 'var(--text-main)' }}>{items.length}</strong>
          </div>
          <button type="submit" className="btn btn-primary" disabled={items.length === 0}>
            Submit Order
          </button>
        </div>
      </form>
    </div>
  );
};

export default CreateOrder;
