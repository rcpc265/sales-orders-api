import { useEffect, useState } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../api/axios';
import { ArrowLeft, Package, User, CreditCard, RefreshCw, CheckCircle, Truck, XCircle } from 'lucide-react';

const OrderDetails = () => {
  const { id } = useParams();
  const [order, setOrder] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [updating, setUpdating] = useState(false);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchOrder();
  }, [id]);

  const fetchOrder = async () => {
    try {
      const res = await api.get(`/orders/${id}`);
      setOrder(res.data.data);
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to fetch order details');
    } finally {
      setLoading(false);
    }
  };

  const handleStatusUpdate = async (newStatus: string) => {
    setUpdating(true);
    setError(null);
    try {
      await api.patch(`/orders/${id}/status`, { status: newStatus });
      await fetchOrder(); // Refresh data
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to update status');
    } finally {
      setUpdating(false);
    }
  };

  if (loading) {
    return <div style={{ textAlign: 'center', padding: '4rem', color: 'var(--text-muted)' }}>Loading...</div>;
  }

  if (error && !order) {
    return <div style={{ background: 'rgba(239, 68, 68, 0.15)', color: '#f87171', padding: '1.5rem', borderRadius: 'var(--radius-md)', textAlign: 'center' }}>{error}</div>;
  }

  const getStatusBadge = (status: string) => {
    return <span className={`badge badge-${status}`}>{status}</span>;
  };

  return (
    <div style={{ maxWidth: '900px', margin: '0 auto' }}>
      <div className="mb-6 flex items-center justify-between">
        <Link to="/" className="btn btn-outline" style={{ padding: '0.5rem 1rem' }}>
          <ArrowLeft size={16} /> Back to Dashboard
        </Link>
        <div className="flex items-center gap-4">
          <span style={{ color: 'var(--text-muted)' }}>Last updated: {new Date(order.updated_at).toLocaleString()}</span>
          {getStatusBadge(order.status)}
        </div>
      </div>

      <div className="flex items-center justify-between mb-8">
        <h1 style={{ fontSize: '2.5rem', display: 'flex', alignItems: 'center', gap: '0.75rem' }}>
          <Package color="var(--primary-color)" size={32} />
          Order #{order.id}
        </h1>
        
        <div className="glass-panel flex items-center p-2 gap-2" style={{ borderRadius: 'var(--radius-xl)' }}>
          <button 
            onClick={() => handleStatusUpdate('confirmed')} 
            disabled={updating || order.status !== 'pending'}
            className="btn" 
            style={{ background: order.status === 'confirmed' ? 'rgba(99,102,241,0.2)' : 'transparent', color: order.status === 'confirmed' ? '#818cf8' : 'var(--text-muted)' }}
          >
            <RefreshCw size={16} /> Confirm
          </button>
          <button 
            onClick={() => handleStatusUpdate('shipped')} 
            disabled={updating || order.status !== 'confirmed'}
            className="btn" 
            style={{ background: order.status === 'shipped' ? 'rgba(16,185,129,0.2)' : 'transparent', color: order.status === 'shipped' ? '#34d399' : 'var(--text-muted)' }}
          >
            <CheckCircle size={16} /> Ship
          </button>
          <button 
            onClick={() => handleStatusUpdate('delivered')} 
            disabled={updating || order.status !== 'shipped'}
            className="btn" 
            style={{ background: order.status === 'delivered' ? 'rgba(16,185,129,0.2)' : 'transparent', color: order.status === 'delivered' ? '#34d399' : 'var(--text-muted)' }}
          >
            <Truck size={16} /> Deliver
          </button>
          <div style={{ width: '1px', height: '24px', background: 'var(--surface-border)', margin: '0 4px' }}></div>
          <button 
            onClick={() => handleStatusUpdate('cancelled')} 
            disabled={updating || order.status === 'delivered'}
            className="btn" 
            style={{ background: order.status === 'cancelled' ? 'rgba(239,68,68,0.2)' : 'transparent', color: order.status === 'cancelled' ? '#f87171' : 'var(--text-muted)' }}
          >
            <XCircle size={16} /> Cancel
          </button>
        </div>
      </div>

      {error && (
        <div className="mb-6" style={{ background: 'rgba(239, 68, 68, 0.15)', color: '#f87171', padding: '1rem', borderRadius: 'var(--radius-md)', border: '1px solid rgba(239, 68, 68, 0.3)' }}>
          {error}
        </div>
      )}

      <div className="grid" style={{ gridTemplateColumns: '1fr 300px', gap: '1.5rem' }}>
        <div className="glass-panel stagger-1">
          <div style={{ padding: '1.5rem', borderBottom: '1px solid var(--surface-border)' }}>
            <h2 style={{ fontSize: '1.25rem' }}>Order Items</h2>
          </div>
          <div style={{ padding: '1.5rem' }}>
            <table style={{ width: '100%', textAlign: 'left', borderCollapse: 'collapse' }}>
              <thead>
                <tr style={{ color: 'var(--text-muted)', borderBottom: '1px solid var(--surface-border)' }}>
                  <th style={{ padding: '0.75rem', fontWeight: 500 }}>Product</th>
                  <th style={{ padding: '0.75rem', fontWeight: 500 }}>Price</th>
                  <th style={{ padding: '0.75rem', fontWeight: 500 }}>Qty</th>
                  <th style={{ padding: '0.75rem', fontWeight: 500, textAlign: 'right' }}>Subtotal</th>
                </tr>
              </thead>
              <tbody>
                {order.items.map((item: any) => (
                  <tr key={item.id} style={{ borderBottom: '1px solid rgba(255,255,255,0.04)' }}>
                    <td style={{ padding: '1rem 0.75rem' }}>
                      <div style={{ fontWeight: 500 }}>{item.product_name}</div>
                      <div style={{ fontSize: '0.875rem', color: 'var(--text-muted)' }}>ID: {item.product_id}</div>
                    </td>
                    <td style={{ padding: '1rem 0.75rem' }}>${item.product_price.toFixed(2)}</td>
                    <td style={{ padding: '1rem 0.75rem' }}>{item.quantity}</td>
                    <td style={{ padding: '1rem 0.75rem', textAlign: 'right', fontWeight: 600 }}>${item.subtotal.toFixed(2)}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        <div className="flex-col gap-6">
          <div className="glass-panel stagger-2" style={{ padding: '1.5rem' }}>
            <h2 style={{ fontSize: '1.125rem', marginBottom: '1rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
              <User size={18} color="var(--primary-color)" /> Customer Info
            </h2>
            <div style={{ marginBottom: '0.5rem' }}>
              <div style={{ color: 'var(--text-muted)', fontSize: '0.875rem' }}>Name</div>
              <div style={{ fontWeight: 500 }}>{order.customer_name}</div>
            </div>
            <div>
              <div style={{ color: 'var(--text-muted)', fontSize: '0.875rem' }}>Email</div>
              <div style={{ fontWeight: 500 }}>{order.customer_email}</div>
            </div>
          </div>

          <div className="glass-panel stagger-3" style={{ padding: '1.5rem', background: 'linear-gradient(135deg, rgba(15,23,42,0.8), rgba(99,102,241,0.1))' }}>
            <h2 style={{ fontSize: '1.125rem', marginBottom: '1rem', display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
              <CreditCard size={18} color="var(--primary-color)" /> Summary
            </h2>
            <div className="flex justify-between items-center mb-4" style={{ color: 'var(--text-muted)' }}>
              <span>Items Total</span>
              <span>${order.total_amount.toFixed(2)}</span>
            </div>
            <div className="flex justify-between items-center mb-4" style={{ color: 'var(--text-muted)' }}>
              <span>Tax (0%)</span>
              <span>$0.00</span>
            </div>
            <div style={{ height: '1px', background: 'var(--surface-border)', margin: '1rem 0' }}></div>
            <div className="flex justify-between items-center">
              <span style={{ fontSize: '1.125rem', fontWeight: 600 }}>Total</span>
              <span style={{ fontSize: '1.5rem', fontWeight: 700, color: 'var(--primary-color)', textShadow: 'var(--shadow-glow)' }}>
                ${order.total_amount.toFixed(2)}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default OrderDetails;
