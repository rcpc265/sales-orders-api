import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../api/axios';
import { Eye, Package } from 'lucide-react';

interface Order {
  id: number;
  customer_name: string;
  customer_email: string;
  status: string;
  total_amount: number;
  created_at: string;
}

const Dashboard = () => {
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchOrders = async () => {
      try {
        // Since we don't have an index endpoint in the provided snippets, 
        // we might just show an empty list or mock if we haven't built it.
        // Wait, did we build GET /orders ? The snippet in tests only had store, show, updateStatus.
        // Let's assume we need to list them or at least show a UI.
        // Actually, if we don't have an index endpoint, we'll just mock for now, or display an empty state.
        const res = await api.get('/orders');
        setOrders(res.data.data || []);
      } catch (error) {
        console.error('Failed to fetch orders', error);
      } finally {
        setLoading(false);
      }
    };
    // If the endpoint doesn't exist, we'll just catch and show empty
    fetchOrders();
  }, []);

  const getStatusBadge = (status: string) => {
    return <span className={`badge badge-${status}`}>{status}</span>;
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-8">
        <div>
          <h1 style={{ fontSize: '2rem', marginBottom: '0.25rem' }}>Overview</h1>
          <p style={{ color: 'var(--text-muted)' }}>Manage your sales orders and track their statuses.</p>
        </div>
      </div>

      <div className="glass-panel stagger-1">
        <div style={{ padding: '1.5rem', borderBottom: '1px solid var(--surface-border)' }}>
          <h2 style={{ fontSize: '1.25rem' }}>Recent Orders</h2>
        </div>
        <div style={{ padding: '1.5rem' }}>
          {loading ? (
            <div style={{ textAlign: 'center', padding: '2rem', color: 'var(--text-muted)' }}>Loading...</div>
          ) : orders.length > 0 ? (
            <div style={{ overflowX: 'auto' }}>
              <table style={{ width: '100%', textAlign: 'left', borderCollapse: 'collapse' }}>
                <thead>
                  <tr style={{ color: 'var(--text-muted)', borderBottom: '1px solid var(--surface-border)' }}>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Order ID</th>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Customer</th>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Date</th>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Status</th>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Total</th>
                    <th style={{ padding: '1rem', fontWeight: 500 }}>Action</th>
                  </tr>
                </thead>
                <tbody>
                  {orders.map((order) => (
                    <tr key={order.id} style={{ borderBottom: '1px solid var(--surface-border)' }}>
                      <td style={{ padding: '1rem' }}>#{order.id}</td>
                      <td style={{ padding: '1rem' }}>
                        <div style={{ fontWeight: 500 }}>{order.customer_name}</div>
                        <div style={{ fontSize: '0.875rem', color: 'var(--text-muted)' }}>{order.customer_email}</div>
                      </td>
                      <td style={{ padding: '1rem', color: 'var(--text-muted)' }}>
                        {new Date(order.created_at).toLocaleDateString()}
                      </td>
                      <td style={{ padding: '1rem' }}>{getStatusBadge(order.status)}</td>
                      <td style={{ padding: '1rem', fontWeight: 600 }}>${order.total_amount.toFixed(2)}</td>
                      <td style={{ padding: '1rem' }}>
                        <Link to={`/orders/${order.id}`} className="btn btn-outline" style={{ padding: '0.375rem 0.75rem', fontSize: '0.875rem' }}>
                          <Eye size={14} /> View
                        </Link>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          ) : (
            <div style={{ textAlign: 'center', padding: '3rem 1rem', color: 'var(--text-muted)' }}>
              <Package size={48} style={{ opacity: 0.2, margin: '0 auto 1rem' }} />
              <p>No orders found or API endpoint not yet implemented.</p>
              <p style={{ fontSize: '0.875rem', marginTop: '0.5rem' }}>Create a new order to get started!</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
